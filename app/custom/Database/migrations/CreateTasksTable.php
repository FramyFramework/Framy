<?php
namespace app\custom\Database\migrations;

use app\framework\Component\Database\Migrations\Migration;
use app\framework\Component\Database\Schema\Blueprint;
use app\framework\Component\Database\Schema\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("tasks", function(Blueprint $table) {
            $table->increments();
            $table->integer("project");
            $table->string("title");
            $table->text("description");
            $table->integer("createdBy");
            $table->integer("assignedTo");
            $table->integer("status");
            $table->integer("priority");
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
        Schema::drop("tasks", "mysql");
    }
}
