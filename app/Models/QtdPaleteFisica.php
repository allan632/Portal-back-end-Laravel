<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class QtdPaleteFisica extends Model
{
    use HasFactory;

    protected $table = 'QtdFisica';
    public $timestamps = false; // Mantém created_at e updated_at

    protected $fillable = [
        'IdDoc',
        'TpPalet',
        'QtdPalete',
    ];

    public function palletEntryExit()
    {
        return $this->belongsTo(PalletEntryExit::class, 'IdDoc'); 
    }
    protected $cast = [
        'QtdPalete'=>"integer",
        'IdDoc' =>"integer",
        'TpPalet'=>"string"
    ];
    
}
