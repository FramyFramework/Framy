<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author  Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Exception;

use app\framework\Component\Config\Config;
use app\framework\Component\StdLib\SingletonTrait;

class Handler
{
    use SingletonTrait;

    public function handler(\Exception $e)
    {
        if(Config::getInstance()->get("debug", "app")) {
            view("exception", [
                'filename' => $e->getFile(),
                'lineNumber' => $e->getLine(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            die();
        }
    }
}
