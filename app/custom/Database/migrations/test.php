<?php
    namespace app\custom\Database\migrations;

    use app\framework\Component\Database\Migrations\Migration;
    use app\framework\Component\Database\Schema\Blueprint;
    use app\framework\Component\Database\Schema\Schema;

    class test extends Migration
    {
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create("da", function(Blueprint $table) {
                $table->increments();
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
            Schema::drop("da");
        }
    }