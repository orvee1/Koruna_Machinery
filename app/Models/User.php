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
    protected $fillable = 
    ['name', 'email', 'phone', 'password', 'role','branch_id','worker_id','manager_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if ($user->role == 'worker' && !$user->worker_id) {
                $branchCode = optional($user->branch)->code;
                $lastWorker = self::where('role', 'worker')->where('branch_id', $user->branch_id)->latest()->first();
                $workerNumber = $lastWorker ? (int) substr($lastWorker->worker_id, 2) + 1 : 1;
                $user->worker_id = $branchCode . str_pad($workerNumber, 4, '0', STR_PAD_LEFT);
            }
            
            if ($user->role == 'manager' && !$user->manager_id) {
                // Generate manager_id dynamically based on branch code
                $branchCode = optional($user->branch)->code;
                $lastManager = self::where('role', 'manager')->where('branch_id', $user->branch_id)->latest()->first();
                $managerNumber = $lastManager ? (int) substr($lastManager->manager_id, 2) + 1 : 1;
                $user->manager_id = $branchCode . str_pad($managerNumber, 4, '0', STR_PAD_LEFT);
            }
        });
    }

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
        'password' => 'hashed',
    ];
}
