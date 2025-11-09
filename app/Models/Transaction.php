<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Transaction extends Model
{
    use HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'compte_id',
        'type',
        'montant',
        'status',
        'description',
        'reference',
    ];

    public function compte()
    {
        return $this->belongsTo(Compte::class, 'compte_id', 'id');
    }
}
