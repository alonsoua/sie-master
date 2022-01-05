<?php

class Application_Model_DbTable_Nivel extends Zend_Db_Table_Abstract
{
    protected $_name    = 'codigogrados';
    protected $_primary = 'idCodigoGrado';



    public function getcodigos($idcodigo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('n' => 'codigogrados'));
        $select->where('n.idCodigo=?', $idcodigo);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }
}
