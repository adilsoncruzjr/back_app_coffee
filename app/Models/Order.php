<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    // Nome da tabela
    protected $table = 'orders';


    // Atributos que podem ser preenchidos
    protected $fillable = [
        'id_user',
        'id_prod',
        'final_value',
        'status',
        'id_car'      
    ];

    // Especificando que a coluna 'id_prod' é do tipo JSON
    protected $casts = [
        'id_prod' => 'array', // O Laravel vai automaticamente tratar como um array
    ];

    public $timestamps = false;

    // Relacionamento: Uma ordem pertence a um usuário
    public function user()
{
    return $this->belongsTo(User::class, 'id_user');
}

    // Relacionamento: Uma ordem pode estar vinculada a um carrinho
    public function cart()
    {
        return $this->belongsTo(ShoppingCart::class, 'id_cart');
    }
}
