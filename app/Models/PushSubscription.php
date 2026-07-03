<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = [
        'subscribable_id',
        'subscribable_type',
        'endpoint',
        'auth_key',
        'p256dh_key',
    ];

    public function subscribable()
    {
        return $this->morphTo();
    }
}
