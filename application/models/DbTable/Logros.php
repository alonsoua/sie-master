<?php


class Application_Model_DbTable_Logros extends Zend_Db_Table_Abstract
{
    protected $_name = 'logros';
    protected $_primary = 'idLogro';

    public function listar()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'logros'), '*');
        return $this->fetchAll($select);
    }
}
