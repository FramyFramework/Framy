<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth\Migrations;

use app\framework\Component\Database\Migrations\Migration;
use app\framework\Component\Database\Schema\Blueprint;
use app\framework\Component\Database\Schema\Schema;

/**
 * Class CreateUsersTable
 * @package app\framework\Component\Auth\Migrations
 */
class CreateUsersTable extends Migration
{
    private $tableName = "users";

    /**
     * @inheritDoc
     */
    public function up()
    {
        Schema::create($this->tableName, function (Blueprint $table) {
            $table->increments();
            $table->string('name');
            $table->string('email');
            $table->string('password', 60);
            $table->timestamps();
        }, "mysql");
    }

    /**
     * @inheritDoc
     */
    public function down()
    {
        Schema::drop($this->tableName);
    }
}
