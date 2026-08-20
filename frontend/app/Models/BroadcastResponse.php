<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BroadcastResponse extends Model
{
    use HasFactory;

    protected $table = 'broadcast_responses';

    protected $fillable = [
        'broadcast_id',
        'user_id',
        'response',
    ];

    public function broadcast()
    {
        return $this->belongsTo(Broadcast::class, 'broadcast_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
