<?php


class Application_Model_DbTable_Equivalencia extends Zend_Db_Table_Abstract
{
    protected $_name = 'equivalenciaporcentaje';
    protected $_primary = 'idEquivalencia';


    public function get($id)
    {

        $row = $this->fetchAll('idEquivalencia = ' . (int)$id);
        if (!$row) {
            return 0;
        }

        return $row->toArray();
    }

    public function listar($idEstablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'equivalenciaporcentaje'), array('idEquivalencia', 'porcentaje_inicio', 'porcentaje_final', 'equivalencia_nota', 'tiempoPonderacion'));
        $select->joinLeft(array('n' => 'niveleslogros'), 'n.idEquivalencia = p.idEquivalencia', array('nivelLogro'));
        $select->where('p.idEstablecimiento = ?', $idEstablecimiento);
        $select->where('p.idPeriodo= ?', $idperiodo);
        $select->where('p.tipoEquivalencia= 1');
        return $this->fetchAll($select);
    }

    public function listarperiodo($idEstablecimiento, $idperiodo, $tiempo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'equivalenciaporcentaje'), array('idEquivalencia', 'porcentaje_inicio', 'porcentaje_final', 'equivalencia_nota', 'tiempoPonderacion'));
        $select->joinLeft(array('nl' => 'niveleslogros'), 'p.idEquivalencia =nl.idEquivalencia');
        $select->where('p.idEstablecimiento = ?', $idEstablecimiento);
        $select->where('p.idPeriodo= ?', $idperiodo);
        $select->where('p.tiempoPonderacion= ?', $tiempo);
        $select->where('p.tipoEquivalencia= 1');
        return $this->fetchAll($select)->toArray();
    }

    public function listarperiodos($idEstablecimiento, $idperiodo, $tiempo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'equivalenciaporcentaje'), array('idEquivalencia', 'porcentaje_inicio', 'porcentaje_final', 'equivalencia_nota', 'tiempoPonderacion'));
        $select->joinLeft(array('nl' => 'niveleslogros'), 'p.idEquivalencia =nl.idEquivalencia');
        $select->where('p.idEstablecimiento = ?', $idEstablecimiento);
        $select->where('p.idPeriodo= ?', $idperiodo);
        $select->where('p.tiempoPonderacion= ?', $tiempo);
        $select->where('p.tipoEquivalencia= 1');
        $select->group('nl.nivelLogro');
        return $this->fetchAll($select)->toArray();
    }

    public function listarniveleslogro($idEstablecimiento, $idperiodo, $tiempo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'equivalenciaporcentaje'), array('idEquivalencia', 'porcentaje_inicio', 'porcentaje_final', 'equivalencia_nota', 'tiempoPonderacion'));
        $select->joinLeft(array('nl' => 'niveleslogros'), 'p.idEquivalencia =nl.idEquivalencia');
        $select->where('p.idEstablecimiento = ?', $idEstablecimiento);
        $select->where('p.idPeriodo= ?', $idperiodo);
        $select->where('p.tiempoPonderacion= ?', $tiempo);
        $select->where('p.tipoEquivalencia= 1');
        $select->group('nl.nivelLogro');
        return $this->fetchAll($select)->toArray();
    }

    public function listarperiodopre($idEstablecimiento,$idperiodo,$tiempo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'equivalenciaporcentaje'), array('idEquivalencia', 'porcentaje_inicio', 'porcentaje_final', 'equivalencia_nota', 'tiempoPonderacion'));
        $select->joinLeft(array('nl' => 'niveleslogros'), 'p.idEquivalencia =nl.idEquivalencia');
        $select->where('p.idEstablecimiento = ?', $idEstablecimiento);
        $select->where('p.idPeriodo= ?', $idperiodo);
        $select->where('p.tiempoPonderacion= ?', $tiempo);
        $select->where('p.tipoEquivalencia= 2');
        return $this->fetchAll($select)->toArray();
    }

}
