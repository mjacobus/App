<?php

/**
 * Class for managing configurations
 *
 * @author marcelo.jacobus
 */
class App_Config
{

    /**
     *
     * @var array
     */
    private static $_config = array();

    /**
     * getter method, basically same as offsetGet().
     *
     * This method can be called from an object of type App_Config, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index - get the value associated with $index
     * @return mixed
     */
    public static function get($index = null, $throwException = false)
    {

        if ($index === null) {
            return self::$_config;
        }

        $configParts = explode('.', $index);
        $value = self::$_config;
        $valuePicked = false;

        foreach ($configParts as $key) {
            if (is_array($value) && array_key_exists($key, $value)) {
                $value = $value[$key];
                $valuePicked = true;
            }
        }

        $forbiddenValue = "$value.$key";

        if ((is_string($value) && $index != $forbiddenValue)
            || (is_array($value) && $valuePicked)) {
            return $value;
        }

        if ($throwException) {
            throw new Exception(sprintf('No config was found by key %s.', $index));
        }

        return false;
    }

    /**
     * setter method, basically same as offsetSet().
     *
     * This method can be called from an object of type App_Config, or it
     * can be called statically.  In the latter case, it uses the default
     * static instance stored in the class.
     *
     * @param string $index The location in the ArrayObject in which to store
     *   the value.
     * @param mixed $value The object to store in the ArrayObject.
     * @return void
     */
    public static function set($index, $value)
    {
        $keys = explode('.', $index);
        $first = array_shift($keys);
        $keys = array_reverse($keys);

        foreach ($keys as $key) {
            $value = array($key => $value);
        }

        if (isset(self::$_config[$first])) {
            $value = array_merge_recursive(self::$_config[$first], $value);
        }

        self::$_config[$first] = $value;
    }

}
