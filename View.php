<?php

/**
 * @see Zend_View
 */
require_once 'Zend/View.php';

/**
 * @author marcelo.jacobus
 */
class App_View extends Zend_View
{

    /**
     * The icons
     * @var array
     */
    protected $_icons = array();

    /**
     * Get request
     * @return Zend_Controller_Request_Abstract
     */
    public function getRequest()
    {
        return Zend_Controller_Front::getInstance()->getRequest();
    }

    /**
     * Get crud base url for the current request
     * @param string $append
     */
    public function getCrudUrl($append = '/')
    {
        $request = $this->getRequest();
        $crud = array(
            'module' => $request->getModuleName(),
            'controller' => $request->getControllerName(),
            'action' => null
        );

        return $this->url($crud, 'default', false) . $append;
    }

    /**
     * Create url
     * @return string
     */
    public function c()
    {
        return $this->getCrudUrl('/create');
    }

    /**
     * Read url
     * @param id $id
     * @return string
     */
    public function r($id)
    {
        return $this->getCrudUrl('/read/id/' . $id);
    }

    /**
     * Update url
     * @param id $id
     * @return string
     */
    public function u($id)
    {
        return $this->getCrudUrl('/update/id/' . $id);
    }

    /**
     * Delete url
     * @param id $id
     * @return string
     */
    public function d($id)
    {
        return $this->getCrudUrl('/delete/id/' . $id);
    }

    /**
     * Create image
     * @return string
     */
    public function ci()
    {
        return $this->getIcon('create');
    }

    /**
     * Read image
     * @return string
     */
    public function ri()
    {
        return $this->getIcon('read');
    }

    /**
     * Update image
     * @return string
     */
    public function ui()
    {
        return $this->getIcon('update');
    }

    /**
     * Del image
     * @return string
     */
    public function di()
    {
        return $this->getIcon('delete');
    }

    /**
     *
     * @param string $name
     * @param string $icon
     * @return App_View
     */
    public function setIcon($name, $icon)
    {
        $this->_icons[$name] = $icon;
        return $this;
    }

    /**
     * Set icons
     * @param string $name
     * @return string
     * @throws App_Exception when no icon was set
     */
    public function getIcon($name)
    {
        if (isset($this->_icons[$name])) {
            $icon = $this->_icons[$name];
            if (!preg_match('/^[\w]{3,4}:\/\/.*/', $icon)) {
                $icon = $this->baseUrl($icon);
            }
            return $icon;
        }
        return sprintf('There is no "%s" icon', $name);
    }

    /**
     * Set the icons
     * @param array $icons
     * @return App_View
     */
    public function setIcons(array $icons)
    {
        foreach ($icons as $name => $icon) {
            $this->setIcon($name, $icon);
        }
        return $this;
    }

    /**
     * Get icons
     * @return array
     */
    public function getIcons()
    {
        return $this->_icons;
    }

    /**
     * Crud Check
     * @return Zend_Form_Element_Checkbox
     */
    public function cc($id)
    {
        $check = new Zend_Form_Element_MultiCheckbox('ids');
        $check->addMultiOption($id)
            ->setDecorators(array('viewHelper'))
            ->setAttrib('class', 'crud_id');
        return $check;
    }

}