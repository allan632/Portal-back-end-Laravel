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
    
    // define a tabela que o laravel irá fazer a busca pelos dados
    protected $table = "UsuPor";

    /**
     * Atributos que são atribuidos para o WHERE na busca, não obrigatóriamente deve usar todos
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'NrFun',
        'DsLogin',
        'DsSenha',
        // 'DsAbastTalao',
        // 'NrFun',
        // 'IdFunWMS',
        // 'DsAbastTalaoEx',
        // 'DsNome',
        // 'DsGru',
        // 'CdGru',
        // 'CdEmpresa'
    ];

    /**
     * Os atributos que devem ser ocultados afim de evitar vazamento de conteúdo sensível.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'DsSenha',
    ];
}
