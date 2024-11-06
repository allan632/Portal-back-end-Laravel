<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SacTratOccurrence;
use App\Models\Event;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Treatment extends Model
{
    use HasFactory;

    protected $table = "SacEventoTrat";
    protected $primaryKey = 'CdTratativa'; 
    public $incrementing = true;
    protected $keyType = 'int'; 

    protected $fillable = [
        'CdTratativa',
        'CdEvento',
        'CdRemetente',
        'CdFuncionario',
        // 'CdTratativa',
        'InStatus',
        'DtTratativa'
    ];
    public $timestamps = false;

    // uma tratativa tem um evento
    public function SacEvent(): BelongsTo  {
        return $this->belongsTo(Event::class, 'CdEvento', 'CdEvento');
    }
    
    // public function events(): HasMany {
    //     return $this->hasMany(Event::class, 'CdEvento');
    // }
      public function occurrences(): HasMany {
        return $this->hasMany(SacTratOccurrence::class, 'CdTratativa');
    }
}
