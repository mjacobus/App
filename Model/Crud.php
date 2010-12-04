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
    const REGISTER_NOT_FOUND = 'REGISTER_NOT_FOUND';
    const DELETE_CONSTRAINT_ERROR = 'DELETE_CONSTRAINT_ERROR';
    const DELETE_ERROR = 'DELETE_ERROR';
    const DELETE_OK = 'DELETE_OK';
    const DELETE_CONFIRM = 'DELETE_CONFIRM';

    protected $_crudMessages = array(
        'SAVE_OK' => 'Registro salvo com sucesso.',
        'SAVE_ERROR' => '* Erro ao salvar registro:',
        'REGISTER_NOT_FOUND' => 'Registro não encontrado.',
        'DELETE_CONSTRAINT_ERROR' => 'O regististro não pode ser excluído pois possue vínculos.',
        'DELETE_ERROR' => 'O regististro não pode ser excluído.',
        'DELETE_OK' => 'Registro excluído com sucesso.',
        'DELETE_CONFIRM' => 'Tem certeza de que deseja excluir o seguinte registro?',
    );
    /**
     * Uk Exception patterns
     * @var array
     */
    protected $_ukExceptionPatterns = array(
        "/validator\sfailed\son\s(\w+)\s\(unique\)/i",
        "/Duplicate\sentry\s'.+'\sfor\skey\s'(.+)'/i"
    );
    /**
     * Mapping of unique keys
     * @var array
     */
    protected $_ukMapping = array(
        'filename' => array(
            'field' => 'filename',
            'label' => 'Nome do Arquivo',
            'message' => 'Um registro ja existe com "{label}" igual a "{value}" '
        )
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
     * Try to save a record
     * @param array $values
     * @return Doctrine_Record|false false when its not ok and the record id when it is ok.
     */
    public function save(array $values, $id = null)
    {
        try {
            if ($this->isValid($values)) {
                $values = $this->getForm()->getValues();
                $record = $this->persist($values, $id);
                $this->addMessage($this->_crudMessages[self::SAVE_OK]);
                return $record;
            }
        } catch (Exception $e) {
            $this->addErrorMessage($this->_crudMessages[self::SAVE_ERROR]);

            $exception = $e->getMessage();

            foreach ($this->_ukExceptionPatterns as $pattern) {
                if (preg_match($pattern, $exception, $matches)) {
                    if (array_key_exists(1, $matches)) {
                        $field = $matches[1];
                        if (array_key_exists($field, $this->_ukMapping)) {
                            $recordField = $this->_ukMapping[$field]['field'];
                            $message = $this->replace($this->_ukMapping[$field]['message'],
                                    array(
                                        '{field}' => $recordField,
                                        '{label}' => $this->_ukMapping[$field]['label'],
                                        '{value}' => $values[$recordField],
                                ));
                            try {
                                $this->getForm()->getElement($recordField)->addError($message);
                            } catch (Exception $formException) {
                                $this->addErrorMessage($message);
                            }
                        } else {
                            $this->addErrorMessage('Registro já existe.');
                        }
                    }
                }
            }
        }
        return false;
    }

    /**
     * Get a register by id
     * @param string $table Doctrine Table name
     * @param int $id
     * @return Doctrine_Record
     */
    public function getById($table, $id)
    {
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
            $record = $record = $this->getById($this->getTablelName(), $id);
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
        $record = $this->getById($this->getTablelName(), $id);
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
        $dql = Doctrine_Core::getTable($this->getTablelName())
                ->createQuery()->orderBy('name ASC');

        if (array_key_exists('search', $params) && $params['search']) {
            $search = $params['search'];
            $dql->addWhere('name like ?', "%$search%");
        }
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
     *
     * @return App_Form
     */
    public function getForm()
    {
        return $this->_form;
    }

    /**
     *
     * @param array $values
     */
    public function create($values)
    {
        return $this->save($values);
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
     * Persist a Record
     * @param array $values the values to persist
     * @param int $id the record id
     * @throws Exception
     * @return Doctrine_Record
     */
    public function persist($values, $id = null)
    {
        if ($id !== null) {
            $record = $this->getById($this->getTablelName(), $id);
        } else {
            $record = Doctrine_Core::getTable($this->getTablelName())->create();
        }
        $record->fromArray($values);
        $record->save();
        return $record;
    }

}