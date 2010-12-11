<?php
/**
 * Handles Ajax 
 *
 * @author marcelo.jacobus
 */
class App_Response_Handler_Crud_Http
{

    /**
     *
     * @param App_Form_Abstract $form
     * @param App_Controller_Crud_Abstract $controller
     * @return App_Response_Handler_Crud_Ajax_Json 
     */
    public function handle(App_Form_Abstract $form = null, App_Controller_Crud_Abstract $controller = null)
    {
        $model = $form->getModel();
        $view = $controller->view;

        if ($form->getSuccess()) {
            $messager = $view->flash();
        } else {
            $messager = $view->errors();
        }

        foreach ($model->getAllMessages() as $type => $messages) {
            $messager->addMessages($model->getMessages($type));
        }

        if ($form->getSuccess()) {
            $helper = $controller->getHelper('redirector');
            $helper->gotoUrl($form->getGoTo(),array('prependBase' => false));
        }
    }

}
