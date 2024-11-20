<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShoppingCart extends Model
{
    use HasFactory;

    protected $table = 'shopping_cart';  // Tabela correta

    // Especifica as colunas que podem ser preenchidas em massa
    protected $fillable = [
        'id_user',
        'id_prod_q',
        'final_value_car',
    ];

    // Relacionamento com a tabela users
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Relacionamento com a tabela products
    public function product()
    {
        return $this->belongsTo(Product::class, 'id_prod_q');
    }
}
