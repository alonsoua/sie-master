<?php

class Application_Model_DbTable_Periodo extends Zend_Db_Table_Abstract
{

    protected $_name = 'periodo';


    public function get($idPeriodo)
    {
        $idPeriodo = (int)$idPeriodo;
        //$this->fetchRow devuelve fila donde idPeriodo = $idPeriodo
        $row = $this->fetchRow('idPeriodo = ' . $idPeriodo);
        if (!$row) {
            throw new Exception("Could not find row $idPeriodo");
        }

        return $row->toArray();
    }

    public function getname($nombrePeriodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('e' => 'periodo'));
        $select->where('e.nombrePeriodo = ' . (int)$nombrePeriodo);
        $rows = $this->fetchAll($select);
        if (!$rows) {
            return false;
        } else {
            return $rows->toArray();
        }


    }

    /**
     *  agrega un nuevo Periodo a la base de datos
     * @param  $idPeriodo
     * @param  $fecha
     */
    public function agregar($nombre)

    {
        $nombreint = (int)$nombre;
        $data = array('nombrePeriodo' => $nombreint);
        //$this->insert inserta nuevo Establecimiento
        $this->insert($data);
    }

    /**
     * modifica los datos del Periodo idPeriodo= $idperiodo
     * @param  $idPeriodo
     * @param  $fecha
     */
    public function cambiar($idPeriodo, $nombre)
    {
        $nombreint = (int)$nombre;
        $data = array('nombrePeriodo' => $nombreint);
        //$this->update cambia datos de Establecimiento con RBD= $RBD
        $this->update($data, 'idPeriodo = ' . (int)$idPeriodo);
    }

    /**
     * borra el Establecimientocon idPeriodo= $idPeriodo
     * @param  $id
     */
    public function borrar($idPeriodo)
    {

        $this->delete('idPeriodo =' . (int)$idPeriodo);

    }

    public function listar()
    {
        //devuelve todos los registros de la tabla

        $select = $this->select();
        $select->order('nombrePeriodo DESC');
        return $this->fetchAll($select);
    }

    public function nombreperiodo($id)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('e' => 'periodo'));

        $select->where('e.idPeriodo=' . $id);

        return $this->fetchAll($select);


    }


    //Calendario

    public function agregarcalendario($idestablecimiento, $codigo, $descripcion, $fechainicio, $fechatermino, $idperiodo, $idcurso, $tipo)

    {
        $db = new Zend_Db_Table('calendarios');
        $data = array(
            'idEstablecimiento' => $idestablecimiento,
            'codigoCalendario' => $codigo,
            'descripcionCalendario' => $descripcion,
            'fechaInicioClase' => $fechainicio,
            'fechaTerminoClase' => $fechatermino,
            'idPeriodo' => $idperiodo,
            'idCursos' => $idcurso,
            'tipoCalendario' => $tipo);
        $db->insert($data);
    }


    public function actualizarcalendario($idcalendario, $codigo, $descripcion, $fechainicio, $fechatermino)

    {
        $db = new Zend_Db_Table('calendarios');
        $data = array(
            'codigoCalendario' => $codigo,
            'descripcionCalendario' => $descripcion,
            'fechaInicioClase' => $fechainicio,
            'fechaTerminoClase' => $fechatermino);
        $db->update($data, 'idCalendario = ' . (int)$idcalendario);
    }

    public function eliminarevento($id)
    {
        $db = new Zend_Db_Table('calendarioEvento');
        $db->delete('idEvento =' . (int)$id);

    }


    public function validarcalendario($idestablecimiento, $idperiodo, $idcurso, $tipo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'calendarios'));
        $select->joinLeft(array('e' => 'establecimiento'), 'c.idEstablecimiento = e.idEstablecimiento');
        $select->where('c.idEstablecimiento=' . $idestablecimiento);
        $select->where('c.tipoCalendario=' . $tipo);
        $select->where('c.idPeriodo=' . $idperiodo);
        if ($tipo == 2) {
            $select->where('c.idCursos=' . $idcurso);
        }
        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return true;
        } else {
            return false;
        }
    }

    public function listarcalendario($idperiodo, $tipo, $idestablecimiento)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'calendarios'));
        $select->joinLeft(array('cu' => 'cursosactual'), 'cu.idCursos = c.idCursos');
        $select->joinLeft(array('e' => 'establecimiento'), 'c.idEstablecimiento = e.idEstablecimiento');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');
        $select->where('c.idPeriodo=' . $idperiodo);
        $select->where('c.tipoCalendario=' . $tipo);

        if ($idestablecimiento != null) {
            $select->where('c.idEstablecimiento=?', $idestablecimiento);

        }

        return $this->fetchAll($select);
    }


    public function listarcalendarioevento($idcalendario, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'calendarioEvento'))
            ->joinLeft(array('t' => 'tipoEvento'), 't.idTipoEvento = c.tipoEvento')
            ->joinLeft(array('e' => 'calendarios'), 'c.idCalendario = e.idCalendario')
            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = e.idEstablecimiento');
        $select->where('e.idPeriodo=' . $idperiodo);
        $select->where('c.idCalendario=' . $idcalendario);
        $select->order('c.fechaEvento ASC');

        return $this->fetchAll($select);
    }

    public function listarcalendarioeventocurso($idcalendario, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'calendarioEvento'))
            ->joinLeft(array('t' => 'tipoEvento'), 't.idTipoEvento = c.tipoEvento')
            ->joinLeft(array('e' => 'calendarios'), 'c.idCalendario = e.idCalendario')
            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = e.idEstablecimiento');
        $select->where('e.idPeriodo=' . $idperiodo);
        $select->where('c.idCalendario=' . $idcalendario);
        $select->order('c.fechaEvento ASC');

        return $this->fetchAll($select);
    }


    public function getcalendario($idcalendario, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('ce' => 'calendarios'));
        $select->where('ce.idCalendario = ' . (int)$idcalendario);
        $select->where('ce.idPeriodo= ' . (int)$idperiodo);
        $rows = $this->fetchAll($select);
        if (!$rows) {
            return false;
        } else {
            return $rows->toArray();
        }


    }

    public function getcalendarioestablecimiento($idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('ce' => 'calendarios'));
        $select->where('ce.idEstablecimiento = ?', $idestablecimiento);
        $select->where('ce.tipoCalendario=1');
        $select->where('ce.idPeriodo=? ', $idperiodo);
        $rows = $this->fetchAll($select);
        if (!$rows) {
            return false;
        } else {
            return $rows->toArray();
        }


    }

    public function geteventos($id, $idperiodo,$tipo,$fechainicio,$fechatermino)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarios'));
        $select->join(array('ev'=>'calendarioEvento'),'ev.idCalendario=ce.idCalendario');
        if($tipo==2){
            $select->where('ce.idCursos = ?', $id);
        }else{
            $select->where('ce.idEstablecimiento = ?', $id);
        }
        $select->where('ev.fechaEvento>=?',$fechainicio);
        $select->where('ev.fechaEvento<=?',$fechatermino);
        $select->where('ce.tipoCalendario=?',$tipo);
        $select->where('ce.idPeriodo=? ', $idperiodo);
        return $this->fetchAll($select)->toArray();



    }


    public function gettipoevento()
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'tipoEvento'));
        return $this->fetchAll($select);
    }

    public function getfecha($idcalendario, $tipo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarios'), '*');
        $select->where('ce.idCalendario= ' . $idcalendario);
        $select->where('ce.tipoCalendario= ' . $tipo);
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function getdiaseventos($idcalendario)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarioEvento'), array('fechaEvento'));
        $select->where('ce.idCalendario = ' . $idcalendario);
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function getdiaseventoscurso($idcalendarios)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarioEvento'), array('fechaEvento'));
        $select->where('ce.idCalendario IN(?)', $idcalendarios);
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


    public function agregarevento($idcalendarioestablecimiento, $nombre, $fecha, $tipo)

    {
        $db = new Zend_Db_Table('calendarioEvento');
        $data = array('idCalendario' => $idcalendarioestablecimiento, 'nombreEvento' => $nombre, 'fechaEvento' => $fecha, 'tipoEvento' => $tipo);
        $db->insert($data);
    }

    public function actualizarevento($idevento, $nombre, $fecha, $tipo)

    {
        $db = new Zend_Db_Table('calendarioEvento');
        $data = array('nombreEvento' => $nombre, 'fechaEvento' => $fecha, 'tipoEvento' => $tipo);
        $db->update($data, 'idEvento = ' . (int)$idevento);
    }

    public function getevento($idevento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('ce' => 'calendarioEvento'));
        $select->where('ce.idEvento = ' . (int)$idevento);
        $rows = $this->fetchAll($select);
        if (!$rows) {
            return false;
        } else {
            return $rows->toArray();
        }


    }


    public function agregarferiados($nombre, $fecha, $idperiodo)

    {
        $db = new Zend_Db_Table('feriados');
        $data = array('nombreFeriado' => $nombre, 'fechaFeriado' => $fecha, 'idPeriodo' => $idperiodo);
        $db->insert($data);
    }

    public function getferiados($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('f' => 'feriados'));
        $select->where('f.idPeriodo=?', $idperiodo);
        return $this->fetchAll($select)->toArray();
    }

    public function validarferiado($idcalendarioestablecimiento, $fecha)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarioEvento'));
        $select->where('ce.idCalendario = ' . (int)$idcalendarioestablecimiento);
        $select->where('ce.fechaEvento = ?', $fecha);
        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return true;
        } else {
            return false;
        }


    }

    public function getfechacurso($idcurso, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarios'), '*');
        //$select->joinLeft(array('cu' => 'cursosactual'), 'cu.idEstablecimiento = ce.idEstablecimiento');
        $select->where('ce.idCursos= ' . $idcurso);
        $select->where('ce.idPeriodo= ' . $idperiodo);
        return $this->fetchAll($select)->toArray();

    }



    //JSON EXPORT

    public function getdiaseventosJSON($idperiodo, $idestablecimiento)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarioEstablecimiento'), '*')
            ->joinLeft(array('cev' => 'calendarioEvento'), 'cev.idCalendarioEstablecimiento=ce.idCalendarioEstablecimiento')
            ->joinLeft(array('pe' => 'periodo'), 'pe.idPeriodo=ce.idPeriodo')
            ->joinLeft(array('te' => 'tipoEvento'), 'te.idTipoEvento=cev.tipoEvento');
        $select->where('ce.idEstablecimiento = ' . $idestablecimiento);
        $select->where('ce.idPeriodo = ' . $idperiodo);
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


}

