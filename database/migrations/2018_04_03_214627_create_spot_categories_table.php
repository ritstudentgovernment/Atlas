<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSpotCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('spot_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('icon_prefix');
            $table->text("description");
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

        Schema::dropIfExists('spot_categories');
        Schema::dropIfExists('categories');

    }
}
