<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionTables extends Migration
{

    private function modelHasSchema($permissionOrRole, $tableNames){

        Schema::create($tableNames['model_has_'.$permissionOrRole], function (Blueprint $table) use ($tableNames, $permissionOrRole) {
            $table->integer($permissionOrRole.'_id')->unsigned();
            $table->morphs('model');

            $table->foreign($permissionOrRole.'_id')
                ->references('id')
                ->on($tableNames[$permissionOrRole.'s'])
                ->onDelete('cascade');

            $table->primary([$permissionOrRole.'_id', 'model_id', 'model_type']);
        });

    }

    private function basicSchema($permissionOrRole, $tableNames){

        Schema::create($tableNames[$permissionOrRole."s"], function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
        });

    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableNames = config('permission.table_names');

        $this->basicSchema("permission", $tableNames);
        $this->basicSchema("role", $tableNames);

        $this->modelHasSchema("permission", $tableNames);
        $this->modelHasSchema("role", $tableNames);

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned();

            $table->foreign('permission_id')
                ->references('id')
                ->on($tableNames['permissions'])
                ->onDelete('cascade');

            $table->foreign('role_id')
                ->references('id')
                ->on($tableNames['roles'])
                ->onDelete('cascade');

            $table->primary(['permission_id', 'role_id']);

            app('cache')->forget('spatie.permission.cache');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tableNames = config('permission.table_names');

        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
}
