<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

    namespace app\framework\Component\Database\Migrations\Commands;

    use app\framework\Component\Console\Command\Command;
    use app\framework\Component\Console\Input\InputInterface;
    use app\framework\Component\Console\Output\ConsoleOutput;
    use app\framework\Component\Console\Output\Output;
    use app\framework\Component\Database\Migrations\Migration;
    use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;

    class Migrate extends Command
    {
        private $namespace = "app\\custom\\Database\\migrations\\";

        /**
         * @var Migration[] $migrations
         */
        private $migrations = [];

        protected function configure()
        {
            $this->setName("migrate")
                ->setDescription("Migrate your database.");
        }

        protected function execute(InputInterface $input, ConsoleOutput $output)
        {
            $this->setMigrations();

            foreach ($this->migrations as $className => $migration) {
                $output->writeln("<info>Migrating:</info> ".$className);
                $migration->down();
                $migration->up();
                $output->writeln("<comment>Migrated:</comment> ".$className);
            }
        }

        private function setMigrations()
        {
            // get all migrations
            // run up() methods
            $classes = [];
            $namespace = $this->namespace;

            $path = str_replace("\\","/", $namespace);
            $filesInNamespace = new ArrayObject(scandir(ROOT_PATH."/".$path."/"));
            $filesInNamespace->removeFirst()->removeFirst();

            for ($i = 0; $i <= $filesInNamespace->count()-1; $i++)
                $filesInNamespace->key($i, explode(".php", $filesInNamespace->key($i))[0]);

            foreach ($filesInNamespace->val() as $value) {
                $class = $namespace.$value;
                if(class_exists($class) && is_subclass_of($class,'app\framework\Component\Database\Migrations\Migration'))
                    $classes[$value] = new $class();
            }

            // tell user so if there are no migrations
            if($classes == [])
                $output->writeln("No migrations found! You can Create migrations via the make:migration command.");
            else {
                foreach ($classes as $class) {
                    $output->writeln("<info>Migrating: ".get_class($class)."</info>");
                    $output->writeln("<comment>Running down!</comment>");
                    $class->down();
                    $output->writeln("<comment>Running up!</comment>");
                    $class->up();
                }
                $output->writeln("Migration successful");
            }
        }
    }