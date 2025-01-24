<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use \Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'role_slug',
        'deparment_slug',
        'subdeparment_slug',
        'suporvisor_id',

        'verified',
        'blocked',

        'first_name',
        'sure_name',
        'bsn',
        'date_of_service',

        'used_attendance',
        'max_attendance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isVerified() {
        return Auth::user()->verified;
    }

    public function isBlocked() {
        return Auth::user()->blocked;
    }

    public function isAdmin() {
        return Auth::user()->role_slug == 'admin';
    }

    public function isEmployee() {
        return Auth::user()->role_slug == 'employee';
    }

    public function supervisor() {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function sendVerifyEmail() {
        $this->verified = true;
        $this->save();
        return;
    }

    public function attendance() {
        return $this->hasMany(Attendance::class);
    }
}
