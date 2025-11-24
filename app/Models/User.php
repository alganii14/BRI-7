<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
        'password_changed_at',
        'photo',
        'role',
        'rmft_id',
        'pernr',
        'kode_kanca',
        'nama_kanca',
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
        'password_changed_at' => 'datetime',
    ];
    
    /**
     * Check if user is manager
     */
    public function isManager()
    {
        return $this->role === 'manager';
    }
    
    /**
     * Check if user is RMFT
     */
    public function isRMFT()
    {
        return $this->role === 'rmft';
    }
    
    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    
    /**
     * Get RMFT data relation
     */
    public function rmftData()
    {
        return $this->belongsTo(RMFT::class, 'rmft_id');
    }
    
    /**
     * Check if user needs to change password
     */
    public function needsPasswordChange()
    {
        // Hanya untuk manager dan rmft
        if (!in_array($this->role, ['manager', 'rmft'])) {
            return false;
        }
        
        // Jika password_changed_at null, berarti belum pernah ganti password
        return is_null($this->password_changed_at);
    }
}
