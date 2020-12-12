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
            $table->string('id',20)->unique()->primary();

            $table->string('barcode',20)->nullable();

            $table->string('name',100);

            $table->string('name_initial',5)->nullable()
                ->comment("2 or 3 letters which represent product's name");

            $table->integer('unit_id')->comment('one to many relationship with Units table');

            $table->integer('category_id')->comment('one to many relationship with categories table');

            $table->enum('stock_type',['single','composite'])
                  ->comment("single means product stock is determined by the qty of the product itself,
                  while composite is determined by the qty of it's ingredient");

            $table->string('primary_ingredient_id',20)
                  ->comment("is used to determine the qty of a composite product.
                  one to many relationship with products table");

            $table->bigInteger('primary_ingredient_qty')
                  ->comment("is used to determine the qty of a composite product");;

            $table->boolean('for_sale');

            $table->string('image',100)->nullable();

            $table->integer('minimum_qty')
                  ->comment("User will be informed when product qty is below the minimum_qty");

            $table->integer('minimum_expiration_days')
                  ->comment("User wil be informed when days to expiration date is equal or
                  less than 'minimum_expiration_days'");

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
        Schema::dropIfExists('products');
    }
}
