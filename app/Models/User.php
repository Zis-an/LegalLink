<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function client()
    {
        return $this->hasOne(Client::class);
    }

    public function lawyer()
    {
        return $this->hasOne(Lawyer::class);
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            // Delete Client Photo if exists
            if ($user->client) {
                if ($user->client->photo) {
                    Storage::disk('public')->delete($user->client->photo);
                }
                $user->client->delete(); // this will delete from DB
            }

            // Delete Lawyer Photo if exists
            if ($user->lawyer) {
                if ($user->lawyer->photo) {
                    Storage::disk('public')->delete($user->lawyer->photo);
                }
                $user->lawyer->delete();
            }
        });
    }

}
