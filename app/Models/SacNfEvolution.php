<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\SacTratOccurrenceNf;
use App\Models\SacCadEvolution;

class SacNfEvolution extends Model
{
    use HasFactory;

    protected $table = "SacNfEvolucao";
    protected $primaryKey = 'CdTratOcorrenciaNf'; 
    public $incrementing = true;
    protected $keyType = 'int'; 
    public $timestamps = false;

    protected $fillable = [
        'CdTratOcorrenciaNf', // FK sacTratOcorrenciaNf
        'CdEvolucao', // FK sacCadEvolucao
        'CdFuncionario',
        'DsObs',
        'DtEvolucao',
    ];
    
    public function tratOccurrenceNf(): BelongsTo  {
        return $this->belongsTo(SacTratOccurrenceNf::class, 'CdTratOcorrenciaNf', 'CdTratOcorrenciaNf');
    }
    public function sacNfEvolutions(): BelongsTo  {
        return $this->belongsTo(SacCadEvolution::class, 'CdEvolucao');
    }
}
