<?php

/**
 * Exception App_Acl_Exception
 *
 * @author marcelo.jacobus
 */
class App_Acl_Exception extends Exception
{

    public function __construct($message = null, $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}