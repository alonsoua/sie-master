<?php

class Application_Model_DbTable_Contenido extends Zend_Db_Table_Abstract
{

    protected $_name = 'controlcontenidos';


    public function get($idcontenido, $idcurso, $idasignatura, $idcuenta, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'controlcontenidos'), '*')
            ->joinLeft(array('e' => 'cursosactual'), 'o.idCursos = e.idCursos')
            ->joinLeft(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura');
        $select->where('o.idControlContenido= ?', $idcontenido);
        $select->where('o.idAsignatura= ?', $idasignatura);
        $select->where('o.idCuenta= ?', $idcuenta);
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos= ?', $idcurso);

        $row = $this->fetchAll($select);
        if ($row) {
            return $row->toArray();
        } else {
            return false;
        }

    }


    public function agregar($idcurso, $fecha, $idperiodo)
    {
        $data = array('idCursos' => $idcurso, 'fechaControl' => $fecha, 'idPeriodo' => $idperiodo);
        $this->insert($data);


    }

    public function agregardetalle($idhorario, $idasignatura, $contenido, $idcuenta, $idcontenido)
    {
        $db = new Zend_Db_Table('controlcontenidosdetalle');
        $data = array('idHorario' => $idhorario, 'idAsignatura' => $idasignatura, 'contenidos' => $contenido, 'idCuenta' => $idcuenta, 'idControlContenido' => $idcontenido);
        $db->insert($data);


    }

    public function actualizarcontenido($idcontenidodetalle,$contenido)
    {

        $db= new Zend_Db_Table('controlcontenidosdetalle');
        $data = array('contenidos' => $contenido);
        $db->update($data, 'idControlContenidoDetalle = ' . (int)$idcontenidodetalle);
    }




    public function listar($id, $idcurso, $periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'observaciones'), '*');
        $select->join(array('c' => 'cuentasUsuario'), 'o.idCuenta = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));
        $select->joinLeft(array('a' => 'alumnos', array('nombres', 'apaterno', 'amaterno')),
            'a.idAlumnos = o.idAlumnos')
            ->joinLeft(array('e' => 'cursosactual'), 'o.idCursos = e.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento', array('nombreEstablecimiento'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('o.idAlumnos= ?', $id);
        $select->where('o.idCursos= ?', $idcurso);
        $select->where('o.idPeriodo= ?', $periodo);
        $select->where('e.idPeriodo= ?', $periodo);
        $select->order('o.fechaObservacion DESC');

        $stmt = $select->query();

        return $stmt->fetchAll();


    }


    public function editar($idobservacion, $fecha, $observacion, $idcuenta, $idperiodo, $idcurso, $tipo)
    {

        $data = array('fechaObservacion' => $fecha, 'observacion' => $observacion, 'idTipo' => $tipo);
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idObservaciones = ?', $idobservacion);
        $where[] = $this->getAdapter()->quoteInto('idPeriodo = ?', $idperiodo);
        $where[] = $this->getAdapter()->quoteInto('idCursos = ?', $idcurso);
        return $this->update($data, $where);
    }

    public function eliminar($idobservacion, $idcuenta, $idperiodo)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idObservaciones = ?', $idobservacion);
        $where[] = $this->getAdapter()->quoteInto('idCuenta = ?', $idcuenta);
        $where[] = $this->getAdapter()->quoteInto('idPeriodo = ?', $idperiodo);
        $this->delete($where);
    }


    public function validacontenido($idcurso, $fecha, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'controlcontenidos'), '*');
        $select->where('o.fechaControl= ?', $fecha);
        //$select->where('cd.idAsignatura= ?', $idasignatura);
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos= ?', $idcurso);
        $row = $this->fetchAll($select)->toArray();
        if ($row) {
            return $row;
        } else {
            return false;
        }


    }

    public function validacontenidobloque($idcontenido, $idasignatura, $idcuenta, $idhorario)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'controlcontenidosdetalle'), '*');
        $select->where('o.idControlContenido= ?', $idcontenido);
        $select->where('o.idAsignatura= ?', $idasignatura);
        $select->where('o.idCuenta= ?', $idcuenta);
        $select->where('o.idHorario= ?', $idhorario);

        $row = $this->fetchAll($select)->toArray();
        if ($row) {
            return $row;
        } else {
            return false;
        }


    }

    public function listarcontenido($idcurso, $idasignatura, $fecha, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*')
            ->joinLeft(array('cd' => 'controlcontenidosdetalle'), 'a.idControlContenido = cd.idControlContenido');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('cd.idAsignatura = ' . $idasignatura);
        $select->where('a.fechaControl = ?', $fecha);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->order('a.fechaControl DESC');
        $row = $this->fetchAll($select)->toArray();
        //return $row->toArray();
        if ($row) {
            return $row;
        } else {
            return false;
        }

    }

    public function listarbloquescontenido($idcontenido)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidosdetalle'), '*')
            ->joinLeft(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura')
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');
            //->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        $select->order('h.idHorario');
        $select->where('a.idControlContenido = ' . $idcontenido);
        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarbloquescontenidouser($idcontenido,$idcuenta)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidosdetalle'), '*')
            ->joinLeft(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura')
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');
            //->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        $select->order('h.idHorario');
        $select->where('a.idControlContenido = ' . $idcontenido);
        $select->where('a.idCuenta= ' . $idcuenta);
        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarbloquescontenidoid($idcontenido, $idhorario)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidosdetalle'), '*')
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');
           // ->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        $select->where('a.idControlContenido = ' . $idcontenido);
        $select->where('a.idHorario = ' . $idhorario);

        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return $row;
        } else {
            return false;
        }


    }


    public function listarcontenidos($idcurso, $idcuenta, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*')
            ->joinLeft(array('cd' => 'controlcontenidosdetalle'), 'a.idControlContenido = cd.idControlContenido');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('cd.idCuenta = ' . $idcuenta);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->group('a.idControlContenido');
        $select->order('a.fechaControl ASC');
        $row = $this->fetchAll($select)->toArray();
        return $row;


    }

    public function listarcontenidosadmin($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*');
        $select->where('a.idCursos = ' . $idcurso);
        //$select->where('cd.idCuenta = ' . $idcuenta);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->order('a.fechaControl');
        $select->group('a.idControlContenido');
        $row = $this->fetchAll($select)->toArray();
        return $row;


    }

    public function getcontenido($idcontenido, $idcuenta, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('cd' => 'controlcontenidosdetalle'), '*')
            ->joinLeft(array('co' => 'controlcontenidos'), 'cd.idControlContenido = co.idControlContenido')
            ->joinLeft(array('as' => 'asignaturas'), 'cd.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura')
            ->joinLeft(array('h' => 'horario'), 'cd.idHorario = h.idHorario');
            //->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        $select->where('cd.idControlContenidoDetalle= ?', $idcontenido);
        $select->where('cd.idCuenta= ?', $idcuenta);
        $select->where('co.idPeriodo= ?', $idperiodo);
        $row = $this->fetchAll($select);
        if ($row) {
            return $row->toArray();
        } else {
            return false;
        }

    }

    public function getregistrofecha($idcurso, $idperiodo,$idcuenta,$fecha)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*');
        $select->joinLeft(array('cd' => 'controlcontenidosdetalle'), 'a.idControlContenido = cd.idControlContenido');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->where('a.fechaControl= ?' ,$fecha);
        if(!empty($idcuenta)){
            $select->where('cd.idCuenta= ?' ,$idcuenta);
        }
        return $this->fetchAll($select)->toArray();


    }





}

