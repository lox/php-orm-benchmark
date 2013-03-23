<?php

require_once(__DIR__.'/../src/Loader.php');
Amiss\Loader::register();

date_default_timezone_set('Australia/Melbourne');

require_once(__DIR__.'/../doc/demo/model.php');
require_once(__DIR__.'/../doc/demo/ar.php');
require_once(__DIR__.'/lib.php');

class ActiveRecordDataTestCase extends SqliteDataTestCase
{
    public function getMapper()
    {
        $mapper = parent::getMapper();
        $mapper->objectNamespace = 'Amiss\Demo\Active';
        return $mapper;
    }
}

class TestConnector extends \Amiss\Sql\Connector
{
    public $calls = array();
    
    public function exec($statement)
    {
        $this->calls[] = array($statement, array());
    }
    
    public function prepareWithResult($statement, $result, array $driverOptions=array())
    {
        $stmt = $this->prepare($statement, $driverOptions);
        $stmt->result = $result;
        return $stmt;
    }
    
    public function prepare($statement, array $driverOptions=array())
    {
        return new TestStatement($this, $statement, $driverOptions);
    }
    
    public function getLastCall()
    {
        return $this->calls[count($this->calls)-1];
    }
}

class TestStatement
{
    public $queryString;
    public $params = array();
    public $driverOptions = array();
    public $result;
    
    public function __construct($connector, $statement, $driverOptions)
    {
        $this->connector = $connector;
        $this->driverOptions = $driverOptions;
        $this->queryString = $statement;
    }
    
    public function execute()
    {
        $this->connector->calls[] = array($this->queryString, $this->params);
        $this->params = array();
    }
    
    public function fetchColumn()
    {
        $result = $this->result;
        $this->result = null;
        return $result;
    }
}

class TestMapper implements \Amiss\Mapper
{
    public $meta;
    
    function __construct($meta=array())
    {
        $this->meta = $meta;
    }
    
    function getMeta($class)
    {
        return isset($this->meta[$class]) ? $this->meta[$class] : null;
    }

    function toObject($meta, $row, $args=null)
    {
        $object = $this->createObject($meta, $row, $args);
        $this->populateObject($meta, $object, $row);
        return $object;
    }
    
    function createObject($meta, $row, $args=null) {}
    
    function populateObject($meta, $object, $row) {}

    function fromObject($meta, $object, $context=null) {}
    
    function determineTypeHandler($type) {}
    
    /**
     * Create and populate a list of objects
     */
    function toObjects($meta, $input, $args=null)
    {
       $out = array();
       if ($input) {
           foreach ($input as $item) {
               $obj = $this->toObject($meta, $item);
               $out[] = $obj;
           }
       }
       return $out;
    }

    /**
     * Get row values from a list of objects.
     */
    function fromObjects($meta, $input, $context=null)
    {
       $out = array();
       if ($input) {
           foreach ($input as $key=>$item) {
               $out[$key] = $this->fromObject($meta, $item, $context);
           }
       }
       return $out;
    }
}

class TestTypeHandler implements \Amiss\Type\Handler
{
    public $valueForDb;
    public $valueFromDb;
    public $columnType;
    
    public function __construct($data=array())
    {
        foreach ($data as $k=>$v) $this->$k = $v;
    }
    
    function prepareValueForDb($value, $object, array $fieldInfo)
    {
        return $this->valueForDb;
    }
    
    function handleValueFromDb($value, $object, array $fieldInfo, $row)
    {
        return $this->valueFromDb;
    }
    
    function createColumnType($engine)
    {
        return $this->columnType;
    }
}
