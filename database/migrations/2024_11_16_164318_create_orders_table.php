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
        Schema::create('orders', function (Blueprint $table) {
            $table->id(); // id auto-incrementável
            $table->unsignedBigInteger('id_user'); // Relacionamento com o usuário
            $table->json('id_prod'); // Lista de produtos como JSON
            $table->decimal('final_value', 10, 2); // Valor final
            $table->timestamp('created_at')->useCurrent(); // Data e hora de criação
            $table->string('status'); // Status do pedido
            $table->unsignedBigInteger('id_car'); // Relacionamento com o carrinho
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade'); // Relacionamento com a tabela de usuários
            $table->foreign('id_car')->references('id')->on('shopping_cart')->onDelete('cascade'); // Relacionamento com a tabela de carrinhos
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
