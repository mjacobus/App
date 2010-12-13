<?php

/**
 * Parent class
 *
 * @see Zend_Navigation_Page_Mvc
 */
require_once 'Zend/Navigation/Page/Mvc.php';

/**
 * Description of App_Navigation_Page_Mvc
 *
 * @author marcelo.jacobus
 */
class App_Navigation_Page_Mvc extends Zend_Navigation_Page_Mvc
{

    /**
     * Returns whether page should be considered active or not
     *
     * This method will compare the page properties against the request object
     * that is found in the front controller.
     *
     * @param  bool $recursive  [optional] whether page should be considered
     *                          active if any child pages are active. Default is
     *                          false.
     * @return bool             whether page should be considered active or not
     */
    public function isActive($recursive = false)
    {
        if (!$this->_active) {
            $front = Zend_Controller_Front::getInstance();
            $reqParams = $front->getRequest()->getParams();

            if (!array_key_exists('module', $reqParams)) {
                $reqParams['module'] = $front->getDefaultModule();
            }

            $myParams = $this->_params;

            if (null !== $this->_module) {
                $myParams['module'] = $this->_module;
            } else {
                $myParams['module'] = $front->getDefaultModule();
            }

            if (null !== $this->_controller) {
                $myParams['controller'] = $this->_controller;
            } else {
                $myParams['controller'] = $front->getRequest()->getControllerName();
            }

            if (null !== $this->_action) {
                $myParams['action'] = $this->_action;
            } else {
                $myParams['action'] = $front->getRequest()->getActionName();
            }

            if (count(array_intersect_assoc($reqParams, $myParams)) ==
                count($myParams)) {
                $this->_active = true;
                return true;
            }
        }

        return parent::isActive($recursive);
    }

}
?>
