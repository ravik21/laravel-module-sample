<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\User\Entities\Sentinel\User;

class ChatMessage extends Model
{
    protected $appends = ['selfMessage'];
    protected $table   = 'chat_messages';
    protected $fillable = ['user_id', 'chatable_type', 'chatable_id', 'body', 'payload', 'notified'];

    public function user()
    {
       return $this->belongsTo(User::class);
    }

    public function getSelfMessageAttribute()
    {
        return $this->user_id === auth()->user()->id;
    }

    public function sender()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function receiver()
    {
        return $this->hasOne(User::class, 'id', 'chatable_id');
    }

    public function scopeReceivedFromUsers($query, $meta)
    {
        return $query->where('chatable_id', $meta['user_id']);
    }

    public function scopeSentToUsers($query, $meta)
    {
        return $query->where('user_id', $meta['user_id']);
    }
}
