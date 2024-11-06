<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Treatment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    protected $table = "SacEvento";
    protected $primaryKey = 'CdEvento'; 
    public $incrementing = true;
    protected $keyType = 'int'; 

    protected $fillable = [
        'CdEvento',
        'CdEmpresaRom',
        'CdRotaRom',
        'CdRomaneio',
        'CdDest',
        'CdFuncionario',
        'DtEvento',
        'InStatus',
    ];
    public $timestamps = false;

    public function SacTreatment(): BelongsTo  {
        return $this->belongsTo(Treatment::class, 'CdEvento');
    }

    // um evento tem várias tratativas
    // public function SacTreatment(): HasMany {
    //     return $this->hasMany(Treatment::class, 'CdEvento', 'CdEvento');
    // }
}