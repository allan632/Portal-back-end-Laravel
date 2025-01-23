<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Carbon;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $primaryKey = 'NrFun'; 
    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey(); // Substitua por seu campo de identificação
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public $timestamps = false; // Habilita timestamps
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'DsLogin',
        'DsSenha',
        'IdFunWMS',
        'CdSideBar',
        'updated_at',
        'created_at',
        'remember_token',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'DsSenha',
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
        'DsSenha'=> 'hashed',


    ];

        /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
}
