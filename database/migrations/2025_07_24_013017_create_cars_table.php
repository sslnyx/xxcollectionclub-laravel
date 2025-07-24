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
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('handle');
            $table->string('title');
            $table->string('hero_image')->nullable();
            $table->text('body_html')->nullable();
            $table->text('gallery')->nullable();
            $table->string('status');
            $table->string('variant_price')->nullable();
            $table->string('condition')->nullable();
            $table->string('brand');
            $table->string('mileage')->nullable();
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
        Schema::dropIfExists('cars');
    }
};