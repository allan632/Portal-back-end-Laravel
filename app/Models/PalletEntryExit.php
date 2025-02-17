<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class PalletEntryExit extends Model
{
    use HasFactory;

    protected $table = 'EntradaSaida';
    protected $primaryKey = 'IdDoc';
    public $timestamps = false; // Mantém created_at e updated_at
    protected $fillable = [
        "IdDoc",
        'NrDocumento',
        'NrSerie',
        'TpDoc',
        'TpOperacao',
        'CNPJRemetente',
        'CNPJDestinatario',
        'FilialRecebedoura',
        'NrFun',
        'created_at'
    ];

    public function user()
    {
        return $this->hasMany(User::class, 'NrFun'); 
    }

    public function qtdDoc()
    {
        return $this->hasMany(QtdDoc::class, 'IdDoc'); 
    }

    public function qtdPaleteDoc()
    {
        return $this->hasMany(QtdPaleteDoc::class, 'IdDoc');
    }

    public function ftDocAuxiliar()
    {
        return $this->hasMany(FtDocAuxiliar::class, 'IdDoc');
    }

    public function setDataRegistroAttribute($value)
{
    $this->attributes['created_at'] = Carbon::parse($value)->format('Y-m-d H:i:s');
}
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s', // Formato ISO para SQL Server
        // Outros campos...
    ];
}
