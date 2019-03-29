<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('categories');
            $table->boolean('deleted')->default(false);
            $table->timestamps();
        });

//        Schema::create('spot_types', function (Blueprint $table) {
//            $table->increments('id');
//            $table->string('name');
//            $table->integer('category_id')->unsigned()->index();
//            $table->foreign('category_id')->references('id')->on('spot_categories');
//            $table->timestamps();
//        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('types');
    }
}
