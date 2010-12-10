<?php

/**
 * App_Model_Crud
 *
 * @author marcelo
 */
class App_Model_Crud extends App_Model_Abstract
{

    /**
     * Admin_Form_Abstract
     */
    protected $_form;
    /**
     * @var String
     */
    protected $_tableName;
    /**
     * @var Admin_Form_Del
     */
    protected $_delForm;
    /**
     * @var Admin_Form_Search
     */
    protected $_searchForm;


    const SAVE_OK = 'SAVE_OK';
    const SAVE_ERROR = 'SAVE_ERROR';
    const FORM_ERROR = 'FORM_ERROR';
    const REGISTER_NOT_FOUND = 'REGISTER_NOT_FOUND';
    const DELETE_CONSTRAINT_ERROR = 'DELETE_CONSTRAINT_ERROR';
    const DELETE_ERROR = 'DELETE_ERROR';
    const DELETE_OK = 'DELETE_OK';
    const DELETE_CONFIRM = 'DELETE_CONFIRM';

    protected $_crudMessages = array(
        'FORM_ERROR' => 'O Formulário contém valores inválidos',
        'SAVE_OK' => 'Registro salvo com sucesso.',
        'SAVE_ERROR' => 'Erro ao salvar registro:',
        'REGISTER_NOT_FOUND' => 'Registro não encontrado.',
        'DELETE_CONSTRAINT_ERROR' => 'O regististro não pode ser excluído pois possue vínculos.',
        'DELETE_ERROR' => 'O regististro não pode ser excluído.',
        'DELETE_OK' => 'Registro excluído com sucesso.',
        'DELETE_CONFIRM' => 'Tem certeza de que deseja excluir o seguinte registro?',
    );

    /**
     *  Constructor
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * To be overriden
     */
    public function init()
    {
        
    }

    /**
     * Get a register by id
     * @param int $id
     * @param string $table Doctrine Table name
     * @return Doctrine_Record
     */
    public function getById($id, $table = null)
    {
        if ($table === null) {
            $table = $this->getTablelName();
        }
        $register = Doctrine_Core::getTable($table)->find($id);
        if ($register == null) {
            throw new App_Exception_RegisterNotFound($this->_crudMessages[self::REGISTER_NOT_FOUND]);
        }
        return $register;
    }

    /**
     * Attempts to delete a record
     * @return bool
     */
    public function delete($id)
    {
        try {
            $record = $record = $this->getById($id);
            $record->delete();
        } catch (App_Exception_RegisterNotFound $e) {
            throw $e;
        } catch (Exception $e) {
            $message = $e->getMessage();
            $dependencyRegexp = '/Integrity\sconstraint\sviolation/';

            if (preg_match($dependencyRegexp, $message)) {
                $message = $this->_crudMessages[self::DELETE_CONSTRAINT_ERROR];
            } else {
                $message = $this->_crudMessages[self::DELETE_ERROR];
            }

            $this->addMessage($message);
            return false;
        }
        $this->addMessage($this->_crudMessages[self::DELETE_OK]);
        return true;
    }

    /**
     * Populates a form
     * @param int $id
     * @throws App_Exception_RegisterNotFound case register wont exist
     * @return Admin_Model_Brand
     */
    public function populateForm($id)
    {
        $record = $this->getById($id);
        $this->getForm()->populate($record->toArray());
        return $this;
    }

    /**
     * Return a DQL for listing registers
     * @param array $params for querying
     * @return Doctrine_Query
     */
    public function getQuery(array $params = array())
    {
        $dql = Doctrine_Query::create()
                ->from($this->getTablelName() . ' base');
        return $dql;
    }

    /**
     * Get the table name where the persistence is made
     * @return string
     */
    public function getTablelName()
    {
        return $this->_tableName;
    }

    /**
     * Get the form
     * @return App_Form_Abstract
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     * Create an record
     * @param array $values
     * @return boolean
     */
    public function create($values)
    {
        return $this->save($values);
    }

    /**
     * Update an record
     * @param array $values
     * @return boolean
     */
    public function update($values, $id)
    {
        return $this->save($values, $id);
    }

    /**
     * Check whether is valid
     * @param array $values
     * @return bool
     */
    public function isValid($values)
    {
        return $this->getForm()->isValid($values);
    }

    /**
     * Save a record
     * @param array $values
     * @param int $id
     * @return Doctrine_Record
     */
    public function save(array $values, $id = null)
    {
        if (!$this->isValid($values)) {
            $this->addErrorMessage($this->_crudMessages[self::FORM_ERROR]);
        } else {
            try {
                $record = $this->getRecord($id);
                $record->merge($values);
                $record->save();
                $this->addInfoMessage($this->_crudMessages[self::SAVE_OK]);
                $this->getForm()->setSuccess(true);
                return $record;
            } catch (App_Exception_RegisterNotFound $e) {
                throw $e;
            } catch (Exception $e) {
                $this->addErrorMessage($this->_crudMessages[self::SAVE_ERROR]);
                if (!$this->handleSimpleUkException($e, $record)
                    && !$this->handleCompositeUkException($e, $record)) {

                    $this->addErrorMessage($e->getMessage());
                }
            }
        }
        return false;
    }

    /**
     * Get or create an record
     * @param int $id
     * @return Doctrine_Record
     * @throws  App_Exception_RegisterNotFound when id is provided and record is
     *          not found
     */
    public function getRecord($id = null)
    {
        if ($id === null) {
            $record = Doctrine_Core::getTable($this->getTablelName())->create();
        } else {
            $record = $this->getById($id);
        }

        if (!$record) {
            throw new App_Exception_RegisterNotFound();
        }
        return $record;
    }

    /**
     * Handles composite uk errors.
     * @param Exception $exception
     * @param Doctrine_Record $record
     * @return bool true when finds the composite uk and handles it
     */
    public function handleCompositeUkException(Exception $exception, Doctrine_Record $record)
    {
        $message = $exception->getMessage();
        $form = $this->getForm();
        $indexes = $record->getTable()->getOption('indexes');

        foreach ($indexes as $index => $options) {

            if (isset($options['type']) && $options['type'] == 'unique') {
                $fields = $options['fields'];
                $pattern = '/[\'"]' . preg_quote($index) . '[\'"]/';

                if (preg_match($pattern, $message)) {
                    $labels = array();

                    foreach ($fields as $field => $options) {
                        $element = $form->getElement($field);

                        if ($element) {
                            $label = $element->getLabel();
                            $labels[] = '"' . ($label ? $label : $field) . '"';
                            $class = $element->getAttrib('class') . ' error';
                            $element->setAttrib('class', $class)->addError('Duplicated field');
                        } else {
                            $labels[] = '"' . $field . '"';
                        }
                    }

                    $serializedLabels = $this->serializeLabels($labels);
                    $message = sprintf('Duplicated combination of field %s', $serializedLabels);
                    $this->addErrorMessage($message);

                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Handles simple uk errors.
     * @param Exception $exception
     * @param Doctrine_Record $rec
     * @return bool true when finds the composite uk and handles it
     */
    public function handleSimpleUkException(Exception $exception, Doctrine_Record $rec)
    {
        $message = $exception->getMessage();
        $form = $this->getForm();
        $table = $rec->getTable();

        $columns = $table->getColumnNames();

        foreach ($columns as $fieldName) {
            $columnName = $table->getColumnName($fieldName);
            $definition = $table->getColumnDefinition($columnName);

            if (isset($definition['unique']) && $definition['unique'] == true) {
                $pattern = '/\b' . preg_quote('url') . '\b/';

                if (preg_match($pattern, $message)) {
                    $element = $form->getElement($fieldName);

                    if ($element) {
                        $label = $element->getLabel();
                        if (!$label) {
                            $label = $fieldName;
                        }

                        $class = $element->getAttrib('class') . ' error';
                        $element->setAttrib('class', $class)
                            ->addError(sprintf('A record already has "%s" set to "%s"',
                                    $label, $element->getValue()));
                    } else {
                        $this->addErrorMessage(sprintf('A record already has the given "%s" value', $fieldName));
                    }
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Serialize fields.
     * I.E. apple, bannana and [put the name of another fruit here] 8-0
     * @param array $labels
     * @param string $glue
     * @return string
     * @throws App_Exception when there is no label
     */
    public function serializeLabels(array $labels, $glue = ' and ')
    {
        $total = count($labels);

        if (!$total) {
            throw new App_Exception('You must have at least one label');
        } else if ($total == 1) {
            return $label[0];
        }

        $last = array_pop($labels);

        return implode(', ', $labels) . $glue . $last;
    }

}