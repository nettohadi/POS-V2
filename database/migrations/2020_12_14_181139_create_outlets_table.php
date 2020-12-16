<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOutletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlets', function (Blueprint $table) {
            $table->string('id')->unique()->primary();
            $table->string('name',30);
            $table->string('address',100);
            $table->integer('vat_percentage')->nullable();
            $table->integer('dp_percentage')->nullable();
            $table->string('social_media',100)->nullable();
            $table->string('contact',100)->nullable();
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
        Schema::dropIfExists('outlets');
    }
}
