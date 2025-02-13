<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;

use App\Models\PalletEntryExit;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    public function Entry()
    {
        return $this->hasMany(PalletEntryExit::class, 'NrFun');
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    //configuração de jwt token
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

    //desabilita o timestamp de ir automatico
    public $timestamps = false; // Habilita timestamps


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

        //lista de atributos da tabela
    protected $fillable = [
        'NrFun',
        'IdFunWMS',
        'DsLogin',
        'password',
        'CdSideBar',
        'DsAbastTalaoEx',
        'DsAbastTalao',
        'remember_token',
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    //atrui
    protected $hidden = [
        'password'
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
    //Avisa a model sobre a relacao com profiles e quais sao a chaves
     public function profiles()
     {
         return $this->belongsToMany(Profile::class, 'profile_user', 'user_fk', 'profile_fk');
     }
}
