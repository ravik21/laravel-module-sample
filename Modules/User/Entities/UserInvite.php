<?php

namespace Modules\User\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class UserInvite extends Model
{
    protected $table = 'user_invites';

    protected $fillable = [
        'inviter_id',
        'email',
        'message',
        'invite_count',
        'code',
        'completed',
        'completed_at',
        'expires_at',
    ];

    public function getDates()
    {
        return ['completed_at', 'expires_at', 'created_at', 'updated_at'];
    }

    /**
     * Get mutator for the "completed" attribute.
     *
     * @param  mixed  $completed
     * @return bool
     */
    public function getCompletedAttribute($completed)
    {
        return (bool) $completed;
    }

    /**
     * Set mutator for the "completed" attribute.
     *
     * @param  mixed  $completed
     * @return void
     */
    public function setCompletedAttribute($completed)
    {
        $this->attributes['completed'] = (bool) $completed;
    }

    /**
     * {@inheritDoc}
     */
    public function getCode()
    {
        return $this->attributes['code'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }

    /**
     * Scope a query for unexpired
     *
     * @var array
     */
    public function scopeUnexpired($query)
    {
        $query->where('expires_at', '>', Carbon::now());
    }

    /**
     * Scope a query for expired
     *
     * @var array
     */
    public function scopeExpired($query)
    {
        $query->where('expires_at', '<=', Carbon::now());
    }

    public function markAsComplete()
    {
        $this->completed = true;
        $this->completed_at = Carbon::now();

        $this->save();

        return $this;
    }
}
