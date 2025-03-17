<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovPaleteVinculoEntradaSaldoSaida extends Model {
    protected $table = 'MovPaleteVinculoEntradaSaldoSaida';
    public $incrementing = false; // Indica que a chave primária não é auto-incremento
    public $timestamps = false; // Se não houver `created_at` e `updated_at`


    protected $fillable = ['IdDocSaida', 'IdDocEntradaFisica', 'TpPaletEntrada','QtdPaleteSaldo'];

    // Relacionamento com MovPalete
    public function movPalete() {
        return $this->belongsTo(MovPalete::class, 'id_doc', 'id_doc');
    }
    public function palletEntryFisical()
    {
        return $this->belongsTo(MovPaleteQtdDoc::class, 'IdDoc'); 
    }
    public function viculoSaidaEntrada()
    {
        return $this->belongsTo(MovPaleteSaldo::class, 'IdDocEntradaFisica'); 
    }
}

