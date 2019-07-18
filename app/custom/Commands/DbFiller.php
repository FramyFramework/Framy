<?php
namespace app\custom\Commands;

use app\custom\Models\Post;
use app\custom\Models\User;
use app\framework\Component\Console\Command\Command;
use app\framework\Component\Console\Input\InputInterface;
use app\framework\Component\Console\Output\ConsoleOutput;

class DbFiller extends Command
{
    protected function configure()
    {
        $this->setName("DbFiller")
            ->setDescription("Database filler for testing ENV")
            ->setHelp("");
    }
    
    protected function execute(InputInterface $input, ConsoleOutput $output)
    {
        $user = new User(['username' => "Test"]);
        $output->writeln("Saving user successful: ".$user->save());

        $post = new Post(["content" => "some noice content", "user_id" => 1]);
        $output->writeln("Saving post successful: ".$post->save());
    }
}