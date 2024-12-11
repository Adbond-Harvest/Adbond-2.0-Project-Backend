<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Client extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    public static $userType = "app\Models\Client";

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

    public function getFullNameAttribute()
    {
        $fullname = '';
        if($this->title && !empty($this->title)) $fullname .= $this->title.' ';
        if($this->firstname && !empty($this->firstname)) $fullname .= $this->firstname.' ';
        if($this->lastname && !empty($this->lastname)) $fullname .= $this->lastname.' ';
        if($this->othername && !empty($this->othername)) $fullname .= $this->othername.' ';
        return $fullname;
    }

    public function getNameAttribute()
    {
        $name = '';
        if($this->firstname && !empty($this->firstname)) $name .= $this->firstname.' ';
        if($this->lastname && !empty($this->lastname)) $name .= $this->lastname.' ';
        return $name;
    }

    public function photo()
    {
        return $this->belongsTo("app\Models\File");
    }

    public function referer()
    {
        return $this->belongsTo(User::class);
    }

    public function nextOfKins()
    {
        return $this->hasMany(ClientNextOfKin::class, "client_id", "id");
    }

    public function assets()
    {
        return $this->hasMany(ClientPackage::class, "client_id", "id");
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
