<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens; // Import HasApiTokens


class User extends Authenticatable
{
    use Notifiable, HasRoles, HasApiTokens; // Make sure this line is included

    protected $fillable = ['name', 'email', 'password', 'company_id'];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    public function companies()
    {
        return $this->belongsToMany(Company::class);
    }
}

