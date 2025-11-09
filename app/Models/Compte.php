<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;

class Compte extends Model
{
    /** @use HasFactory<\Database\Factories\CompteFactory> */
    use HasFactory,HasUuids, SoftDeletes;
    
    protected $keyType = 'string';
    public $incrementing = false;

       protected $fillable = [
        'id',
        'numero_compte',
        'user_id',
        'titulaire',
        'type',
        'devise',
        'statut',
        'derniere_modification',
        'version',
        'code_expire_at',
        'is_archived'
    ];

    protected function numeroCompte(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value,
            set: fn ($value) => $value ?: 'ACC-' . strtoupper(Str::random(10))
        );
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($compte) {
            if (!$compte->numero_compte) {
                $compte->numero_compte = 'ACC-' . strtoupper(Str::random(10));
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'compte_id', 'id');
    }


    public function scopeFiltrerComptes(Builder $query, $filters = [], $user = null)
{
    $isAdmin = $user && method_exists($user, 'isAdmin') ? $user->isAdmin() : false;

    $query->whereIn('type', ['epargne', 'cheque'])
          ->where('statut', 'actif')
          ->where('is_archived', false); // Exclure les comptes archivés

    if (!$isAdmin && $user) {
        $query->where('user_id', $user->id);
    }

    if (!empty($filters['type'])) {
        $query->where('type', $filters['type']);
    }

    if (!empty($filters['statut'])) {
        $query->where('statut', $filters['statut']);
    }

    if(!empty($filters['numero_compte'])) {
        $query->where('numero_compte', $filters['numero_compte']);
    }

    if (!empty($filters['search'])) {
        $search = $filters['search'];
        $query->where(function ($q) use ($search) {
            $q->where('titulaire', 'like', "%{$search}%")
              ->orWhere('numero_compte', 'like', "%{$search}%");
        });
    }

    $sortField = match ($filters['sort'] ?? null) {
        'dateCreation' => 'created_at',
        'solde' => 'solde',
        'titulaire' => 'titulaire',
        default => 'created_at',
    };

    $order = in_array($filters['order'] ?? '', ['asc', 'desc'])
        ? $filters['order']
        : 'desc';

    $query->orderBy($sortField, $order);

    $query->withSum(['transactions as depot_sum' => fn($q) =>
        $q->where('type', 'depot')->where('status', 'validee')
    ], 'montant')
    ->withSum(['transactions as retrait_sum' => fn($q) =>
        $q->where('type', 'retrait')->where('status', 'validee')
    ], 'montant');

    return $query;
}

    public function scopeArchived(Builder $query)
    {
        return $query->where('is_archived', true);
    }

    public function scopeNotArchived(Builder $query)
    {
        return $query->where('is_archived', false);
    }
    
}
