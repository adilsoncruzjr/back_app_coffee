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
        Schema::table('orders', function (Blueprint $table) {
            // Adicionar ou modificar colunas na tabela 'orders'

            // Adicionando uma nova coluna exemplo
            $table->string('tracking_code')->nullable()->after('status'); // Código de rastreamento

            // Alterar uma coluna existente, se necessário
            // Exemplo: tornar `final_value` nullable
            $table->decimal('final_value', 10, 2)->nullable()->change();

            // Qualquer outra alteração que precisar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Reverter as alterações realizadas no método up()

            // Remover a coluna adicionada
            $table->dropColumn('tracking_code');

            // Reverter a alteração na coluna existente, se necessário
            // Exemplo: tornar `final_value` obrigatório novamente
            $table->decimal('final_value', 10, 2)->nullable(false)->change();
        });
    }
};
