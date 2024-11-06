<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\SacTratOccurrence;

class SacCadOccurrence extends Model
{
    use HasFactory;

    protected $table = "SacCadOcorrencia";
    protected $primaryKey = 'CdOcorrencia'; 
    public $incrementing = true;
    protected $keyType = 'int'; 

    protected $fillable = [
        'CdOcorrencia',
        'DsOcorrencia',
        'NrUsuario',
        'InAtivo',
        'DtCadastro',
    ];
    public $timestamps = false;

    // Relação one to many com SacTratOccurrence
    public function tratOccurrence(): HasMany {
        return $this->hasMany(SacTratOccurrence::class, 'CdOcorrencia', 'CdTratativa');
    }
}
