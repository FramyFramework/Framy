<?php
namespace app\custom\Database\migrations;

use app\framework\Component\Database\Migrations\Migration;
use app\framework\Component\Database\Schema\Blueprint;
use app\framework\Component\Database\Schema\Schema;

class CreateProjectsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("projects-users", function(Blueprint $table) {
            $table->integer("user_id");
            $table->integer("project_id");
            $table->timestamps();
        }, "mysql");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("projects-users", "mysql");
    }
}
