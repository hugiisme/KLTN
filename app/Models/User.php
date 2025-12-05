<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    // @use HasFactory<\Database\Factories\UserFactory>
    use HasFactory, Notifiable;
    protected $table = 'users';

    //  The attributes that are mass assignable.
    //  @var list<string>

    protected $fillable = [
        'username',
        'password_hash',
        'user_type_id',
        'status',
    ];

    // The attributes that should be hidden for serialization.
    // @var list<string>

    protected $hidden = [
        'password_hash',
    ];

    // Get the attributes that should be cast.
    // @return array<string, string>

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'user_orgs', 'user_id', 'org_id');
    }

    // public function getAuthIdentifierName()
    // {
    //     return 'username';
    // }

    public function type()
    {
        return $this->belongsTo(UserType::class, 'user_type_id');
    }
}
