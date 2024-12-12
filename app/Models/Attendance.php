<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['day_id', 'user_id', 'morning', 'afternoon'];

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
