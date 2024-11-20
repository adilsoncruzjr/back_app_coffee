<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id(); // Auto-increment para 'id'
            $table->string('id_prod')->unique()->default(DB::raw('uuid()')); // 'id_prod' como UUID gerado automaticamente
            $table->string('name_prod'); // Nome do produto
            $table->decimal('value_prod', 8, 2); // Valor do produto (decimais)
            $table->text('description'); // Descrição do produto
            $table->integer('stock'); // Estoque do produto
            $table->timestamps(); // Colunas created_at e updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
