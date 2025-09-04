<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;
    
    protected $table = 'admin_users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if admin has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if admin has any of the given roles
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        return in_array($this->role, $roles);
    }

    /**
     * Check if admin is super admin
     */
    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if admin is regular admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if admin is manager
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }

    /**
     * Check if admin can access admin panel
     */
    public function canAccessAdmin()
    {
        return $this->is_active && in_array($this->role, ['super_admin', 'admin', 'manager']);
    }

    /**
     * Check if admin can manage settings
     */
    public function canManageSettings()
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Check if admin can manage other admin users
     */
    public function canManageAdmins()
    {
        return $this->role === 'super_admin';
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute()
    {
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'manager' => 'Manager',
            default => ucfirst($this->role)
        };
    }

    /**
     * Scope for active admins
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific role
     */
    public function scopeRole($query, $role)
    {
        return $query->where('role', $role);
    }
}