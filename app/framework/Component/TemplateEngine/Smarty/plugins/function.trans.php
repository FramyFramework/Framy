<?php
/**
 * Framy Framework
 *
 * @copyright Copyright Framy
 * @Author Marco Bier <mrfibunacci@gmail.com>
 */

/**
 * Smarty {trans} function plugin
 * Type:     function<br>
 * Name:     trans<br>
 * Purpose:  print out a translation string
 *
 * @author Monte Ohrt <monte at ohrt dot com>
 * @link   http://www.smarty.net/manual/en/language.function.counter.php {counter}
 *         (Smarty online manual)
 *
 * @param array                    $params   parameters
 * @param Smarty_Internal_Template $template template object
 *
 * @return string|null
 */
function smarty_function_trans($params, $template)
{
    return \app\framework\Component\Localization\Trans::get($params['k']);
}
