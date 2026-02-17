<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password_hash',
        'full_name',
        'role_id',
        'commission_rate',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function marketerRequests()
    {
        return $this->hasMany(MarketerRequest::class, 'marketer_id');
    }

    public function isMarketer(): bool
    {
        return $this->role_id === 3;
    }

    public function isWarehouse(): bool
    {
        return $this->role_id === 2;
    }

    public function isAdmin(): bool
    {
        return $this->role_id === 1;
    }

    public function isSales(): bool
    {
        return $this->role_id === 4;
    }
}
