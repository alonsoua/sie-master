<?php

class Application_Model_DbTable_AlumnosDetalle extends Zend_Db_Table_Abstract
{

    protected $_name = 'alumnosDetalle';
    protected $_primary = 'idDetalle';


    public function get($id, $ids, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnosDetalle'), '*');

        $select->where('a.idAlumno  = ' . (int)$id);
        $select->where('a.segmento  = ' . (int)$ids);
        $select->where('a.idPeriodo  = ' . (int)$idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        if (!$result) {
            return false;
        } else {

            return $result;
        }
    }

    public function listardetalle($id, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnosDetalle'), array('diasTrabajado' => new Zend_Db_Expr('SUM(diasTrabajado)'), 'diasInasistencia' => new Zend_Db_Expr('SUM(diasInasistencia)'), 'numeroatrasos' => new Zend_Db_Expr('SUM(numeroatrasos)'),'nombreApoderado'=>'nombreApoderado','observaciones'=>'observaciones'));
        $select->where('a.idAlumno  = ' . (int)$id);
        $select->where('a.idPeriodo  = ' . (int)$idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function listardetalleperiodo($id, $segmento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'alumnosDetalle'));
        $select->where('a.idAlumno  = ?' , $id);
        $select->where('a.segmento = ?' ,$segmento);
        $select->where('a.idPeriodo  = ?' , $idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;
    }


    public function agregar($diastrabajados, $diasinasistencia, $asistencia, $numeroatrasos, $observaciones, $nombreapoderado, $segmento, $idalumno, $idperiodo)
    {
        $data = array('diasTrabajado' => $diastrabajados, 'diasInasistencia' => $diasinasistencia, 'asistencia' => $asistencia, 'numeroatrasos' => $numeroatrasos, 'observaciones' => $observaciones, 'nombreApoderado' => $nombreapoderado, 'segmento' => $segmento, 'idAlumno' => $idalumno, 'idPeriodo' => $idperiodo);
        $this->insert($data);
    }


    public function cambiar($diastrabajados, $diasinasistencia, $asistencia, $numeroatrasos, $observaciones, $nombreapoderado, $segmento, $idalumno)
    {
        $data = array('diasTrabajado' => $diastrabajados, 'diasInasistencia' => $diasinasistencia, 'asistencia' => $asistencia, 'numeroatrasos' => $numeroatrasos, 'observaciones' => $observaciones, 'nombreApoderado' => $nombreapoderado, 'segmento' => $segmento
        );
        $this->update($data, 'idDetalle = ' . (int)$idalumno);
    }


    public function borrar($id)
    {

        $this->delete('idDetalle =' . (int)$id);
    }


}

