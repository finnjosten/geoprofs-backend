<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceReason extends Model
{
    use HasFactory;

    protected $fillable = [ 'slug', 'name', 'description', 'default' ];
}
