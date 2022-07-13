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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->integer('phone')->nullable();
            $table->integer('alternative_phone')->nullable();
            $table->string('photo')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_staff')->default(false);
            $table->double('initial_bill',8,2)->default(0.00);
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
