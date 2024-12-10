<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'week_id'];

    public function week()
    {
        return $this->belongsTo(Week::class);
    }

    public function attendance()
    {
        return $this->hasMany(Attendance::class);
    }
}
