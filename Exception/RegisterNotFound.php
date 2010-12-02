<?php

/**
 * Exception App_Exception_RegisterNotFound
 *
 * @author marcelo.jacobus
 */
class App_Exception_RegisterNotFound extends Exception
{

    public function __construct($message = "Registro não encontrado.", $code = null, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}