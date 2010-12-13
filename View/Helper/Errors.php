<?php
class Zend_View_Helper_Errors {

    protected $messages = array();

    /**
     *
     * @param string/array $errors
     * @return object
     */
    function errors($errors = null) {
        if($errors) {
            if (is_array($errors)) {
                $this->addMessages($errors);
            }else {
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
    public function addMessages($messages = array()) {
        foreach($messages as $message) {
            $this->addMessage($message);
        }
        return $this;
    }



    public function addMessage($message) {
        $this->messages[] = $message;
        return $this;
    }


    function render() {
        $htmlMessage = '<ul class="messages">';

        foreach($this->messages as $message) {
            $htmlMessage .= '<li class="error">' . htmlentities($message) . '</li>';
        }

        $htmlMessage  .= '</li>';

        return count($this->messages) ? $htmlMessage : null;
    }

}
