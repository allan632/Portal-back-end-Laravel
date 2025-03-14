<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovPaleteSaldoFilial extends Model {
    use HasFactory;
    protected $table = 'MovPaleteSaldoFilial';
    public $timestamps = false; // Mantém created_at e updated_at

    protected $fillable = [
        'IdFilial','PBR','CHEP','DsFilial','Descartavel','Cnpj'
    ];


}