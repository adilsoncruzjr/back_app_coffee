<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shopping_cart', function (Blueprint $table) {
            $table->id(); // Auto-increment para 'id'
            $table->unsignedBigInteger('id_user'); // ID do usuário
            $table->unsignedBigInteger('id_prod_q'); // ID do produto no carrinho
            $table->decimal('final_value_car', 10, 2); // Valor final do carrinho
            $table->timestamps(); // Colunas created_at e updated_at

            // Definindo chave estrangeira para 'id_user'
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');

            // Definindo chave estrangeira para 'id_prod_q'
            $table->foreign('id_prod_q')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_cart');
    }
};
