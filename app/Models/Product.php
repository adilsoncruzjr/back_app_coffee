<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Para gerar UUIDs

class Product extends Model
{
    // use HasFactory;

    protected $fillable = [
        'id_prod',
        'name_prod',
        'value_prod',
        'description',
        'stock',
    ];

    // Gerar 'id_prod' automaticamente como UUID
    protected static function booted()
    {
        static::creating(function ($product) {
            if (!$product->id_prod) {
                $product->id_prod = (string) Str::uuid(); // Gerar UUID para 'id_prod'
            }
        });
    }
}
