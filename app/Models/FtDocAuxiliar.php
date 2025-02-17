<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class FtDocAuxiliar extends Model
{
    use HasFactory;

    protected $table = 'FtDocAuxiliar';
    protected $primaryKey = ['IdDoc','DataAtualizacao'];
    public $timestamps = true; // Mantém created_at e updated_at

    protected $fillable = [
        "IdDoc",
        "FotoDoc",
        "NrFun",
        "dataAtualizacao"
    ];

    public function palletEntryExit()
    {
        return $this->belongsTo(PalletEntryExit::class, 'IdDoc'); 
    }

}
