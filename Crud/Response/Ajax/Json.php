<?php
/**
 * Handles Ajax 
 *
 * @author marcelo.jacobus
 */
class App_Crud_Response_Ajax_Json extends Zend_View_Helper_Json
{

    /**
     *
     * @param App_Features_Abstract $feature
     * @return App_Crud_Response_Ajax_Json
     */
    public function handle(App_Features_Abstract $feature)
    {
        $response['success'] = $feature->getSuccess();
        $response['messages'] = $feature->getModel()->getAllMessages();
        $response['formErrors'] = $feature->getMessages();
        $response['goTo'] = $feature->getGoTo();

        $controller = $feature->getController();
        $controller->getHelper('viewRenderer')->setNoRender(true);
        $controller->getHelper('layout')->disableLayout();
        $controller->getResponse()->setBody($this->json($response));
        return $this;
    }

}
