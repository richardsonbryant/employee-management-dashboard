<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{

    protected $table = 'broadcasts';

    protected $fillable = [
        'start_off_date',
        'end_off_date',
        'total_off_day',
        'message',
    ];

    public function responses()
    {
        return $this->hasMany(BroadcastResponse::class, 'broadcast_id');
    }
}
