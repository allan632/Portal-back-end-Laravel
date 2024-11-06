<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SacNfEvolution;

class SacTratOccurrenceNf extends Model
{
    use HasFactory;

    protected $table = "SacTratOcorrenciaNf";
    protected $primaryKey = 'CdTratOcorrenciaNf'; 
    public $incrementing = true;
    protected $keyType = 'int'; 

    protected $fillable = [
        'CdTratOcorrenciaNf', //NF
        'CdTratOcorrencia',
        'NrSerie',
        'NrNotaFiscal',
    ];
    public $timestamps = false;
    
    public function nfEvolution(): HasMany {
        return $this->hasMany(SacNfEvolution::class, 'CdTratOcorrenciaNf', 'CdTratOcorrenciaNf');
    }

}
