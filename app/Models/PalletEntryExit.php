<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PalletEntryExit extends Model
{
    use HasFactory;

    protected $table = 'EntradaSaida';
    protected $primaryKey = 'IdDoc';
    public $timestamps = true; // Mantém created_at e updated_at

    protected $fillable = [
        'NrDocumento',
        'NrSerie',
        'TpDoc',
        'TpOperacao',
        'CNPJRemetente',
        'CNPJDestinatario',
        'FilialRecebedoura',
        'DataRegistro',
        'NrFun'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'NrFun'); 
    }
    
}
