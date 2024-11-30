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
           
            $table->string('tracking_code')->nullable()->after('status'); 
            $table->decimal('final_value', 10, 2)->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->dropColumn('tracking_code');

        
            $table->decimal('final_value', 10, 2)->nullable(false)->change();
        });
    }
};
