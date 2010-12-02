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
     protected $_messages = array();

     /**
      * Sets a message
      * @param string $value message
      * @param string $key associated key for a message
      * @return App_Model_Abstract
      */
     public function setMessage($value, $key = null)
     {
        if ($key !== null) {
            $this->_messages[$key] = $message;
        } else {
            $this->_messages[] = $message;
        }
        return $this;
     }

     /**
      * Sets lots of messages
      * @param array $messages
      * @return App_Model_Abstract
      */
     public function setMessages(array $messages = array()) {
        foreach($messages as $key => $message) {
            if (is_string($key)) 
                $this->setMessage($message, $key);
            else
                $this->setMessage($message);
        }
        return $this;
     }
     
     /**
      * Add a message
      *
      * @param string $value message
      * @param string $key associated key for a message
      * @param bool $force whether should overwrite existing key
      * @return App_Model_Abstract
      */
     public function addMessage($message, $key = null, $force = false)
     {
         if ($key == null)
             $this->_messages[] = $message;
         else {
            if (($force) && (!array_key_exists($key, $this->getMessages()))) {
                throw new App_Model_Exception('Key ' . $key . ' already exists.');
            }
            $thsi->_messages[$key] = $message;
         }
         return $this;
     }

     /**
      * Gets messages
      * @return array
      */
     public function getMessages(){
        return $this->_messages;
     }

}
