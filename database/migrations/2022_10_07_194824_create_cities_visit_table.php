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
        Schema::create('cities_visit', function (Blueprint $table) {
            $table->id();
            $table->integer('userId');
            $table->integer('cityId');
            $table->tinyInteger('rate')->nullable();
            $table->timestamps();

            $table->unique(['userId', 'cityId']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities_visit');
    }
};
