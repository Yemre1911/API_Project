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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id'); // Kategori ID'si
            $table->timestamps();
            $table->string('name');
            $table->text('description')->nullable(); // Ürün açıklaması, isteğe bağlı
            $table->decimal('main_price', 8, 2);
            $table->string('main_image')->nullable(); // Ürünün ana resmi, isteğe bağlı

            // Kategori tablosuna olan foreign key tanımı
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');

            $table->engine = 'InnoDB'; // Motoru belirt

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
