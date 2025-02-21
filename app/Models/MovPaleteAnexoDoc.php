<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class MovPaleteAnexoDoc extends Model
{
    use HasFactory;

    protected $table = 'MovPaleteAnexoDoc';
    
    public $timestamps = false; // Mantém created_at e updated_at

    protected $fillable = [
        "IdDoc",
        "FotoDoc",
        "NrFun",
        "DataAtualizacao"

    ];

    public function palletEntryExit()
    {
        return $this->belongsTo(MovPalete::class, 'IdDoc'); 
    }

    public function getFromDateAttribute($value) {
        return \Carbon\Carbon::parse($value)->format('d-m-Y');
    }
    public function setDataRegistroAttribute($value)
    {
        $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
    }
    protected $casts = [
        'FotoDoc' => 'string', // Formato ISO para SQL Server
        'DataAtualizacao' => 'datetime:Y-m-d H:i:s', 
        // Outros campos...
    ];

}
