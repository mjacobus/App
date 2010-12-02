<?php

/**
 * Basis for crud
 *
 * @author marcelo
 */
class App_Controller_Crud_Abstract extends Zend_Controller_Action
{

    /**
     * Post Create Rotine
     * @param Doctrine_Record $record 
     */
    public function postCreate(Doctrine_Record $record)
    {
        $this->view->flash($this->model->getMessages());
        $this->_redirect(implode('/', array(
                $this->getRequest()->getModuleName(),
                $this->getRequest()->getControllerName(),
            )));
    }

    /**
     * Post Update
     * @param Doctrine_Record $record 
     */
    public function postUpdate(Doctrine_Record $record)
    {
        $this->postUpdate($record);
    }

    /**
     * Post Delete
     * @param Doctrine_Record $record
     */
    public function postDelete(Doctrine_Record $record)
    {
        $this->postUpdate($record);
    }

    /**
     * Do Create
     * @param Zend_Request_Http_Request $request 
     */
    public function doCreate(Zend_Controller_Request_Http $request)
    {
        $record = $this->model->create($request->getPost());
        if ($record) {
            $this->postCreate($record);
        } else {
            $this->view->errors($this->model->getMessages());
        }
    }

    /**
     *
     * @param Zend_Request_Http_Request $request 
     */
    public function doAjaxCreate(Zend_Controller_Request_Http $request)
    {
        $response = array(
            'success' => false,
            'messages' => array(),
            'formErrors' => array(),
        );
        
        $record = $this->model->create($request->getPost());
        if ($record) {
            $response['success'] = true;
        } else {
            $response['formErrors'] = $this->model->getForm()->getErrorMessages();
        }
        $response['message'] = $this->model->getMessages();

        $this->_helper->json($response);
    }

    public function indexAction()
    {
        
    }

    public function createAction()
    {
        $request = $this->getRequest();
        if ($request->isXmlHttpRequest()) {
            $this->_helper->layout->disableLayout();
            if ($request->isPost()) {
                $this->doAjaxCreate($request);
            }
        } else if ($request->isPost()) {
            $this->doCreate($request);
        }
        $this->view->form = $this->model->getForm();
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

}
