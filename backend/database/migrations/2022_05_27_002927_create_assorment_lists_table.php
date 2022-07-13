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
        Schema::create('assortment_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_user_id');
            $table->unsignedBigInteger('user_supplied_id');
            $table->string('title');
            $table->boolean('assortment_status')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_user_id')->references('id')->on('users')->onDelete(null);
            $table->foreign('user_supplied_id')->references('id')->on('users')->onDelete(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assorment_lists');
    }
};
