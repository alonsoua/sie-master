<?php

class Application_Model_DbTable_Bloque extends Zend_Db_Table_Abstract
{

    protected $_name = 'bloque';
    protected $_primary = 'idBloque';



    public function agregarbloque($tiempoinicio, $tiempotermino)

    {
        $db = new Zend_Db_Table('bloque');
        $data = array('tiempoInicio' => $tiempoinicio, 'tiempoTermino' => $tiempotermino);
        $db->insert($data);
    }

    public function actualizarbloque($idbloque, $tiempoinicio, $tiempotermino)

    {
        $db = new Zend_Db_Table('bloque');
        $data = array('tiempoInicio' => $tiempoinicio, 'tiempoTermino' => $tiempotermino);
        $db->update($data, 'idBloque = ' . (int)$idbloque);
    }


    public function eliminarbloqueid($idbloque)
    {
        $db = new Zend_Db_Table('bloque');
        $db->delete('idBloque =' . (int)$idbloque);
    }


    public function listarbloqueid($idbloque)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'bloque'));
        $select->joinLeft(array('e' => 'establecimiento'), 'c.idEstablecimiento = e.idEstablecimiento');
        $select->where('c.idBloque=' . $idbloque);

        return $this->fetchAll($select);
    }



}
