<?php
/**
 * Handles Ajax 
 *
 * @author marcelo.jacobus
 */
class App_Response_Handler_Crud_Ajax_Json extends Zend_View_Helper_Json
{

    /**
     *
     * @param App_Form_Abstract $form
     * @param App_Controller_Crud_Abstract $controller
     * @return App_Response_Handler_Crud_Ajax_Json 
     */
    public function handle(App_Form_Abstract $form = null, App_Controller_Crud_Abstract $controller = null)
    {
        $response['success'] = $form->getSuccess();
        $response['messages'] = $form->getModel()->getAllMessages();
        $response['formErrors'] = $form->getMessages();
        $response['goTo'] = $form->getGoTo();

        $controller->getHelper('viewRenderer')->setNoRender(true);
        $controller->getHelper('layout')->disableLayout();
        $controller->getResponse()->setBody($this->json($response));
        return $this;
    }

}
