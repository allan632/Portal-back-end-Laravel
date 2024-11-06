<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SacNfEvolution;

class SacCadEvolution extends Model
{
    use HasFactory;
    protected $table = "SacCadEvolucao";
    protected $primaryKey = 'CdEvolucao'; 
    public $incrementing = true;
    protected $keyType = 'int'; 

    protected $fillable = [
        'CdEvolucao', // FK
        'DsEvolucao',
        'HrAlerta',
        'CdFuncionario',
        'InAtivo',
        'DtCadastro',
        'InFinaliza',
    ];
    public $timestamps = false;

    public function events(): HasMany {
        return $this->hasMany(SacNfEvolution::class, 'CdEvolucao');
    }
}
