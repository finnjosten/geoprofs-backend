<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Week extends Model
{
    use HasFactory;

    protected $fillable = ['week_number', 'year_id'];

    public function days()
    {
        return $this->hasMany(Day::class);
    }

    public function year()
    {
        return $this->belongsTo(Year::class);
    }
}
