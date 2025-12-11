<?php


namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

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

    /**
     * Role Helper Methods for RBAC
     */
    public function isStafSCM(): bool
    {
        return $this->role === 'staf_scm';
    }

    public function isPICSales(): bool
    {
        return $this->role === 'pic_sales';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Relationships
     */
    public function customers()
    {
        return $this->hasMany(Customer::class, 'pic_user_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'created_by_user_id');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
