<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceStatus extends Model
{
    use HasFactory;

    protected $fillable = [ 'slug', 'name', 'description', 'show_in_agenda', 'default' ];
}
