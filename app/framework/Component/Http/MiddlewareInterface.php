<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Http;

use app\framework\Component\Routing\Request;

interface MiddlewareInterface
{
    /**
     * The action to perform
     *
     * @param Request $request
     * @return mixed
     */
    public function handle(Request $request);
}
