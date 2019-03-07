<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

namespace app\framework\Component\Auth;

use app\framework\Component\Validation\ValidationTrait;

/**
 * Trait AuthenticatesAndRegistersUsers
 * @package app\custom\Http\Controller\Auth
 */
trait AuthenticatesAndRegistersUsers
{
    use AuthenticatesUsers,RegistersUsers,ValidationTrait;
}
