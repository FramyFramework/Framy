<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Console\Command;

use app\framework\Component\Console\Input\InputArgument;
use app\framework\Component\Console\Input\InputDefinition;
use app\framework\Component\Console\Input\InputInterface;
use app\framework\Component\Console\Output\ConsoleOutput;
use app\framework\Component\Storage\File\File;
use app\framework\Component\Storage\Storage;

class MakeMiddleware extends Command
{
    private $template = <<<'EOD'
<?php
namespace app\custom\Http\Middleware;

use app\framework\Component\Http\MiddlewareInterface;
use app\framework\Component\Routing\Request;

class §NAME§ implements MiddlewareInterface 
{
    public function handle(Request $request)
    {
        // add logic here ...
    }
} 
EOD;

    protected function configure()
    {
        $this->setName("make:middleware")
            ->setDescription("Adds an new middleware class")
            ->setDefinition(new InputDefinition([
                new InputArgument("name", InputArgument::REQUIRED, "The name of the Middleware")
            ]))
            ->setHelp("To add an middleware directly where it belongs");
    }

    protected function execute(InputInterface $input, ConsoleOutput $output)
    {
        $newControllerName = $input->getArgument("name");

        $file = new File($newControllerName.".php", new Storage("middleware"), true);

        file_put_contents(
            $file->getAbsolutePath(),
            str_replace("§NAME§", $newControllerName, $this->template)
        );

        $output->writeln("File created...");
        $output->writeln("All you have to do now is to register the new middleware in the config file.");
    }
}
