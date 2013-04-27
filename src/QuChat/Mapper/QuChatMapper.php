<?php

namespace QuChat\Mapper;

use QuChat\Db\Adapter\DbAdapterAwareInterface;

use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate;
use Zend\Db\Sql\Sql;
use Zend\Stdlib\Hydrator\Reflection as ReflectionHydrator;
use Zend\Paginator\Paginator;


class QuChatMapper  implements DbAdapterAwareInterface
{
    protected $dbAdapter;
    protected $tableName = 'qu-chat';
    protected $Order;
    protected $where;
    protected $select;

    public function all($TableName = null)
    {
        $select = $this->selectByWhereByOrder($TableName);
        $stmt   = $this->sql()->prepareStatementForSqlObject($select);
        $result = $this->resultSet()->initialize($stmt->execute());

        return  $result;
    }
    public function row($TableName = null)
    {
        $select = $this->selectByWhereByOrder($TableName);
        $stmt = $this->sql()->prepareStatementForSqlObject($select);
        $result = $this->resultSet()->initialize($stmt->execute())->current();

        return $result;
    }

    public function getRow($array = array(),$order = null)
    {
        $this->Order($order);
        $this->where($array);
        return $this->row();
    }

    public function getAll($array = array(),$order = null)
    {
        $this->Order($order);
        $this->where($array);
        return $this->all();
    }

    public  function onInsert($data)
    {
        $sql       = new Sql($this->getDbAdapter());
        $insert    = $sql->insert($this->getTableName());
        $insert->values($data);
        $statement = $sql->prepareStatementForSqlObject($insert);
        $result    = $statement->execute();
        return $result->getGeneratedValue();
    }
    public function onUpdate($data,$where = null)
    {
        $sql    =  new Sql($this->getDbAdapter());
        $update = $sql->update($this->getTableName());
        $update->set($data)->where($where);
        $statement = $sql->prepareStatementForSqlObject($update);
        return $statement->execute();
    }
    protected function sql()
    {
        return  new Sql($this->getDbAdapter());
    }

    protected function getSelect()
    {
        if(!$this->select){
            $this->setSelect($this->getTableName());
        }
        return $this->select;
    }

    protected function setSelect($TableName)
    {
        $this->select = $this->sql()->select($TableName);
        return $this;
    }

    protected function resultSet()
    {
      return new ResultSet(ResultSet::TYPE_ARRAY);
    }

    public  function Order($Order)
    {
        $this->Order = $Order;
        return $this;
    }

    public function where(array $where)
    {
        $this->where = $where;
        return $this;
    }

    protected function selectByWhereByOrder($TableName)
    {
        $select = $this->sql()->select($TableName ?: $this->getTableName());
        $sel =  $select;
        if($this->Order) $sel = $select->order($this->Order);
        if($this->where) $sel = $select->where($this->where);
        if($this->where and $this->Order) $sel = $select->where($this->where)->order($this->Order);
        return $sel;
    }

    public function onRemove($where = null)
    {
        $sql       = new Sql($this->getDbAdapter());
        $delete = $sql->delete($this->getTableName());
        $delete->where($where);
        $statement = $sql->prepareStatementForSqlObject($delete);
        return $statement->execute();
    }

    public function getDbAdapter()
    {
        return  $this->dbAdapter;
    }

    public function setDbAdapter(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
        return $this;
    }


    public function getTableName()
    {
        return $this->tableName;
    }
    public function setTableName($tableName)
    {
        $this->tableName =  $tableName;
        return $this;
    }
}
