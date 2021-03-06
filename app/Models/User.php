<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_name',
        'email',
        'password',
        'verification_token',
        'avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var int[]
     */
    protected $attributes = [
        'role_id' => 2
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'registered_at' => 'datetime',
    ];

    public function setRegisteredAtAttribute()
    {
        $this->attributes['registered_at'] = Carbon::now();
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function role()
    {
        return $this->hasOne(UserRole::class);
    }

    public function getAvatarAttribute($value)
    {
        if(!empty($this->attributes['avatar'])) {
            $avatar = public_path('avatar'.DIRECTORY_SEPARATOR.$this->attributes['avatar']);
        } else {
            $avatar = "https://via.placeholder.com/256X256";
        }
        return $this->attributes['avatar'] = $avatar;
    }
}
