<?php

namespace Modules\User\Entities\Sentinel;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Authenticatable;

use Modules\User\Entities\UserToken;
use Modules\User\Entities\Favourite;
use Modules\User\Entities\UserInvite;
use Modules\User\Entities\UserClient;
use Modules\User\Entities\UserInterface;
use Modules\User\Entities\UserFile;
use Modules\User\Entities\File;
use Modules\User\Enums\UserFileTypeEnum;

use Modules\Group\Entities\Group;
use Modules\Taggable\Entities\Tag;
use Modules\Company\Entities\Company;
use Modules\User\Entities\ChatMessage;
use Modules\Booking\Entities\Booking;
use Modules\Company\Entities\Team as CompanyTeam;
use Modules\Company\Entities\Country as CompanyCountry;
use Modules\Company\Entities\Location as CompanyLocation;

use Modules\User\Observers\UserObserver;
use Modules\Avatarable\Traits\Avatarable;
use Laracasts\Presenter\PresentableTrait;
use Cartalyst\Sentinel\Users\EloquentUser;
use Modules\User\Enums\ApprovalStatusEnum;
use Modules\User\Presenters\UserPresenter;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Cashier\Billable;
use Modules\Rateable\Traits\Rateable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends EloquentUser implements UserInterface, AuthenticatableContract
{
    use PresentableTrait, Authenticatable, Avatarable, Billable, Rateable, SoftDeletes;

    protected $fillable = [
        'email',
        'password',
        'permissions',
        'gender',
        'title',
        'first_name',
        'last_name',
        'suspended',
        'marketing',
        'is_urgent',
        'approval_status',
        'digest_sent',
        'online_status',
        'company_name',
        'parent_company_name',
        'company_number',
        'company_town',
        'company_region',
        'company_street',
        'company_postcode',
        'company_country_id',
        'company_vat_registered',
        'company_vat_no',
        'company_phone_contact',
        'company_position',
        'timezone',
        'hour_rate',
        'day_rate',
        'languages',
        'availability',
        'professional_experience',
        'past_projects',
        'education',
        'memberships',
        'references',
        'deleted_at'
    ];

    public $avatarableMap = [
        'first_name' => 'first_name',
        'last_name' => 'last_name'
    ];

    /**
     * {@inheritDoc}
     */
    protected $loginNames = ['email'];
    protected $appends = ['fullName', 'fullAddress'];

    protected $presenter = UserPresenter::class;

    public function getFullNameAttribute()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getFullAddressAttribute()
    {
        return ($this->company_postcode ? $this->company_postcode . ', ' : '') . $this->companyCountry()->first()->name;
    }

    public function __construct(array $attributes = [])
    {
        $this->loginNames = config('user.config.login-columns');

        $this->fillable = config('user.config.fillable');

        if (config()->has('user.config.presenter')) {
            $this->presenter = config('user.config.presenter', UserPresenter::class);
        }
        if (config()->has('user.config.dates')) {
            $this->dates = config('user.config.dates', []);
        }
        if (config()->has('user.config.casts')) {
            $this->casts = config('user.config.casts', []);
        }

        parent::__construct($attributes);
    }

        /**
     * Boot Method
     *
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function($user) {
            $user->activations()->delete();
            $user->apiTokens()->delete();
            $user->companies()->detach();
            $user->companyLocations()->detach();
            $user->companyTeams()->detach();
            $user->clients()->delete();
            $user->groups()->detach();
            $user->invites()->delete();
            $user->roles()->delete();
            $user->tags()->detach();
        });
    }

    /**
     * @inheritdoc
     */
    public function hasRoleId($roleId)
    {
        return $this->roles()->whereId($roleId)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function hasRoleSlug($slug)
    {
        return $this->roles()->whereSlug($slug)->count();
    }

    /**
     * @inheritdoc
     */
    public function hasRoleName($name)
    {
        return $this->roles()->whereName($name)->count() >= 1;
    }

    /**
     * @inheritdoc
     */
    public function isActivated()
    {
        if (Activation::completed($this)) {
            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function isInvited()
    {
        $invites = Activation::where('user_id', $this->id)->where('is_invite', 1)->get();

        if($invites->count())
          return true;

        return false;
    }
    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function apiTokens()
    {
        return $this->hasMany(UserToken::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clients()
    {
        return $this->hasMany(UserClient::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invites()
    {
        return $this->hasMany(UserInvite::class, 'inviter_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function companies()
    {
        return $this->belongsToMany(Company::class, 'company__company_users', 'user_id', 'company_id')->withPivot('company_code')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function companyCountry()
    {
        return $this->belongsTo(CompanyCountry::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function companyLocations()
    {
        return $this->belongsToMany(CompanyLocation::class, 'company__location_users', 'user_id', 'location_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function companyTeams()
    {
        return $this->belongsToMany(CompanyTeam::class, 'company__team_users', 'user_id', 'team_id')->withPivot('is_leader')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group__group_users', 'user_id', 'group_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function openGroups()
    {
        return $this->groups()->IsOpen();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'tag__tag_users', 'user_id', 'tag_id')->withPivot('enabled')->withTimestamps();
    }

    public function terms($type = 'TERMS_CONDITIONS')
    {
        return $this->belongsToMany(File::class, 'user_files', 'user_id', 'file_id')->withPivot('file_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function files()
    {
        return $this->hasMany(UserFile::class);
    }

    /**
     * @inheritdoc
     */
    public function getFirstApiToken()
    {
        $userToken = $this->apiTokens->first();

        if ($userToken === null) {
            return '';
        }

        return $userToken->access_token;
    }

    /**
     * @inheritdoc
     */
    public function getLastApiToken()
    {
        $userToken = $this->apiTokens->last();

        if ($userToken === null) {
            return '';
        }

        return $userToken->access_token;
    }

    public function hasLoggedIntoClient($clientId = null)
    {
        $client = $this->clients->where('client_id', $clientId)->first();

        return $client && $client->login_count > 0;
    }

    public function lastLoggedIntoClient($clientId = null)
    {
        $client = $this->clients->where('client_id', $clientId)->first();

        return ($client && $client->last_login_at) ? date('d/m/Y', strtotime($client->last_login_at)) : 'Not logged in yet.';
    }

    public function clientIntroViewed($clientId = null)
    {
        if ($clientId) {
            $client = $this->clients->where('client_id', $clientId)->first();

            if($client) {
              $viewed = $client->isViewed();

              if (!$viewed) {
                $client->intro_viewed = true;
                $client->save();
              }

              return $viewed;
            }
        }

        return true;
    }

    public function __call($method, $parameters)
    {
        #i: Convert array to dot notation
        $config = implode('.', ['user.config.relations', $method]);

        #i: Relation method resolver
        if (config()->has($config)) {
            $function = config()->get($config);

            return $function($this);
        }

        #i: No relation found, return the call to parent (Eloquent) to handle it.
        return parent::__call($method, $parameters);
    }

    /**
     * @inheritdoc
     */
    public function hasAccess($permission)
    {
        $permissions = $this->getPermissionsInstance();

        return $permissions->hasAccess($permission);
    }

    /**
     * @inheritdoc
     */
    public function suspend()
    {
        $this->suspended = true;
        $this->save();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function unsuspend()
    {
        $this->suspended = false;
        $this->save();

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function isSuspended()
    {
        return $this->suspended;
    }

    /**
     * @inheritdoc
     */
    public function passwordIs($password)
    {
        return Hash::check($password, $this->password);
    }

    public function getStatus()
    {
        $status = [];

        // APPROVED
        // REJECTED
        // ACCEPTED
        // ACTIVATED
        // NOT ACTIVATED
        // SUSPENDED

        if ($this->isActivated() && !$this->hasRoleSlug('trader') && !$this->hasRoleSlug('expert')) {
            $status['name'] = 'Active';
            $status['class'] = 'success';
            $status['value'] = ApprovalStatusEnum::ACCEPTED;
        } else if ($this->isActivated() && $this->approvalIsAccepted() && !$this->isSuspended()) {
            $status['name'] = 'Active';
            $status['class'] = 'success';
            $status['value'] = ApprovalStatusEnum::ACCEPTED;
        } else if ($this->approvalIsRejected() && !$this->isSuspended()) {
            $status['name'] = 'Rejected';
            $status['class'] = 'danger';
            $status['value'] = ApprovalStatusEnum::REJECTED;
        }
        else if ($this->isSuspended()){
          $status['name'] = 'Suspended';
          $status['class'] = 'danger';
          $status['value'] = ApprovalStatusEnum::SUSPENDED;
        }
        else if ($this->approvalIsPending()) {
            if(!$this->hasRoleSlug('trader') && !$this->hasRoleSlug('expert')) {
              $status['name'] = 'Not Activated';
            } else {
              $status['name'] = 'Pending';
              if (!$this->isActivated()) {
                $status['name'] .= ' (Not Activated)';
              }
            }
            $status['class'] = 'warning';
            $status['value'] = ApprovalStatusEnum::PENDING;
        }
        else {
            $status['name']  = 'Not Activated';
            $status['class'] = 'warning';
            $status['value'] = ApprovalStatusEnum::PENDING;
        }

        return $status;
    }

    public function setApprovalStatus($status)
    {
        $this->approval_status = $status;
        $this->save();
    }

    public function approvalIsAccepted()
    {
        return $this->approval_status == ApprovalStatusEnum::ACCEPTED;
    }

    public function approvalIsRejected()
    {
        return $this->approval_status == ApprovalStatusEnum::REJECTED;
    }

    public function approvalIsPending()
    {
        return $this->approval_status == ApprovalStatusEnum::PENDING;
    }

    public function approvalStatus()
    {
        return ApprovalStatusEnum::getApprovalStatus($this->approval_status);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function favourites()
    {
        return $this->hasMany(Favourite::class, 'user_id', 'id');
    }

    public function scopetermsConditions($query, $type = 'TERMS_CONDITIONS')
    {
        return File::select('files.*')
                    ->join('user_files', 'files.id', 'user_files.file_id')
                    ->join('users', 'user_files.user_id', 'users.id')
                    ->where('users.id', $this->id)
                    ->where('user_files.type', UserFileTypeEnum::getType($type));
    }

    public function secondaryPermissions()
    {
        $permissionsInstance  = $this->getPermissionsInstance();
        $secondaryPermissions = $permissionsInstance->getSecondaryPermissions();

        return $secondaryPermissions;
    }

    public function scopeExceptActiveUser($query, $activeUserId)
    {
        return $query->where('id', '!=', $activeUserId);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
    public function message()
    {
        return $this->hasOne(ChatMessage::class);
    }

    public function address()
    {
      $address  = '';
      $address .= $this->company_street ? $this->company_street . ', ' : '';
      $address .= $this->company_town ? $this->company_town . ', ' : '';
      $address .= $this->company_region ? $this->company_region . ', ' : '';
      $address .= $this->company_postcode;

      $address = str_replace(', ,', '', $address);

      return $address;
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'vendor_id', 'id');
    }
}
