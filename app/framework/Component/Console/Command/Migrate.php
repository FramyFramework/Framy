<?php
/**
 * Created by PhpStorm.
 * User: Marco
 * Date: 26.05.2018
 * Time: 15:11
 */

    namespace app\framework\Component\Console\Command;

    use app\framework\Component\Console\Input\InputInterface;
    use app\framework\Component\Console\Output\ConsoleOutput;
    use app\framework\Component\Storage\Directory\Directory;
    use app\framework\Component\Storage\Storage;


    class Migrate extends Command
    {
        private $namepace = "app\\custom\\Database\\migrations\\";

        protected function configure()
        {
            $this->setName("migrate")
                ->setDescription("");
        }

        protected function execute(InputInterface $input, ConsoleOutput $output)
        {
            // get all migrations
            // run up() methods
            $dir = new Directory('', new Storage('migration'));

            foreach($dir->filter('*.php') as $file){
                require_once $dir->getAbsolutePath()."/".$file->getKey();

                $class = $this->namepace.basename($file->getKey(), '.php');
                var_dump($class);
                var_dump(class_exists($class));
            }
            //dd(get_declared_classes());
        }
    }