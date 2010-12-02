<?php

/**
 * Base model for the application model
 *
 * @author marcelo
 */
abstract class App_Model_Abstract
{

    /**
     * @var array messages for communicationt to the outside world
     */
    protected $_messages = array(
        'info' => array(),
        'error' => array(),
        'warning' => array()
    );

    /**
     * Add a message
     *
     * @param string $value message
     * @param string $type
     * @return App_Model_Abstract
     */
    public function addMessage($message, $type = 'info')
    {
        $this->_messages[$type][] = (string) $message;
        return $this;
    }

    /**
     * Add message of type 'info'
     * @param string $message
     * @return App_Model_Abstract
     */
    public function addInfoMessage($message)
    {
        return $this->addMessage($message);
    }

    /**
     * Get messages of type 'info'
     * @return array
     */
    public function getInfoMessages()
    {
        return $this->getMessages('info');
    }

    /**
     * Add message of type 'error'
     * @param string $message
     * @return App_Model_Abstract
     */
    public function addErrorMessage($message)
    {
        return $this->addMessage($message, 'error');
    }

    /**
     * Get messages of type 'error'
     * @return array
     */
    public function getErrorMessages()
    {
        return $this->getMessages('error');
    }

    /**
     * Add message of type 'warning'
     * @param string $message
     * @return App_Model_Abstract
     */
    public function addWarningMessage($message)
    {
        return $this->addMessage($message, 'warning');
    }

    /**
     * Get messages of type 'warning'
     * @return array
     */
    public function getWarningMessages()
    {
        return $this->getMessages('warning');
    }

    /**
     *
     * @param string $type
     * @return App_Model_Abstract
     */
    public function resetMessages($type = null)
    {
        if ($type == null) {
            foreach ($this->_messages as $type => $messages) {
                $this->_messages[$type] = array();
            }
        } else {
            $this->_messages[$type] = array();
        }
        return $this;
    }

    /**
     * Get all messages of a given type,.
     * @param string $type defaults to info
     * @return array
     */
    public function getMessages($type = 'info')
    {
        return $this->_messages[$type];
    }

    /**
     * Get messages of all the types
     * @return array
     */
    public function getAllMessages()
    {
        return $this->_messages;
    }

    /**
     * Get base uri
     * @return string
     */
    public function getBaseUrl()
    {
        $fc = Zend_Controller_Front::getInstance();
        return $fc->getBaseUrl();
    }

}
