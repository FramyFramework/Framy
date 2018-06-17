<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Model\Console;

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
    
    use app\framework\Component\http\Model\Model;
    
    class §NAME§ extends Model
    {
        
    }
EOD;

        protected function configure()
        {
            $this->setName("make:model")
                ->setDescription("Creating a new Model")
                ->setDefinition(new InputDefinition([
                    new InputArgument("name", InputArgument::REQUIRED, "Name of the Model")
                ]))
                ->setHelp("Creating a new Model where it belongs");
        }

        protected function execute(InputInterface $input, ConsoleOutput $output)
        {
            $migrationName = $input->getArgument("name");
            $template      = str_replace("§NAME§", $migrationName, $this->template);

            $File = new File($migrationName.".php", new Storage("model"));
            //$File->setContents($template);
            file_put_contents($File->getAbsolutePath(), $template);

            $output->writeln("<info>Model created successfully.</info>");
        }
    }