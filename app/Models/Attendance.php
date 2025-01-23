<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['day_id', 'user_id', 'morning', 'afternoon', 'description', 'attendance_status', 'count_to_total'];

    public function day() {
        return $this->belongsTo(Day::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function morning() {
        return $this->belongsTo(AttendanceReason::class, 'attendance_reason');
    }

    public function afternoon() {
        return $this->belongsTo(AttendanceReason::class, 'attendance_reason');
    }

    public function status() {
        return $this->belongsTo(AttendanceStatus::class, 'attendance_status');
    }
}
