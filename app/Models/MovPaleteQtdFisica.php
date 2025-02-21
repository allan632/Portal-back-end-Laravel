<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class MovPaleteQtdFisica extends Model
{
    use HasFactory;

    protected $table = 'MovPaleteQtdFisica';
    public $timestamps = false; // Mantém created_at e updated_at

    protected $fillable = [
        'IdDoc',
        'TpPalet',
        'QtdPalete',
    ];

    public function palletEntryExit()
    {
        return $this->belongsTo(MovPalete::class, 'IdDoc'); 
    }
    protected $cast = [
        'QtdPalete'=>"integer",
        'IdDoc' =>"integer",
        'TpPalet'=>"string"
    ];
    
}
