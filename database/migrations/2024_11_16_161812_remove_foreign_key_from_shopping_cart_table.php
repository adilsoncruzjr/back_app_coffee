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
         // Removendo a chave estrangeira que referencia 'id_prod_q'
         Schema::table('shopping_cart', function (Blueprint $table) {
            $table->dropForeign(['id_prod_q']); // Remove a chave estrangeira
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertendo a remoção da chave estrangeira, caso necessário
        Schema::table('shopping_cart', function (Blueprint $table) {
            $table->foreign('id_prod_q')->references('id')->on('products')->onDelete('cascade');
            //
        });
    }
};
