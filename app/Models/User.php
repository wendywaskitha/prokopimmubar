<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable 
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'bio',
        'position',
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

    /**
     * Define the roles available in the application
     */
    public const ROLES = [
        'super_admin' => 'Super Admin',
        'editor' => 'Editor',
        'penulis' => 'Penulis',
        'kontributor' => 'Kontributor',
    ];

    /**
     * Check if user has a specific role (custom implementation)
     */
    public function hasCustomRole($role)
    {
        return $this->hasRole($role);
    }

    /**
     * Check if user is super admin
     */
    public function isSuperAdmin()
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is editor
     */
    public function isEditor(): bool
    {
        return $this->hasRole('editor');
    }

    /**
     * Check if user is penulis
     */
    public function isPenulis()
    {
        return $this->hasRole('penulis');
    }

    /**
     * Check if user is kontributor
     */
    public function isKontributor()
    {
        return $this->hasRole('kontributor');
    }

    /**
     * Get the user's avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Return default avatar if none is set
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
    }
}
