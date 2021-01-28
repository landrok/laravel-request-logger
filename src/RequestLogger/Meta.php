<?php

namespace Landrok\Laravel\RequestLogger;

/*
 * Meta is a stack to handle custom logs
 *
 * Before using it, you must configure meta field to true in the config
 * file. Then, you may call Meta::add('key', 'my value') anywhere in
 * your code.
 * In the log table, the meta field will contains JSON
 * '{"key":"my value"}'.
 */
class Meta
{
    /**
     * Where the data is stored before behing logged.
     *
     * @var array
     */
    private static $data = [];

    /**
     * @param  string $key
     * @param  mixed  $value It must be serializable
     */
    public static function set(string $key, $value): void
    {
        self::$data[$key] = $value;
    }

    /**
     * Get a particular index
     *
     * @return  mixed
     */
    public static function get(string $key)
    {
        return self::$data[$key] ?? null;
    }

    /**
     * Get the stack as JSON value
     */
    public static function toJson(): ?string
    {
        return count(self::$data) ? json_encode(self::$data) : null;
    }    
}
