<?php

/**
 * Log to the db table
 *
 * @author marcelo
 */
class App_Logger_Db extends App_Model_Crud
{

    /**
     * @var App_Logger_Db
     */
    public static $_instance;
    /**
     * @var string
     */
    protected $_tableName = 'ExceptionLog';
    /**
     * Mapping for ordering
     * @var array
     */
    protected $_orderMapping = array(
        'created_at' => 'created_at',
        'message' => 'message',
        'exception' => 'exception',
        'stack_trace' => 'stack_trace',
        'parameters' => 'parameters',
    );

    /**
     * Get the query for searching registers
     * @param array $params
     * @return Doctrine_Query
     */
    public function getQuery(array $params = array())
    {
        $this->setSearchFields($this->_orderMapping);

        $dql = parent::getQuery($params);

        return $dql;
    }

    /**
     * Add order to the query
     * @param Doctrine_Query $dql
     */
    public function addDefaultOrder(Doctrine_Query $dql)
    {
        $dql->orderBy('id DESC');
    }

    /**
     *
     * @return Admin_Model_ExceptionLog
     */
    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     *
     * @param Exception $exception
     */
    public static function logException(Exception $exception)
    {
        $params = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        $data = array(
            'message' => $exception->getMessage(),
            'exception' => get_class($exception),
            'stack_trace' => $exception->getTraceAsString(),
            'parameters' => print_r($params, 1)
        );

        $record = self::getInstance()->getRecord();
        $record->merge($data);
        $record->save();
    }

}

