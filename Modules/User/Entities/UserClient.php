<?php

namespace Modules\User\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class UserClient extends Model
{
    protected $table = 'user_clients';
    protected $fillable = ['user_id', 'client_id', 'intro_viewed', 'login_count', 'last_login_at'];

    public function getDates()
    {
        return ['created_at', 'updated_at', 'last_login_at'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isViewed()
    {
        return $this->intro_viewed ? true : false;
    }

    public function isFirstLogin()
    {
        return $this->login_count == 1;
    }
}
