<?php

class App_View_Helper_Errors
{

    /**
     * The collection of messages
     * @var array
     */
    protected $_messages = array();

    /**
     *
     * @param string/array $errors
     * @return object
     */
    function errors($errors = null)
    {
        if ($errors) {
            if (is_array($errors)) {
                $this->addMessages($errors);
            } else {
                $this->addMessage($errors);
            }
        }
        return $this;
    }

    /**
     *
     * @param array $messages
     * @return Zend_View_Helper_Errors
     */
    public function addMessages($messages = array())
    {
        foreach ($messages as $message) {
            $this->addMessage($message);
        }
        return $this;
    }

    /**
     * Add a message
     * @param string $message
     * @return App_View_Helper_Errors 
     */
    public function addMessage($message)
    {
        $this->_messages[] = $message;
        return $this;
    }

    /**
     * Render the html
     * @return string
     */
    function render()
    {
        $html = '<div class="ui-widget">';
        $html .= '<div style="padding: 0pt 0.7em;" class="ui-state-error ui-corner-all">';

        foreach ($this->getMessages() as $message) {
            $html .= '<span style="float: left; margin-right: 0.3em;" class="ui-icon ui-icon-alert"></span><p class="error">' . $message . '</p>';
        }

        $html .= '</div></div>';

        return count($this->_messages) ? $html : '';
    }

    /**
     * For printing the object
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Get all messages
     * @return array
     */
    public function getMessages()
    {
        return $this->_messages;
    }

}
