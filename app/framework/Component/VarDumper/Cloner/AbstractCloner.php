<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace app\framework\Component\VarDumper\Cloner;

use app\framework\Component\VarDumper\Caster\Caster;
use app\framework\Component\VarDumper\Exception\ThrowingCasterException;

/**
 * AbstractCloner implements a generic caster mechanism for objects and resources.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
abstract class AbstractCloner implements ClonerInterface
{
    public static $defaultCasters = [
        '__PHP_Incomplete_Class' => ['app\framework\Component\VarDumper\Caster\Caster', 'castPhpIncompleteClass'],

        'app\framework\Component\VarDumper\Caster\CutStub' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'app\framework\Component\VarDumper\Caster\CutArrayStub' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'castCutArray'],
        'app\framework\Component\VarDumper\Caster\ConstStub' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'castStub'],
        'app\framework\Component\VarDumper\Caster\EnumStub' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'castEnum'],

        'Closure' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castClosure'],
        'Generator' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castGenerator'],
        'ReflectionType' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castType'],
        'ReflectionGenerator' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castReflectionGenerator'],
        'ReflectionClass' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castClass'],
        'ReflectionFunctionAbstract' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castFunctionAbstract'],
        'ReflectionMethod' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castMethod'],
        'ReflectionParameter' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castParameter'],
        'ReflectionProperty' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castProperty'],
        'ReflectionReference' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castReference'],
        'ReflectionExtension' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castExtension'],
        'ReflectionZendExtension' => ['app\framework\Component\VarDumper\Caster\ReflectionCaster', 'castZendExtension'],

        'Doctrine\Common\Persistence\ObjectManager' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Doctrine\Common\Proxy\Proxy' => ['app\framework\Component\VarDumper\Caster\DoctrineCaster', 'castCommonProxy'],
        'Doctrine\ORM\Proxy\Proxy' => ['app\framework\Component\VarDumper\Caster\DoctrineCaster', 'castOrmProxy'],
        'Doctrine\ORM\PersistentCollection' => ['app\framework\Component\VarDumper\Caster\DoctrineCaster', 'castPersistentCollection'],

        'DOMException' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castException'],
        'DOMStringList' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNameList' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMImplementation' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castImplementation'],
        'DOMImplementationList' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNode' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castNode'],
        'DOMNameSpaceNode' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castNameSpaceNode'],
        'DOMDocument' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castDocument'],
        'DOMNodeList' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMNamedNodeMap' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castLength'],
        'DOMCharacterData' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castCharacterData'],
        'DOMAttr' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castAttr'],
        'DOMElement' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castElement'],
        'DOMText' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castText'],
        'DOMTypeinfo' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castTypeinfo'],
        'DOMDomError' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castDomError'],
        'DOMLocator' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castLocator'],
        'DOMDocumentType' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castDocumentType'],
        'DOMNotation' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castNotation'],
        'DOMEntity' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castEntity'],
        'DOMProcessingInstruction' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castProcessingInstruction'],
        'DOMXPath' => ['app\framework\Component\VarDumper\Caster\DOMCaster', 'castXPath'],

        'XMLReader' => ['app\framework\Component\VarDumper\Caster\XmlReaderCaster', 'castXmlReader'],

        'ErrorException' => ['app\framework\Component\VarDumper\Caster\ExceptionCaster', 'castErrorException'],
        'Exception' => ['app\framework\Component\VarDumper\Caster\ExceptionCaster', 'castException'],
        'Error' => ['app\framework\Component\VarDumper\Caster\ExceptionCaster', 'castError'],
        'app\framework\Component\DependencyInjection\ContainerInterface' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'app\framework\Component\HttpClient\CurlHttpClient' => ['app\framework\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'app\framework\Component\HttpClient\NativeHttpClient' => ['app\framework\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClient'],
        'app\framework\Component\HttpClient\Response\CurlResponse' => ['app\framework\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'app\framework\Component\HttpClient\Response\NativeResponse' => ['app\framework\Component\VarDumper\Caster\SymfonyCaster', 'castHttpClientResponse'],
        'app\framework\Component\HttpFoundation\Request' => ['app\framework\Component\VarDumper\Caster\SymfonyCaster', 'castRequest'],
        'app\framework\Component\VarDumper\Exception\ThrowingCasterException' => ['app\framework\Component\VarDumper\Caster\ExceptionCaster', 'castThrowingCasterException'],
        'app\framework\Component\VarDumper\Caster\TraceStub' => ['app\framework\Component\VarDumper\Caster\ExceptionCaster', 'castTraceStub'],
        'app\framework\Component\VarDumper\Caster\FrameStub' => ['app\framework\Component\VarDumper\Caster\ExceptionCaster', 'castFrameStub'],
        'app\framework\Component\VarDumper\Cloner\AbstractCloner' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'app\framework\Component\ErrorCatcher\Exception\SilencedErrorContext' => ['app\framework\Component\VarDumper\Caster\ExceptionCaster', 'castSilencedErrorContext'],

        'ProxyManager\Proxy\ProxyInterface' => ['app\framework\Component\VarDumper\Caster\ProxyManagerCaster', 'castProxy'],
        'PHPUnit_Framework_MockObject_MockObject' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Prophecy\Prophecy\ProphecySubjectInterface' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'cutInternals'],
        'Mockery\MockInterface' => ['app\framework\Component\VarDumper\Caster\StubCaster', 'cutInternals'],

        'PDO' => ['app\framework\Component\VarDumper\Caster\PdoCaster', 'castPdo'],
        'PDOStatement' => ['app\framework\Component\VarDumper\Caster\PdoCaster', 'castPdoStatement'],

        'AMQPConnection' => ['app\framework\Component\VarDumper\Caster\AmqpCaster', 'castConnection'],
        'AMQPChannel' => ['app\framework\Component\VarDumper\Caster\AmqpCaster', 'castChannel'],
        'AMQPQueue' => ['app\framework\Component\VarDumper\Caster\AmqpCaster', 'castQueue'],
        'AMQPExchange' => ['app\framework\Component\VarDumper\Caster\AmqpCaster', 'castExchange'],
        'AMQPEnvelope' => ['app\framework\Component\VarDumper\Caster\AmqpCaster', 'castEnvelope'],

        'ArrayObject' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castArrayObject'],
        'ArrayIterator' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castArrayIterator'],
        'SplDoublyLinkedList' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castDoublyLinkedList'],
        'SplFileInfo' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castFileInfo'],
        'SplFileObject' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castFileObject'],
        'SplFixedArray' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castFixedArray'],
        'SplHeap' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'SplObjectStorage' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castObjectStorage'],
        'SplPriorityQueue' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castHeap'],
        'OuterIterator' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castOuterIterator'],
        'WeakReference' => ['app\framework\Component\VarDumper\Caster\SplCaster', 'castWeakReference'],

        'Redis' => ['app\framework\Component\VarDumper\Caster\RedisCaster', 'castRedis'],
        'RedisArray' => ['app\framework\Component\VarDumper\Caster\RedisCaster', 'castRedisArray'],
        'RedisCluster' => ['app\framework\Component\VarDumper\Caster\RedisCaster', 'castRedisCluster'],

        'DateTimeInterface' => ['app\framework\Component\VarDumper\Caster\DateCaster', 'castDateTime'],
        'DateInterval' => ['app\framework\Component\VarDumper\Caster\DateCaster', 'castInterval'],
        'DateTimeZone' => ['app\framework\Component\VarDumper\Caster\DateCaster', 'castTimeZone'],
        'DatePeriod' => ['app\framework\Component\VarDumper\Caster\DateCaster', 'castPeriod'],

        'GMP' => ['app\framework\Component\VarDumper\Caster\GmpCaster', 'castGmp'],

        'MessageFormatter' => ['app\framework\Component\VarDumper\Caster\IntlCaster', 'castMessageFormatter'],
        'NumberFormatter' => ['app\framework\Component\VarDumper\Caster\IntlCaster', 'castNumberFormatter'],
        'IntlTimeZone' => ['app\framework\Component\VarDumper\Caster\IntlCaster', 'castIntlTimeZone'],
        'IntlCalendar' => ['app\framework\Component\VarDumper\Caster\IntlCaster', 'castIntlCalendar'],
        'IntlDateFormatter' => ['app\framework\Component\VarDumper\Caster\IntlCaster', 'castIntlDateFormatter'],

        'Memcached' => ['app\framework\Component\VarDumper\Caster\MemcachedCaster', 'castMemcached'],

        'Ds\Collection' => ['app\framework\Component\VarDumper\Caster\DsCaster', 'castCollection'],
        'Ds\Map' => ['app\framework\Component\VarDumper\Caster\DsCaster', 'castMap'],
        'Ds\Pair' => ['app\framework\Component\VarDumper\Caster\DsCaster', 'castPair'],
        'app\framework\Component\VarDumper\Caster\DsPairStub' => ['app\framework\Component\VarDumper\Caster\DsCaster', 'castPairStub'],

        ':curl' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castCurl'],
        ':dba' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castDba'],
        ':dba persistent' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castDba'],
        ':gd' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castGd'],
        ':mysql link' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castMysqlLink'],
        ':pgsql large object' => ['app\framework\Component\VarDumper\Caster\PgSqlCaster', 'castLargeObject'],
        ':pgsql link' => ['app\framework\Component\VarDumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql link persistent' => ['app\framework\Component\VarDumper\Caster\PgSqlCaster', 'castLink'],
        ':pgsql result' => ['app\framework\Component\VarDumper\Caster\PgSqlCaster', 'castResult'],
        ':process' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castProcess'],
        ':stream' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castStream'],
        ':OpenSSL X.509' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castOpensslX509'],
        ':persistent stream' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castStream'],
        ':stream-context' => ['app\framework\Component\VarDumper\Caster\ResourceCaster', 'castStreamContext'],
        ':xml' => ['app\framework\Component\VarDumper\Caster\XmlResourceCaster', 'castXml'],
    ];

    protected $maxItems = 2500;
    protected $maxString = -1;
    protected $minDepth = 1;

    private $casters = [];
    private $prevErrorHandler;
    private $classInfo = [];
    private $filter = 0;

    /**
     * @param callable[]|null $casters A map of casters
     *
     * @see addCasters
     */
    public function __construct(array $casters = null)
    {
        if (null === $casters) {
            $casters = static::$defaultCasters;
        }
        $this->addCasters($casters);
    }

    /**
     * Adds casters for resources and objects.
     *
     * Maps resources or objects types to a callback.
     * Types are in the key, with a callable caster for value.
     * Resource types are to be prefixed with a `:`,
     * see e.g. static::$defaultCasters.
     *
     * @param callable[] $casters A map of casters
     */
    public function addCasters(array $casters)
    {
        foreach ($casters as $type => $callback) {
            $this->casters[$type][] = $callback;
        }
    }

    /**
     * Sets the maximum number of items to clone past the minimum depth in nested structures.
     *
     * @param int $maxItems
     */
    public function setMaxItems($maxItems)
    {
        $this->maxItems = (int) $maxItems;
    }

    /**
     * Sets the maximum cloned length for strings.
     *
     * @param int $maxString
     */
    public function setMaxString($maxString)
    {
        $this->maxString = (int) $maxString;
    }

    /**
     * Sets the minimum tree depth where we are guaranteed to clone all the items.  After this
     * depth is reached, only setMaxItems items will be cloned.
     *
     * @param int $minDepth
     */
    public function setMinDepth($minDepth)
    {
        $this->minDepth = (int) $minDepth;
    }

    /**
     * Clones a PHP variable.
     *
     * @param mixed $var    Any PHP variable
     * @param int   $filter A bit field of Caster::EXCLUDE_* constants
     *
     * @return Data The cloned variable represented by a Data object
     */
    public function cloneVar($var, $filter = 0)
    {
        $this->prevErrorHandler = set_error_handler(function ($type, $msg, $file, $line, $context = []) {
            if (E_RECOVERABLE_ERROR === $type || E_USER_ERROR === $type) {
                // Cloner never dies
                throw new \ErrorException($msg, 0, $type, $file, $line);
            }

            if ($this->prevErrorHandler) {
                return ($this->prevErrorHandler)($type, $msg, $file, $line, $context);
            }

            return false;
        });
        $this->filter = $filter;

        if ($gc = gc_enabled()) {
            gc_disable();
        }
        try {
            return new Data($this->doClone($var));
        } finally {
            if ($gc) {
                gc_enable();
            }
            restore_error_handler();
            $this->prevErrorHandler = null;
        }
    }

    /**
     * Effectively clones the PHP variable.
     *
     * @param mixed $var Any PHP variable
     *
     * @return array The cloned variable represented in an array
     */
    abstract protected function doClone($var);

    /**
     * Casts an object to an array representation.
     *
     * @param Stub $stub     The Stub for the casted object
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The object casted as array
     */
    protected function castObject(Stub $stub, $isNested)
    {
        $obj = $stub->value;
        $class = $stub->class;

        if (isset($class[15]) && "\0" === $class[15] && 0 === strpos($class, "class@anonymous\x00")) {
            $stub->class = get_parent_class($class).'@anonymous';
        }
        if (isset($this->classInfo[$class])) {
            list($i, $parents, $hasDebugInfo, $fileInfo) = $this->classInfo[$class];
        } else {
            $i = 2;
            $parents = [$class];
            $hasDebugInfo = method_exists($class, '__debugInfo');

            foreach (class_parents($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            foreach (class_implements($class) as $p) {
                $parents[] = $p;
                ++$i;
            }
            $parents[] = '*';

            $r = new \ReflectionClass($class);
            $fileInfo = $r->isInternal() || $r->isSubclassOf(Stub::class) ? [] : [
                'file' => $r->getFileName(),
                'line' => $r->getStartLine(),
            ];

            $this->classInfo[$class] = [$i, $parents, $hasDebugInfo, $fileInfo];
        }

        $stub->attr += $fileInfo;
        $a = Caster::castObject($obj, $class, $hasDebugInfo);

        try {
            while ($i--) {
                if (!empty($this->casters[$p = $parents[$i]])) {
                    foreach ($this->casters[$p] as $callback) {
                        $a = $callback($obj, $a, $stub, $isNested, $this->filter);
                    }
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }

    /**
     * Casts a resource to an array representation.
     *
     * @param Stub $stub     The Stub for the casted resource
     * @param bool $isNested True if the object is nested in the dumped structure
     *
     * @return array The resource casted as array
     */
    protected function castResource(Stub $stub, $isNested)
    {
        $a = [];
        $res = $stub->value;
        $type = $stub->class;

        try {
            if (!empty($this->casters[':'.$type])) {
                foreach ($this->casters[':'.$type] as $callback) {
                    $a = $callback($res, $a, $stub, $isNested, $this->filter);
                }
            }
        } catch (\Exception $e) {
            $a = [(Stub::TYPE_OBJECT === $stub->type ? Caster::PREFIX_VIRTUAL : '').'⚠' => new ThrowingCasterException($e)] + $a;
        }

        return $a;
    }
}
