<?php

class Application_Model_DbTable_Observacion extends Zend_Db_Table_Abstract
{

    protected $_name = 'observaciones';




    public function get($id,$idperiodo,$idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'observaciones'), '*');
        $select->joinLeft(array('a' => 'alumnos'),
            'a.idAlumnos = o.idAlumnos')
            ->joinLeft(array('e' => 'cursosactual'), 'o.idCursos = e.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->joinLeft(array('asc' => 'asignaturascursos'),
            'asc.idAsignatura = o.idAsignatura');
        $select->joinLeft(array('as' => 'asignaturas'), 'asc.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('o.idObservaciones= ?', $id);
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos= ?', $idcurso);

        $row=$this->fetchAll($select);
        if ($row) {
            return $row->toArray();
        }else{
            return false;
        }

    }


    public function agregar($rut, $fecha, $sub, $comentarios, $usuario, $periodo, $curso, $tipo)
    {
        $data = array('idAlumnos' => $rut, 'fechaObservacion' => $fecha, 'idAsignatura' => $sub, 'observacion' => $comentarios, 'idCuenta' => $usuario, 'idPeriodo' => $periodo, 'idCursos' => $curso, 'idTipo' => $tipo,'estadoObservacion'=>1);

        $this->insert($data);


    }

    public function cambiar($id, $rut, $fecha, $sub, $comentarios, $usuario, $periodo, $curso, $tipo)
    {
        $data = array('idAlumnos' => $rut, 'fechaObservacion' => $fecha, 'idAsignatura' => $sub, 'observacion' => $comentarios, 'idCuenta' => $usuario, 'idPeriodo' => $periodo, 'idCursos' => $curso, 'idTipo' => $tipo
        );
        //$this->update cambia datos de Alumno con Rut= $rut
        $this->update($data, 'idObservaciones = ' . (int)$id);
    }


    public function borrar($idobservaciones)
    {
        //$this->delete borrar album donde RBD=$rbd
        $this->delete('idObservaciones =' . (int)$idobservaciones);


    }

    public function listar($id,$periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'observaciones'), '*');
        $select->join(array('c' => 'cuentasUsuario'), 'o.idCuenta = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));
        $select->join(array('a' => 'alumnos',array('nombres','apaterno','amaterno')), 'a.idAlumnos = o.idAlumnos')
            ->join(array('al' => 'AlumnosActual',array('nombres','apaterno','amaterno','comunaActual','calle','numeroCasa','villa','ciudadActual')), 'al.idAlumnos = o.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'o.idCursos = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento',array('nombreEstablecimiento'))
            ->join(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->join(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('o.idAlumnos= ?', $id);
        $select->where('al.idPeriodoActual= ?', $periodo);
        $select->where('o.idPeriodo= ?', $periodo);
        $select->where('o.estadoObservacion= 1');
        $select->order('o.fechaObservacion DESC');
        //$select->group('o.idObservaciones');

        $stmt = $select->query();

        return $stmt->fetchAll();


    }

    public function listarpordocente($id, $periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'observaciones'), '*');
        $select->join(array('a' => 'alumnos'),
            'a.idAlumnos = o.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'o.idCursos = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->join(array('asc' => 'asignaturascursos'),
            'asc.idAsignatura = o.idAsignatura');
        $select->join(array('as' => 'asignaturas'), 'asc.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('o.idAlumnos= ?', $id);
        $select->where('o.idPeriodo= ?', $periodo);

        $stmt = $select->query();

        return $stmt->fetchAll();


    }



    public function editar($idobservacion, $fecha, $observacion,$idcuenta,$idperiodo,$idcurso,$tipo)
    {

        $data = array('fechaObservacion' => $fecha, 'observacion' => $observacion,'idTipo'=>$tipo);
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idObservaciones = ?', $idobservacion);
        $where[] = $this->getAdapter()->quoteInto('idPeriodo = ?', $idperiodo);
        $where[] = $this->getAdapter()->quoteInto('idCursos = ?', $idcurso);
        return $this->update($data, $where);
    }

    public function eliminar($idobservacion,$idcuenta,$idperiodo)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idObservaciones = ?', $idobservacion);
        $where[] = $this->getAdapter()->quoteInto('idCuenta = ?', $idcuenta);
        $where[] = $this->getAdapter()->quoteInto('idPeriodo = ?', $idperiodo);
        return $this->update(array('estadoObservacion'=>2), $where);
    }
}

