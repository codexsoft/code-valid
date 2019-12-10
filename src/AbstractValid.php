<?php

namespace CodexSoft\Code\Valid;

use CodexSoft\Code\Classes\Classes;

abstract class AbstractValid
{

    /** @var array */
    protected static $cache = [];

    /** @var array */
    protected static $map;

    /**
     * @noinspection PhpDocMissingThrowsInspection
     *
     * @param string $class
     * @param string $prefix
     *
     * @return array
     */
    private static function grabValidValues(string $class, string $prefix): array
    {
        if (!\array_key_exists($class, static::$cache)) {
            static::$cache[$class] = [];
        }

        if (!\array_key_exists($prefix, static::$cache[$class])) {
            /** @noinspection PhpUnhandledExceptionInspection */
            static::$cache[$class][$prefix] = Classes::grabPrefixedConstantsFromClass($class, $prefix);
        }

        return static::$cache[$class][$prefix];

    }

    /**
     * [
     *     ...
     *     'invoiceStatuses' => [Model\Invoice::class, 'STATUS_'],
     *     ...
     * ]
     *
     * also need to add to class phpdoc block: @method static array invoiceStatuses()
     *
     * then Valid::invoiceStatuses() can be used
     *
     * @return array
     */
    abstract protected static function getMap(): array;

    /**
     * todo: cache?
     * @param $name
     * @param $arguments
     *
     * @return array
     */
    public static function __callStatic($name, $arguments)
    {
        if (!\is_array(static::$map)) {
            static::$map = static::getMap();
        }

        if (\array_key_exists($name, static::$map)) {
            return self::grabValidValues(static::$map[$name][0], static::$map[$name][1]);
        }

        throw new \RuntimeException('Call to missing Valid::'.$name.' method');
    }

}
