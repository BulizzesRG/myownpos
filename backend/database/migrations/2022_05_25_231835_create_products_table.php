<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('description')->nullable();			
            $table->string('photo')->nullable();
            $table->string('barcode')->unique();
            $table->string('alternative_code')->unique();
            $table->decimal('purchase_price');
            $table->decimal('sale_price');
            $table->boolean('is_active')->default(true);
            $table->enum('format_of_sell', ['pieza','servicio', 'granel', 'caja'])->default('pieza');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
