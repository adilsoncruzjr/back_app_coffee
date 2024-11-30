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
         
         Schema::table('shopping_cart', function (Blueprint $table) {
            $table->dropForeign(['id_prod_q']); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       
        Schema::table('shopping_cart', function (Blueprint $table) {
            $table->foreign('id_prod_q')->references('id')->on('products')->onDelete('cascade');
            //
        });
    }
};
