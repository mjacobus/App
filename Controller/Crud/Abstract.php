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
     * @var App_Model_Crud
     */
    public $model;

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
     * Do Update
     * @param Zend_Controller_Request_Http $request
     * @param App_Form_Abstract $form
     */
    public function doUpdate(Zend_Controller_Request_Http $request, App_Form_Abstract $form)
    {
        $record = $this->model->update($request->getPost(), $request->getParam('id'));
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

    /**
     * create action
     */
    public function createAction()
    {
        $request = $this->getRequest();
        $form = $this->model->getForm();

        if ($request->isPost()) {
            $this->doCreate($request, $form);
        }

        $this->view->form = $form;
    }

    /**
     * read action
     */
    public function readAction()
    {

    }

    /**
     * update action
     */
    public function updateAction()
    {
        $request = $this->getRequest();
        $form = $this->model->getForm();
        $id = new Zend_Form_Element_Hidden('id');
        $id->setValue($request->getParam('id'));
        $form->addElement($id);

        if ($request->isPost()) {
            $this->doUpdate($request, $form);
        } else {
            $this->model->populateForm($request->getParam('id'));
        }

        $this->view->form = $form;
    }

    /**
     * delete action
     */
    public function deleteAction()
    {
        
    }

    /**
     * list action
     */
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
        return Zend_Controller_Front::getInstance()->getBaseUrl() . '/' . trim($append, '/');
    }

    /**
     * Index Action
     */
    public function indexAction()
    {
        $search = $this->_getAllParams();
        $dql = $this->model->getQuery($search);

        $pager = new Doctrine_Pager(
            $dql,
            $this->_getParam('page', 1),
            $this->_getParam('per-page', 10)
        );

        $this->view->pagination()->setPager($pager);

        $this->view->registers = $pager->execute();
        $this->view->records = $pager->execute();
    }

    /**
     * Pre dispatch
     */
    public function preDispatch()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
        }
    }

}
