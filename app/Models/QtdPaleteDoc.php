<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class QtdPaleteDoc extends Model
{
    use HasFactory;

    protected $table = 'QtdPaleteDoc';
    protected $primaryKey = ['IdDoc','TpPalet'];
    public $timestamps = true; // Mantém created_at e updated_at

    protected $fillable = [
        'IdDoc',
        'TpPalet',
        'QtdPalete',
    ];

    public function palletEntryExit()
    {
        return $this->belongsTo(PalletEntryExit::class, 'IdDoc'); 
    }
    
}
