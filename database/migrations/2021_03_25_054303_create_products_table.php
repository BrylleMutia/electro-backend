<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
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
            $table->timestamps();
            $table->string('product_name');
            $table->decimal('price', $precision = 9, $scale = 2);
            $table->foreignId('seller_id');
            $table->foreign('seller_id')->references('id')->on('sellers');
            $table->foreignId('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->text('description')->nullable();
            $table->json('product_image');
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
}
