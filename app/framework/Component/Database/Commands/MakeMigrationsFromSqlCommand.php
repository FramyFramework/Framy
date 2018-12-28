<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Commands;

    use app\framework\Component\Console\Command\Command;
    use app\framework\Component\Console\Input\InputDefinition;
    use app\framework\Component\Console\Input\InputInterface;
    use app\framework\Component\Console\Input\InputOption;
    use app\framework\Component\Console\Output\ConsoleOutput;

    class MakeMigrationsFromSqlCommand extends Command
    {
        protected function configure()
        {
            $this->setName("make:migrations:fromSql")
                ->setDefinition(new InputDefinition([
                    new InputOption("file", null, InputOption::VALUE_REQUIRED, "Define where to find the sql file")
                ]));
        }

        protected function execute(InputInterface $input, ConsoleOutput $output)
        {
            $file = $input->getOption("file");

            if(!($fileContent = file_get_contents($file))) {
                $output->writeln("File not found: '".$file."'");exit();
            }

            // search for CREATE TABLE statements in file in path
            preg_match_all("@create table (?:(?!--).)*@", $fileContent, $res);

            dd($res);
        }
    }