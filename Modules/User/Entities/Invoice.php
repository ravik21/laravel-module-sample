<?php

namespace Modules\User\Entities;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['user_id', 'stripe_invoice_id', 'data', 'path'];
    protected $table = 'invoices';

    public function user()
    {
        return $this->belongsTo(\Modules\User\Entities\Sentinel\User::class, 'user_id', 'id');
    }
}
