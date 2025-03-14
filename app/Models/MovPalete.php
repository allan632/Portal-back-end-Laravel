<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class MovPalete extends Model
{
    use HasFactory;

    protected $table = 'MovPalete';
    protected $primaryKey = 'IdDoc';
    public $timestamps = false; // Mantém created_at e updated_at
    protected $fillable = [
        "IdDoc",
        'NrDocumento',
        'NrSerie',
        'TpDoc',
        'TpOperacao',
        'CNPJRemetente',
        'DsRemetente',
        'DsDestinatario',
        'CNPJDestinatario',
        'FilialRecebedoura',
        'NrFun',
        'DataEmissaoDoc',
        'DataRegistro',
        'TpProc'
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'NrFun'); 
    }

    public function movPaleteQtdFisica()
    {
        return $this->hasMany(MovPaleteQtdFisica::class, 'IdDoc'); 
    }

    public function qtdPaleteDoc()
    {
        return $this->hasMany(MovPaleteQtdDoc::class, 'IdDoc');
    }

    public function movPaleteAnexoDoc()
    {
        return $this->hasMany(MovPaleteAnexoDoc::class, 'IdDoc');
    }


}
