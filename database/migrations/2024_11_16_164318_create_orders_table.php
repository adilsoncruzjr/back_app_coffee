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
            $table->id(); 
            $table->unsignedBigInteger('id_user'); 
            $table->json('id_prod'); 
            $table->decimal('final_value', 10, 2); 
            $table->timestamp('created_at')->useCurrent(); 
            $table->string('status'); 
            $table->unsignedBigInteger('id_car'); 
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade'); 
            $table->foreign('id_car')->references('id')->on('shopping_cart')->onDelete('cascade'); 
        
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
