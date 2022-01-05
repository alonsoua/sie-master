<?php


class Application_Model_DbTable_Region extends Zend_Db_Table_Abstract
{
    protected $_name = 'region';
    protected $_primary = 'idRegion';

    public function listarregiones()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'region'), '*');
        $select->order("p.nombreRegion");
        return $this->fetchAll($select);
    }
}
