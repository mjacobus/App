<?php

/**
 * Exception App_Exception_RegisterNotFound
 *
 * @author marcelo.jacobus
 */
class App_Exception_RegisterNotFound extends Zend_Exception
{

    /**
     * Construct the exception
     *
     * @param  string $msg
     * @param  int $code
     * @param  Exception $previous
     * @return void
     */
    public function __construct($msg = 'Not Found', $code = 0, Exception $previous = null)
    {
        parent::__construct($msg, $code, $previous);
    }

}