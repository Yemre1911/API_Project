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
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Product ID
            $table->unsignedBigInteger('color_id'); // Color ID
            $table->unsignedBigInteger('size_id'); // Size ID
            $table->string('main_image')->nullable(); // Varyantın ana resmi, isteğe bağlı
            $table->string('img1')->nullable(); // Ekstra resim 1
            $table->string('img2')->nullable(); // Ekstra resim 2
            $table->string('img3')->nullable(); // Ekstra resim 3
            $table->timestamps();

            // Foreign key tanımları
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('color_id')->references('id')->on('colors')->onDelete('cascade');
            $table->foreign('size_id')->references('id')->on('sizes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};
