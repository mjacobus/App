<?php

/**
 * Loads resources from a module
 *
 * @author Marcelo
 */
class App_Loader_ModuleResources implements Zend_Loader_Autoloader_Interface
{

    /**
     * The resource types
     * @var array
     */
    protected $_resourceTypes = array(
        'Form' => 'forms',
        'Menu' => 'menus',
        'Model' => 'models',
    );

    /**
     * The resource types to load
     * 
     *
     * @param array $resourceTypes 
     */
    public function construct(array $resourceTypes = array())
    {
        if (count($resourceTypes)) {
            $this->_resourceTypes = $resourceTypes;
        }
    }

    /**
     * autoload class name
     * @param string $class class name
     * @return bool
     */
    public function autoload($class)
    {
        $classParts = explode('_', $class);

        $isDefaultModule = (count($classParts) == 2);

        if ($isDefaultModule) {
            $module = 'Default';
        } else {
            $module = array_shift($classParts);
        }

        $moduleFolder = $this->classToFolderName($module);
        $pathPart[] = $this->getModulePath($moduleFolder);

        $resource = array_shift($classParts);
        if (array_key_exists($resource, $this->_resourceTypes)) {
            $pathPart[] = $this->_resourceTypes[$resource];
        } else {
            $pathPart[] = $resource;
        }

        $pathPart[] = implode(DIRECTORY_SEPARATOR, $classParts);

        $file = implode(DIRECTORY_SEPARATOR, $pathPart) . '.php';

        if (file_exists($file)) {
            require_once $file;
            return true;
        }
        
        return false;
    }

    /**
     * Get path to module
     * @param string $module module name
     * @return string
     */
    public function getModulePath($module)
    {
        return Zend_Controller_Front::getInstance()->getModuleDirectory($module);
    }

    /**
     * Convert class names to folder names
     * I.E. CamelCaseClassPart -> camel-case-class-part
     * @param string $class
     * @return string
     */
    public function classToFolderName($class)
    {
        preg_match_all('/[A-Z][a-z-]+/', $class, $matches);
        if (count($matches) && count($matches[0])) {
            $folderName = implode('-', $matches[0]);
        } else {
            $folderName = $class;
        }
        return strtolower($folderName);
    }

}