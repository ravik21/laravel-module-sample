<?php

namespace Modules\User\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class UserToken extends Model
{
    protected $table = 'user_tokens';
    protected $fillable = ['user_id', 'access_token', 'expires_at'];

    public function getDates()
    {
        return ['created_at', 'updated_at', 'expires_at'];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
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
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'access_token';
    }
}
