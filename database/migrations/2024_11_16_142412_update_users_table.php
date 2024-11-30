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

            
            $table->uuid('user_id')->primary()->default(DB::raw('uuid()'))->after('id'); 

            
            
            $table->string('contact_phone')->nullable()->after('password'); 
            $table->string('address')->nullable()->after('contact_phone'); 
            $table->unsignedBigInteger('orders_id')->nullable()->after('address'); 
            $table->unsignedBigInteger('id_cart')->nullable()->after('orders_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           
            $table->dropColumn('user_id');
            $table->dropColumn('contact_phone');
            $table->dropColumn('address');
            $table->dropColumn('orders_id');
            $table->dropColumn('id_cart');
        });
    }
};
