<?php
/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 24-07-17
 * Time: 6:17 PM
 */

class Application_Model_DbTable_Codigo extends Zend_Db_Table_Abstract
{

    protected $_name = 'codigotipoensenanza';


    public function listar()
    {
        $select = $this->select();
        $select->order('idCodigoTipo ASC');
        return $this->fetchAll($select);
    }

    public function listarcodigosector()
    {
        $db   = new Zend_Db_Table('codigosector');
        $select = $db->select();
        $select->order('idCodigoSector ASC');
        return $this->fetchAll($select);
    }



    public function listartipoensenanza()
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('t' => 'tipoensenanza'), '*');
        $select->joinLeft(array('c' => 'codigotipoensenanza'),
        't.idCodigoTipo = c.idCodigoTipo');
        $select->order('c.idCodigoTipo');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }




}