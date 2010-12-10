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
        return $this->baseUrl('/img/create.png');
    }

    /**
     * Read image
     * @return string
     */
    public function ri()
    {
        return $this->baseUrl('/img/read.png');
    }

    /**
     * Update image
     * @return string
     */
    public function ui()
    {
        return $this->baseUrl('/img/update.png');
    }

    /**
     * Del image
     * @return string
     */
    public function di()
    {
        return $this->baseUrl('/img/delete.png');
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
            ->setAttrib('class','crud_id');
        return $check;
    }

}