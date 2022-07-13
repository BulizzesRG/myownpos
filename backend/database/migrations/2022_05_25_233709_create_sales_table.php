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
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->decimal('payment',7,2);
            $table->decimal('total',7,2);
            $table->decimal('change',7,2);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('buyer_id');
            $table->boolean('is_canceled')->default(false);
            $table->string('cancelled_reason',255)->nullable();
            $table->unsignedTinyInteger('payment_method_id');
            $table->integer('porcentaje_extra')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
