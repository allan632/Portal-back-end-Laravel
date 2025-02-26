<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovPaleteSaldo extends Model {
    use HasFactory;
    protected $table = 'MovPaleteSaldo';
    public $timestamps = false; // Mantém created_at e updated_at

    protected $fillable = [
        'IdDocEntradaFisica','TpPalet','QtdPalete'
    ];
    public function palletEntryFisical()
    {
        return $this->belongsTo(MovPaleteQtdFisica::class, 'IdDoc'); 
    }
    public function viculoSaidaEntrada()
    {
        return $this->hasOne(MovVinculoNfSaidaEntrada::class, 'IdDocEntradaFisica'); 
    }
}