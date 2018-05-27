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
    use app\framework\Component\Database\Migrations\Migration;
    use app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject;
    use app\framework\Component\Storage\Directory\Directory;
    use app\framework\Component\Storage\Storage;


    class Migrate extends Command
    {
        private $namespace = "app\\custom\\Database\\migrations\\";

        protected function configure()
        {
            $this->setName("migrate")
                ->setDescription("");
        }

        protected function execute(InputInterface $input, ConsoleOutput $output)
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
                    $classes[] = new $class();
            }

            foreach ($classes as $class) {
                $class->up();
            }
        }
    }