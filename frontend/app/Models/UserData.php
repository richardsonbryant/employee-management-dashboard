<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model
{
    use HasFactory;

    protected $table = 'user_data';
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function responses()
    {
        return $this->hasMany(BroadcastResponse::class, 'user_id');
    }

    protected $dates = ['start_off_date', 'end_off_date'];



    protected $fillable = [
        'new_name',
        'email',
        'start_off_date',
        'end_off_date',
        'total_off_day',
        'reason',
        'approval_status',
        'request_type'
    ];
}
