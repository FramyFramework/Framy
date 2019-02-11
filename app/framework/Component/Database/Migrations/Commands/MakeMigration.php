<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Database\Migrations\Commands;

use app\framework\Component\Console\Command\Command;
use app\framework\Component\Console\Input\InputArgument;
use app\framework\Component\Console\Input\InputDefinition;
use app\framework\Component\Console\Input\InputInterface;
use app\framework\Component\Console\Output\ConsoleOutput;
use app\framework\Component\Storage\File\File;
use app\framework\Component\Storage\Storage;

class MakeMigration extends Command
{
    private $template = <<<'EOD'
<?php
namespace app\custom\Database\migrations;

use app\framework\Component\Database\Migrations\Migration;
use app\framework\Component\Database\Schema\Blueprint;
use app\framework\Component\Database\Schema\Schema;

class §NAME§ extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("§NAME§", function(Blueprint $table) {
            $table->increments();
            $table->timestamps();
        }, "§connection§");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop("§NAME§", "§connection§");
    }
}
EOD;

    protected function configure()
    {
        $this->setName("make:migration")
            ->setDefinition(new InputDefinition([
                new InputArgument("name", InputArgument::REQUIRED, "Name of migrations to create."),
                new InputArgument("connection", InputArgument::REQUIRED, "What connection to use")
            ]))
            ->setDescription("Creates a new migration");
    }

    protected function execute(InputInterface $input, ConsoleOutput $output)
    {
        $newMigrationName = $input->getArgument("name");
        $connection       = $input->getArgument("connection");
        $output->writeln("<info>creating migration: ".$newMigrationName."</info>", ConsoleOutput::OUTPUT_PLAIN);

        try {
            $File = new File($newMigrationName.".php", new Storage("migrations"));

            if(fopen($File->getAbsolutePath(), "w")) {
                $tempDefaultCommand = str_replace("§NAME§", $newMigrationName, $this->template);
                $tempDefaultCommand = str_replace("§connection§", $connection, $tempDefaultCommand);
                file_put_contents($File->getAbsolutePath(), $tempDefaultCommand);

                $output->writeln("<info>migrations: ".$newMigrationName." was successfully created</info>");
            } else {
                $output->writeln("<error>creating new migrations failed</error>");
            }
        } catch (\Exception $e) {
            $output->writeln("<error>An error happened: ".$e->getMessage()."</error>");
        }
    }
}
