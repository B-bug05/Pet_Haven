<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Application;
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function applications()
    {
        // Removing the leading slash since you are already in the App\Models namespace
        return $this->hasMany(Application::class);
    }

    // The pets this user has favorited
    public function favorites()
    {
        return $this->belongsToMany(Pet::class, 'favorites');
    }
    // Inside app/Models/User.php

    public function isStaff()
    {
        // Admins usually get staff privileges too!
        return $this->role === 'staff' || $this->role === 'admin';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAdopter()
    {
        return $this->role === 'adopter';
    }
}
