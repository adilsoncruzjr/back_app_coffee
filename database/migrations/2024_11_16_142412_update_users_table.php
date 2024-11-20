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
        Schema::table('users', function (Blueprint $table) {

            // Adicionando o campo 'user_id' como UUID
            $table->uuid('user_id')->primary()->default(DB::raw('uuid()'))->after('id'); // 'user_id' agora é UUID e é chave primária

            // Adicionando as colunas à tabela users
            
            $table->string('contact_phone')->nullable()->after('password'); // Telefone de contato
            $table->string('address')->nullable()->after('contact_phone'); // Endereço do usuário
            $table->unsignedBigInteger('orders_id')->nullable()->after('address'); // Relacionamento com pedidos
            $table->unsignedBigInteger('id_cart')->nullable()->after('orders_id'); // Relacionamento com carrinho
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Removendo as colunas adicionadas
            $table->dropColumn('user_id');
            $table->dropColumn('contact_phone');
            $table->dropColumn('address');
            $table->dropColumn('orders_id');
            $table->dropColumn('id_cart');
        });
    }
};
