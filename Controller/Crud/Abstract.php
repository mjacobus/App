<?php

/**
 * Basis for crud
 *
 * @author marcelo
 */
abstract class App_Controller_Crud_Abstract extends Zend_Controller_Action
{

    /**
     *
     * @var string
     */
    protected $_absoluteBaseUrl;

    /**
     *
     * @param Doctrine_Record $record
     * @param App_Form_Abstract $form 
     */
    public function postCreate(Doctrine_Record $record, App_Form_Abstract $form)
    {
        $this->postUpdate($record, $form);
    }

    /**
     *
     * @param Doctrine_Record $record
     * @param App_Form_Abstract $form
     */
    public function postUpdate(Doctrine_Record $record, App_Form_Abstract $form)
    {
        $form->setGoTo($this->getAbsoluteBaseUrl(implode('/', array(
                    $this->getRequest()->getModuleName(),
                    $this->getRequest()->getControllerName()
                ))));
    }

    /**
     *
     * @param Doctrine_Record $record
     * @param App_Form_Abstract $form 
     */
    public function postDelete(Doctrine_Record $record, App_Form_Abstract $form)
    {
        $this->postUpdate($record, $form);
    }

    /**
     * Do Create
     * @param Zend_Controller_Request_Http $request
     * @param App_Form_Abstract $form
     */
    public function doCreate(Zend_Controller_Request_Http $request, App_Form_Abstract $form)
    {
        $record = $this->model->create($request->getPost());
        if ($record) {
            $this->postCreate($record, $form);
        }
        $this->setResponseHandler($request, $form);
    }

    /**
     *
     * @param Zend_Controller_Request_Http $request
     * @param App_Form_Abstract $form 
     */
    public function setResponseHandler(Zend_Controller_Request_Http $request, App_Form_Abstract $form)
    {
        if ($request->isXmlHttpRequest()) {
            $handler = new App_Response_Handler_Crud_Ajax_Json();
        } else {
            $handler = new App_Response_Handler_Crud_Http();
        }
        $handler->handle($form, $this);
    }

    public function indexAction()
    {
        
    }

    public function createAction()
    {
        $request = $this->getRequest();
        $form = $this->model->getForm();
        if ($request->isPost()) {
            $form->setSuccess(true);
            $this->doCreate($request, $form);
        }
        if (!$request->isXmlHttpRequest()) {
            $this->view->form = $form;
        }
    }

    public function readAction()
    {

    }

    public function updateAction()
    {
        
    }

    public function deleteAction()
    {
        
    }

    public function listAction()
    {

    }

    /**
     * Return url in format protocol://host:port
     * @return string
     */
    public function getAbsoluteBaseUrl($append = '/')
    {
        if ($this->_absoluteBaseUrl == null) {

            $protocol = explode('/', $_SERVER['SERVER_PROTOCOL']);
            $protocol = strtolower($protocol[0]) . '://';

            $port = ':' . $_SERVER['SERVER_PORT'];

            if (($protocol == 'http://' && $port == ':80') || ($protocol == 'https://' && $port == ':443')) {
                $port = '';
            }

            $host = $_SERVER['HTTP_HOST'];
            $this->_absoluteBaseUrl = $protocol . $host . $port;
        }

        return $this->_absoluteBaseUrl . $this->getBaseUrl($append);
    }

    /**
     * Get relative base url.
     * @param string $append
     * @return string
     */
    public function getBaseUrl($append)
    {
        return Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . trim($append,'/');
    }

}
