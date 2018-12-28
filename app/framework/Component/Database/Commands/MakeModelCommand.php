<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Commands;

    use app\framework\Component\Console\Command\Command;
    use app\framework\Component\Console\Input\InputArgument;
    use app\framework\Component\Console\Input\InputDefinition;
    use app\framework\Component\Console\Input\InputInterface;
    use app\framework\Component\Console\Output\ConsoleOutput;
    use app\framework\Component\Storage\File\File;
    use app\framework\Component\Storage\Storage;

    class MakeModelCommand extends Command
    {
        private $template = <<<'EOD'
<?php
    namespace app\custom\Models;
    
    use app\framework\Component\Database\Model\Model;
    
    class §NAME§ extends Model
    {
        §CONNECTION§
        §TABLE§
    }
EOD;

        protected function configure()
        {
            $this->setName("make:model")
                ->setDescription("Creating a new Model")
                ->setDefinition(new InputDefinition([
                    new InputArgument("name", InputArgument::REQUIRED, "Name of the Model"),
                    new InputArgument("tableName", InputArgument::OPTIONAL, "Tell what table shall be used."),
                    new InputArgument("connectionName", InputArgument::OPTIONAL, "Tell what connection shall be used.")
                ]))
                ->setHelp("Creating a new Model where it belongs");
        }

        protected function execute(InputInterface $input, ConsoleOutput $output)
        {
            $migrationName = $input->getArgument("name");
            $tableName =  $input->getArgument("tableName");
            $connectionName =  $input->getArgument("connectionName");

            $template = str_replace("§NAME§", $migrationName, $this->template);

            if(isset($connectionName))
                $template = str_replace("§CONNECTION§", 'protected $connection = "'.$connectionName.'";', $template);
            else
                $template = str_replace("§CONNECTION§", '', $template);

            if(isset($tableName))
                $template = str_replace("§TABLE§", 'protected $table = "'.$tableName.'";', $template);
            else
                $template = str_replace("§TABLE§", '', $template);

            $File = new File($migrationName.".php", new Storage("model"));
            //$File->setContents($template);
            file_put_contents($File->getAbsolutePath(), $template);

            $output->writeln("<info>Model created successfully.</info>");
        }
    }