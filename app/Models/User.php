<?php

namespace app\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
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
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier() {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims() {
        return [];
    }

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

    public static $userType = "app\Models\User";

    public function getNameAttribute()
    {
        $fullname = '';
        if($this->firstname && !empty($this->firstname)) $fullname .= $this->firstname.' ';
        if($this->lastname && !empty($this->lastname)) $fullname .= $this->lastname.' ';
        return $fullname;
    }

    public function staffType()
    {
        return $this->belongsTo("app\Models\StaffType");
    }

    public function role()
    {
        return $this->belongsTo("app\Models\Role");
    }

    public function photo()
    {
        return $this->belongsTo("app\Models\File");
    }

    public function registerer()
    {
        return $this->belongsTo("app\Models\User", "registered_by", "id");
    }

    /**
     * Get all commission earnings for the user
     */
    public function commissionEarnings()
    {
        return $this->hasMany(StaffCommissionEarning::class);
    }

    /**
     * Get all commission redemptions for the user
     */
    public function commissionRedemptions()
    {
        return $this->hasMany(StaffCommissionRedemption::class);
    }

    /**
     * Get all commission transactions for the user
     */
    public function commissionTransactions()
    {
        return $this->hasMany(StaffCommissionTransaction::class);
    }
}
