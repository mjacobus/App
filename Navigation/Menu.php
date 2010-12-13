<?php

/**
 * Base for menus
 *
 * @author marcelo.jacobus
 */
class App_Navigation_Menu
{

    /**
     * Store the the register
     * @var array
     */
    private static $_menus = array();

    /**
     * Get a menu
     * @param string $menuClass
     * @return Zend_Navigation
     */
    public static function get($menuClass)
    {
        if (!self::hasKey($menuClass)) {
            self::set($menuClass, new $menuClass());
        }
        return self::$_menus[$menuClass];
    }

    /**
     * 
     * @param string $menuClass
     * @param string $menu
     * @param bool $override
     */
    public static function set($menuClass, Zend_Navigation $menu, $override = false)
    {
        if (!$override && self::hasKey($menuClass)) {
            throw new App_Exception(sprintf('Key "%s" is already set.', $menuClass));
        }
        self::$_menus[(string) $menuClass] = $menu;
    }

    /**
     * Whether key exists
     * @param string $key
     * @return bool
     */
    public static function hasKey($key)
    {
        return array_key_exists((string) $key, self::$_menus);
    }

}