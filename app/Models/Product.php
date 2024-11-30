<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Para gerar UUIDs

class Product extends Model
{


    protected $fillable = [
        'id_prod',
        'name_prod',
        'value_prod',
        'description',
        'stock',
    ];


    protected static function booted()
    {
        static::creating(function ($product) {
            if (!$product->id_prod) {
                $product->id_prod = (string) Str::uuid();
            }
        });
    }
}
