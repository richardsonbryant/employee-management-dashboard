<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UserAttendance extends Model
{

    protected $table = 'user_attendance';

    use HasFactory;

    protected $fillable = ['email', 'clock_in', 'clock_out', 'total_hours', 'verification'];

    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    public function calculateTotalHours()
    {
        if ($this->clock_in && $this->clock_out) {
            return Carbon::parse($this->clock_in)->diffInHours(Carbon::parse($this->clock_out));
        }
        return null;
    }
}
