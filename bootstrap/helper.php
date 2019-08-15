<?php
if(! function_exists("dd")) {
    /**
     * Little helper called dump and die
     * @param $val
     */
    function dd($val) {
        \app\framework\Component\VarDumper\VarDumper::dump($val);die;
    }
}

if(! function_exists("pathTo")) {
    /**
     * Easy function to get the path to the project + if you want an directory in it.
     *
     * @param null $path
     * @return bool|string
     */
    function pathTo($path = null) {
        return realpath(dirname(__FILE__)."/../".$path);
    }
}

if(! function_exists("view")) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string  $view        Name of template file.
     * @param  array   $data        Data to set values in template file
     * @return \app\framework\Component\View\View|string
     */
    function view($view = null, $data = []) {
        $data['auth'] = new \app\framework\Component\Auth\Auth;
        $View = new \app\framework\Component\View\View($view, $data);
        return $View->render();
    }
}

if(! function_exists("app")) {
    /**
     * Used to easily call Methods from classes without manually set
     * locally Instances of them.
     *
     * @param string $classMethod The class name(if in \app\custom\ namespace) or the "namespace+className@methodToCall"
     * @param array $param To declare what parameter shall be passed to the method
     * @return mixed
     */
    function app($classMethod, $param = []) {
        return $GLOBALS["App"]->call($classMethod, $param);
    }
}

if(! function_exists("url")) {
    /**
     * Basically completes just the the url
     * e.g. /test to yourexample.site/test
     *
     * Simple, very simple.
     *
     * @param $path
     *
     * @return string
     */
    function url($path) {
        return $_SERVER['HTTP_HOST'].$path;
    }
}

if(! function_exists("getStringBetween")) {
    /**
     * This is a handy little function to strip out a string between
     * two specified pieces of text. This could be used to parse
     * XML text, bbCode, or any other delimited code/text for that matter.
     *
     * @param $string
     * @param $start
     * @param $end
     * @return bool|string
     */
    function getStringBetween($string, $start, $end) {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}

if(! function_exists("handle")) {
    function handle( $e) {
        \app\framework\Component\Exception\Handler::getInstance()->handler($e);
    }
}

if(! function_exists("arr")) {
    /**
     * Create an ArrayObject from array
     * @param array $arr
     * @return \app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject
     */
    function arr(array $arr = null) {
        if (is_null($arr)) {
            return new \app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObjectFacade();
        } else {
            return new \app\framework\Component\StdLib\StdObject\ArrayObject\ArrayObject($arr);
        }
    }
}

if(! function_exists("str")) {
    function str($str) {
        return new \app\framework\Component\StdLib\StdObject\StringObject\StringObject($str);
    }
}

if(! function_exists("encrypt")) {
    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool   $serialize
     * @throws Exception
     * @return string
     */
    function encrypt($value, $serialize = true) {
        $Encryptor = new \app\framework\Component\Encryption\Encrypter(
            \app\framework\Component\Config\Config::getInstance()->get("CrypKey")
        );

        return $Encryptor->encrypt($value, $serialize );
    }
}

if(! function_exists("decrypt")) {
    /**
     * Decrypt the given value.
     *
     * @param  string  $value
     * @param  bool   $unserialize
     * @throws Exception
     * @return mixed
     */
    function decrypt($value, $unserialize = true) {
        $Encryptor = new \app\framework\Component\Encryption\Encrypter(
            \app\framework\Component\Config\Config::getInstance()->get("CrypKey")
        );

        return $Encryptor->decrypt($value, $unserialize );
    }
}

if(! function_exists("version")) {
    /**
     * @return string version as written in config/app.php
     */
    function version() {
        return \app\framework\Component\Config\Config::getInstance()->get("version", "app");
    }
}

if(! function_exists("isDebug")) {
    function isDebug() {
        return \app\framework\Component\Config\Config::getInstance()->get("debug", "app");
    }
}

if(! function_exists("class_basename")) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param  string|object  $class
     * @return string
     */
    function class_basename($class)
    {
        $class = is_object($class) ? get_class($class) : $class;
        return basename(str_replace('\\', '/', $class));
    }
}

if (! function_exists("get_connection_log"))
{
    /**
     * returns query log from Connection as array
     *
     * @return array
     * @throws \app\framework\Component\Database\Connection\ConnectionNotConfiguredException
     */
    function get_connection_log()
    {
        return \app\framework\Component\Database\Connection\ConnectionFactory::getInstance()->get()->getQueryLog();
    }
}
