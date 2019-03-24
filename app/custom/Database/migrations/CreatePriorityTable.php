<?php
namespace app\custom\Database\migrations;

use app\framework\Component\Database\Migrations\Migration;
use app\framework\Component\Database\Schema\Blueprint;
use app\framework\Component\Database\Schema\Schema;

class CreatePriorityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("priority", function(Blueprint $table) {
            $table->increments();
            $table->text("text");
        }, "mysql");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("priority", "mysql");
    }
}
