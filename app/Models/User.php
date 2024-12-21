<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
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
        'name',
        'email',
        'password',
        'user_status',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function user_profiles()
    {
        return $this->hasOne(UsersProfile::class, 'user_id');
    }

    public function user_profile()
    {
        return $this->hasOne(UserProfile::class, 'id');
    }

    public function upload()
    {
        return $this->hasOne(Upload::class, 'id');
    }

    // method for fetching users with filters
    public static function fetchUsersWithFilters($nameQuery = '', $emailQuery = '')
    {
        $users = self::query()
            ->where('user_status', 1)
            ->where('is_admin', 0)
            ->where('name', 'like', "%$nameQuery%")
            ->where('email', 'like', "%$emailQuery%");

        return $users;
    }
}
