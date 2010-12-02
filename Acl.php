<?php

/**
 * Class for managing permissions
 *
 * @author marcelo.jacobus
 */
class App_Acl extends Zend_Acl
{

    /**
     * Instance of App_Acl
     * @var App_Acl
     */
    private static $_instance;
    /**
     * Path for the Zend_Config_Ini file
     * @var string
     */
    private static $_config;

    /**
     *
     * @param <type> $configFile
     * @throws App_Acl_Exception
     */
    public static function setConfigFile($configFile)
    {
        if (self::$_instance !== null) {
            require 'App/Acl/Exception.php';
            throw new App_Acl_Exception('App_Acl was already instanciated.');
        }
        if (!file_exists($configFile)) {
            require 'App/Acl/Exception.php';
            throw new App_Acl_Exception("File '$configFile' does not exist.");
        }
        self::$_config = $configFile;
    }

    /**
     * Get the config file for instantiating the object
     * @return string
     */
    public static function getConfigFile()
    {
        if (self::$_config == null) {
            self::setConfigFile(APPLICATION_PATH . '/configs/acl.ini');
        }
        return self::$_config;
    }

    /**
     * Constructor
     */
    private function __construct()
    {
        $config = new Zend_Config_Ini(self::getConfigFile());

        $roles = array();
        foreach ($config->roles as $role => $inherit) {
            if (empty($inherit)) {
                $inherit = null;
            }
            $this->addRole($role, $inherit);
        }

        foreach ($config->resources as $resourceName => $resource) {

            $this->addResource($resourceName);

            $this->deny($roles, $resourceName);

            if ($resource->allow) {
                $roles = explode(',', $resource->allow);
                foreach ($roles as $role) {
                    $this->allow(trim($role), $resourceName);
                }
            }

            if ($resource->deny) {
                $roles = explode(',', $resource->deny);
                foreach ($roles as $role) {
                    $this->deny(trim($role), $resourceName);
                }
            }
        }
    }

    /**
     * Get the instance
     * @return App_Acl
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Check whether user has access to the given resource
     * @param string $resource
     * @return bool
     */
    public static function canAccess($resource)
    {
        $role = 'guest';
        if (Admin_Model_Authentication::isLogged()) {
            $role = Admin_Model_Authentication::getIdentity()->Role->name;
        }

        $acl = self::getInstance();
        if ($acl->has($resource)) {
            return $acl->isAllowed($role, $resource);
        }
        return true;
    }

}