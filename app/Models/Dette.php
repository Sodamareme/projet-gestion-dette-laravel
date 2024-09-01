<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'montantDu',
        'montantRestant',
        'idClient',
    ];

    // Si vous utilisez des relations, définissez-les ici
    public function client()
    {
        return $this->belongsTo(Client::class, 'idClient');
    }
}
