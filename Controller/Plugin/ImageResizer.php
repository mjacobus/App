<?php

/**
 * @see WideImage
 */
require_once 'WideImage/WideImage.php';

/**
 * Plugin for Image Resizing
 *
 * @author marcelo.jacobus
 */
class App_Controller_Plugin_ImageResizer extends Zend_Controller_Plugin_Abstract
{

    /**
     *
     * @param Zend_Controller_Request_Abstract $request 
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        if ($request->getControllerName() == 'image' && $request->getModuleName() == 'default') {
            try {
                $model = App_Model_Image::getInstance();
                $model->setFile($request->getParam('file') . '.' . $request->getParam('extention'))
                    ->setWidth($request->getParam('width', 150))
                    ->setHeight($request->getParam('height', 150))
                    ->setToken($request->getParam('token'));

                $content = $model->getFileContent();
                $contentType = $model->getFileContentType();
                header("Content-type: $contentType");
                echo $content;
                exit();
            } catch (Exception $e) {
                $this->getResponse()->setHttpResponseCode(404);
            }
        }
    }

}