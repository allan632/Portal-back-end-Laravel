<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'access_level',
        'updated_at',
        'created_at',

    ];
    //Avisa a model sobre a relacao com users e quais sao a chaves
    public function users()
    {
        return $this->belongsToMany(User::class, 'profile_user', 'profile_fk', 'user_fk');
    }
      //desabilita o timestamp de ir automatico
      public $timestamps = false; // Habilita timestamps
    protected $casts = [
        'access_level'=>"string"


    ];
}
