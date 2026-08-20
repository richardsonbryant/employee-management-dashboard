<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionData extends Model
{
    //
    use HasFactory;

    protected $table = 'user_sick_data';

    protected $dates = ['start_off_date', 'end_off_date'];
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    protected $fillable = [
        'new_name',
        'email',
        'start_off_date',
        'end_off_date',
        'total_off_day',
        'reason',
        'approval_status',
        'permission_letter',
        'has_doctor_letter',
        'request_type'
    ];
}
