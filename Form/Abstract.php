<?php

/**
 * Base for crud forms
 *
 * @author marcelo
 */
class App_Form_Abstract extends Zend_Form
{

    /**
     * @var App_Model_Crud
     */
    protected $_model;
    /**
     * @var bool
     */
    protected $_success = false;
    /**
     * @var string
     */
    protected $_goTo;
    /**
     * Global decorators to apply to all elements
     * @var null|array
     */
    protected $_elementDecorators = array(
        'Description',
        'ViewHelper',
        'Errors',
        array(array('element' => 'HtmlTag'), array('tag' => 'div')),
        array('Label', array('tag' => 'div')),
        array('HtmlTag', array('tag' => 'div','class' => 'element')),
    );

    /**
     *
     * @param App_Model_Crud $model
     * @param array $options
     */
    public function __construct(App_Model_Crud $model, $options = null)
    {
        $this->setModel($model);
        parent::__construct($options);
        $this->setElementDecorators($this->_elementDecorators);
        $this->addDecorator('FormElements')
            ->addDecorator('HtmlTag', array('tag' => 'div', 'class' => 'form-elements'))
            ->addDecorator('Form');
    }

    /**
     * Constructor
     */
    public function init()
    {
        $this->setAttrib('class', 'validate');
    }

    /**
     * Get a Zend_Form_Element_Text with predefined values for date inputs
     * @param string $name
     * @param string $label
     * @param bool $required
     * @return Zend_Form_Element_Text
     */
    public function getDateElement($name, $label, $required = true)
    {
        $element = new Zend_Form_Element_Text($name);
        $element->setLabel($label)
            ->setAttrib('class', 'date')
            ->addValidator(new Zend_Validate_Date('dd/mm/yyyy'));
        $this->trim($element);
        $this->setRequired($element, $required);
        return $element;
    }

    /**
     * Add Submit Button wich is a Zend_Form_Element_Submit
     * @param string $label defaults to Salvar
     * @param string $name defaults to submit
     * @return Zend_Form_Element_Select
     */
    public function addSubmit($label = 'Salvar', $name = 'submit')
    {
        $element = new Zend_Form_Element_Submit($name);
        $element->setLabel($label);
        $this->addElement($element);
        return $this;
    }

    /**
     * Get a Zend_Form_Element_Text with some predefined values
     * @param string $name
     * @param string $label
     * @param bool $required
     * @param array $length default to array('min' => 0, 'max' => 255)
     * @return Zend_Form_Element_Text
     */
    public function getTextElement($name, $label, $required = true, $length = array('min' => 0, 'max' => 255))
    {
        $element = new Zend_Form_Element_Text($name);
        $this->trim($element)->setRequired($element, $required);
        $element->setLabel($label)
            ->addValidator(new Zend_Validate_StringLength($length))
            ->addFilter(new Zend_Filter_StringTrim());

        if (array_key_exists('max', $length)) {
            $element->setAttrib('maxlength', $length['max']);
        }

        return $element;
    }

    /**
     * Get a Zend_Form_Element_Password with some predefined values
     * @param string $name
     * @param string $label
     * @param bool $required
     * @param array $length default to array('min' => 0, 'max' => 255)
     * @return Zend_Form_Element_Text
     */
    public function getPasswordElement($name, $label, $required = true, $length = array('min' => 8, 'max' => 39))
    {
        $element = new Zend_Form_Element_Password($name);
        $this->trim($element)->setRequired($element, $required);
        $element->setLabel($label)
            ->setRequired($required)
            ->addFilter(new Zend_Filter_StringTrim());

        if (count($length)) {
            $element->addValidator(new Zend_Validate_StringLength($length));
            if (array_key_exists('max', $length)) {
                $element->setAttrib('maxlength', $length['max']);
            }
        }

        return $element;
    }

    /**
     * Get a Zend_Form_Element_Textarea with some predefined values
     * @param string $name
     * @param string $label
     * @param bool $required
     * @return Zend_Form_Element_Textarea
     */
    public function getTextareaElement($name, $label, $required = true)
    {
        $element = new Zend_Form_Element_Textarea($name);
        $this->trim($element);
        $element->setLabel($label)
            ->setRequired($required)
            ->addFilter(new Zend_Filter_StringTrim());

        $element->getDecorator('HtmlTag')->setOption('class', 'textarea');
        $element->getDecorator('Label')->setOption('class', 'textarea');
        return $element;
    }

    /**
     * Get a boolean option
     * @param string $name
     * @param string $labelTrue
     * @param array $options labels for ture and false
     * @return Zend_Form_Element_Radio
     */
    public function getBooleanElement($name, $label, $options = array('false' => 'false', 'true', 'true'), $default = null)
    {
        $element = new Zend_Form_Element_Radio($name);
        $element->setLabel($label)
            ->setRequired(true);
        $element->addMultiOption(1, $options['true']);
        $element->addMultiOption(0, $options['false']);
        $element->setAttrib('class', 'required boolean');

        if ($default !== null) {
            $element->setValue((int) $default);
        }

        $element->getDecorator('HtmlTag')->setOption('class', 'boolean');

        return $element;
    }

    /**
     * Get a Radio option
     * @param string $name
     * @param string $labelTrue
     * @return Zend_Form_Element_Radio
     */
    public function getRadioElement($name, $label = '', $required = true, $options = array())
    {
        $element = new Zend_Form_Element_Radio($name);
        $element->setLabel($label);
        $element->setAttrib('class', 'radio-multi');
        $element->getDecorator('HtmlTag')->setOption('class',
            'radio-multi height-' . count($options));
        $element->addMultiOptions($options);
        $this->setRequired($element, $required);
        return $element;
    }

    /**
     * Get a Radio option
     * @param string $name
     * @param string $labelTrue
     * @return Zend_Form_Element_Radio
     */
    public function getMultiCheckboxElement($name, $label = '', $required = true, $options = array(), array $checked = array())
    {
        $element = new Zend_Form_Element_MultiCheckbox($name);
        $element->setLabel($label);
        $element->setAttrib('class', 'radio-multi');
        $element->getDecorator('HtmlTag')->setOption('class',
            'radio-multi height-' . count($options));
        $element->addMultiOptions($options);
        $element->setValue($checked);
        $this->setRequired($element, $required);
        return $element;
    }

    /**
     * Return a Textarea with predefined values to behave like a Html Editor
     * @param string $name
     * @param string $label
     * @param bool $required
     * @return Zend_Form_Element_Textarea
     */
    public function getHtmlEditorElement($name, $label, $required = true)
    {
        $element = $this->getTextareaElement($name, $label, $required);
        $element->setAttrib('class', 'editor');
        $element->getDecorator('HtmlTag')->setOption('class', 'html-editor');
        $element->getDecorator('Label')->setOption('class', 'html-editor');
        return $element;
    }

    /**
     * Set required and change the element class attribute accordinly
     * @param string $element element name
     * @param bool $required
     * @return Form_Abstract
     */
    public function setRequired($element, $required = true)
    {
        if (is_string($element)) {
            $element = $this->{$element};
        }

        $class = $element->getAttrib('class');
        $class = str_replace('required', '', $class);
        $element->setRequired($required);

        if ($required) {
            $element->setAttrib('class', trim('required ' . $class));
        } else {
            $element->setAttrib('class', trim($class));
        }
        return $this;
    }

    /**
     * Add class hidden to the decorator
     * @param Zend_Form_Element|string $element Element or element name
     * @return Form_Abstract
     */
    public function hideElement($element)
    {
        if (false == ($element instanceof Zend_Form_Element)) {
            $element = $this->{$element};
        }

        $decorator = $element->getDecorator('Label');
        $decorator->setOption('class', trim($decorator->getOption('class') . ' hidden'));

        $decorator = $element->getDecorator('HtmlTag');
        $decorator->setOption('class', trim($decorator->getOption('class') . ' hidden'));

        return $this;
    }

    /**
     * @return inline script
     */
    public function getInlineScript()
    {
        $script = '';
        return $script;
    }

    /**
     * Add a css class to an element
     * @param string $class
     * @param Zend_Form_Element $element
     * @return Form_Abstract
     */
    public function addClass($class, Zend_Form_Element $element)
    {
        if (!$this->hasClass($class, $element)) {
            $class = $element->getAttrib('class') . " $class";
            $element->setAttrib('class', trim($class));
        }
        return $this;
    }

    /**
     * Remove a css class to an element
     * @param string $class
     * @param Zend_Form_Element $element
     * @return Form_Abstract
     */
    public function removeClass($class, Zend_Form_Element $element)
    {
        $regexp = "/\b$class\b/";
        $class = preg_replace($regexp, '', $element->getAttrib('class'));
        $element->setAttrib('class', trim($class));
        return $this;
    }

    /**
     * Checks wheter element has class
     * @param string $class
     * @param Zend_Form_Element $element
     * @return boolean
     */
    public function hasClass($class, Zend_Form_Element $element)
    {
        if (strlen($class)) {
            $regexp = "/\b$class\b/";
            if (preg_match($regexp, $element->getAttrib('class'))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get a check box (boolean, 1 or 0)
     * @return Zend_Form_Element_Checkbox
     */
    public function getCheckElement($name, $label)
    {
        $element = new Zend_Form_Element_Checkbox($name);
        $element->setLabel($label);
        $this->addClass('single-check', $element);
        return $element;
    }

    /**
     * Get hidden element
     * @return Zend_Form_Element_Hidden
     */
    public function getHiddenElement($name, $required = true)
    {
        $element = new Zend_Form_Element_Hidden($name);
        $element->setRequired($required);
        $element->setDecorators(array('ViewHelper'));

        return $element;
    }

    /**
     *
     * @param Zend_Form_Element $element
     * @return Form_Abstract
     */
    public function trim(Zend_Form_Element $element)
    {
        $element->addFilter(new Zend_Filter_StringTrim());
        return $this;
    }

    /**
     *
     * @param string $name
     * @param string $label
     * @param bool $required
     * @return Zend_Form_Element_Text
     */
    public function getEmailElement($name, $label, $required = true)
    {
        $element = $this->getTextElement($name, $label, $required);
        $element->addValidator(new Zend_Validate_EmailAddress());
        $this->addClass('email', $element);
        return $element;
    }

    /**
     * Get model
     * @param App_Form_Crud $model
     * @return App_Model_Crud
     */
    public function setModel(App_Model_Crud $model)
    {
        $this->_model = $model;
        return $this;
    }

    /**
     *
     * @return App_Model_Crud
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Set whether form was successfully processed
     * @param bool $flag
     */
    public function setSuccess($flag)
    {
        $this->_success = (bool) $flag;
    }

    /**
     * Get success of the form process
     * @return bool
     */
    public function getSuccess()
    {
        return $this->_success;
    }

    /**
     * Url to go to after processing
     * @param string $goTo
     * @return App_Form_Abstract
     */
    public function setGoTo($goTo)
    {
        $this->_goTo = $goTo;
        return $this;
    }

    /**
     * Get url to go to
     * @return string
     */
    public function getGoTo()
    {
        return $this->_goTo;
    }

}