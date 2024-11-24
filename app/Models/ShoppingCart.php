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

    // Casts a coluna id_prod_q para array
    protected $casts = [
        'id_prod_q' => 'array', // Faz o cast para array automaticamente
    ];

    // Relacionamento com a tabela users
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
