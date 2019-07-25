<?php
namespace app\custom\Database\migrations;

use app\framework\Component\Database\Migrations\Migration;
use app\framework\Component\Database\Schema\Blueprint;
use app\framework\Component\Database\Schema\Schema;

class createUsersRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("role_user", function(Blueprint $table) {
            $table->integer("user_id");
            $table->integer("role_id");
        }, "mysql");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("role_user", "mysql");
    }
}
