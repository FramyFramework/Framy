<?php
    namespace app\custom\Database\migrations;

    use app\framework\Component\Database\Migrations\Migration;
    use app\framework\Component\Database\Schema\Blueprint;
    use app\framework\Component\Database\Schema\Schema;

    class user extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create("user", function(Blueprint $table) {
                $table->increments();
                $table->string("name");
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
            Schema::drop("user", "mysql");
        }
    }