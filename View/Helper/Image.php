<?php

/**
 * Gets url for resized images
 * @author marcelo.jacobus
 */
class App_View_Helper_Image extends Zend_View_Helper_BaseUrl
{

    /**
     * Display an url to the resized image
     * @param $file the file name
     * @param $dimensions widthXheight
     * @return String
     */
    public function image($file, $dimentions = '100x100')
    {
        try {
            $parts = explode('x', strtolower($dimentions));
            if (count($parts) == 2) {
                $request = App_Image::getInstance()
                        ->setFile($file)
                        ->setWidth($parts[0])
                        ->setHeight($parts[1])
                        ->getRequest();

                return $this->baseUrl('image') . $request;
            }
        } catch (Exception $e) {

        }
        throw new Zend_View_Exception("Invalid dimensions: $dimentions");
    }

}