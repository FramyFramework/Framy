<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Console\Command;


use app\framework\Component\Config\Config;
use app\framework\Component\Console\Input\InputArgument;
use app\framework\Component\Console\Input\InputDefinition;
use app\framework\Component\Console\Input\InputInterface;
use app\framework\Component\Console\Output\ConsoleOutput;

class KeyGenerateCommand extends Command
{
    protected function configure()
    {
        $this->setName("key:generate")
            ->setDescription("Auto generate the config value CrypKey")
            ->setDefinition(new InputDefinition([
                new InputArgument(
                    "length",
                    InputArgument::OPTIONAL,
                    "Define the length of the key"
                )
            ]));
    }

    protected function execute(InputInterface $input, ConsoleOutput $output)
    {
        $path       = ROOT_PATH."/config/app.php";
        $length     = ($input->getArgument("length")) ?: 128;
        $currentKey = Config::getInstance()->get("CrypKey");
        $confFile   = file_get_contents($path);
        $confFile   = str_replace($currentKey, $this->random_str($length), $confFile);

        file_put_contents($path, $confFile);

        $output->writeln("Done! Take a look at $path to see the changes.");

    }

    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param int $length      How many characters do we want?
     * @param string $keyspace A string of all possible characters
     *                         to select from
     * @return string
     * @throws \Exception
     */
    function random_str($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $pieces = [];
        $max = mb_strlen($keyspace, '8bit') - 1;
        for ($i = 0; $i < $length; ++$i) {
            $pieces []= $keyspace[random_int(0, $max)];
        }
        return implode('', $pieces);
    }
}