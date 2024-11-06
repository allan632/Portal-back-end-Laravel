<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SacTratOccurrence extends Model
{
    use HasFactory;

    protected $table = "SacTratOcorrencia";
    protected $primaryKey = 'CdTratOcorrencia'; 
    public $incrementing = false;
    protected $keyType = 'int'; 

    protected $fillable = [
        'CdTratOcorrencia',
        'CdTratativa',
        'CdOcorrencia',
        'CdFuncionario',
        'DtProblema',
    ];
    public $timestamps = false;

    // Relação many to one com sacCadOccurence: um tratamento tem várias ocorrências
    public function treatment(): BelongsTo  {
        return $this->belongsTo(Treatment::class,'CdTratativa');
    }
}
