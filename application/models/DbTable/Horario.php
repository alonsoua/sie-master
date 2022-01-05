<?php

class Application_Model_DbTable_Horario extends Zend_Db_Table_Abstract
{

    protected $_name = 'horario';
    protected $_primary = 'idHorario';



    public function gethorarioid($idhorario)
    {
        return $this->fetchAll('idHorario =' .(int)$idhorario)->toArray();
    }
    public function agregarhorario($dia, $idcurso, $idasignatura, $idcuenta, $idperiodo,$inicio,$termino)
    {
        $db = new Zend_Db_Table('horario');
        $data = array( 'dia' => $dia, 'idCursos' => $idcurso, 'idAsignatura' => $idasignatura, 'idCuenta' => $idcuenta, 'idPeriodo' => $idperiodo,'tiempoInicio'=>$inicio,'tiempoTermino'=>$termino);
        $db->insert($data);
    }

    public function actualizarhorario($idasignatura, $idcuenta,$idhorario)
    {
        $db = new Zend_Db_Table('horario');
        $data = array('idAsignatura' => $idasignatura, 'idCuenta' => $idcuenta);
        $db->update($data, 'idHorario = ' . (int)$idhorario);
    }

    public function listarhorario($idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'horario'))
            ->joinLeft(array('as' => 'asignaturas'), 'p.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura')
            ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= p.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'))
            ->where('p.idCursos= ?', $idcurso);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listarhorarioid($idhorario)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'horario'))
            ->joinLeft(array('as' => 'asignaturas'), 'p.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura')
            ->joinLeft(array('c' => 'cuentasUsuario'), 'c.idCuenta = p.idCuenta',array('nombrescuenta', 'paternocuenta', 'maternocuenta'))
            ->where('p.idHorario= ?', $idhorario);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listarhorariototaldocentes($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'horario'))
            ->where('p.idPeriodo= ?', $idperiodo);
        return $this->fetchAll($select)->toArray();

    }

    public function validarangohorario($dia, $inicio, $termino,$idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'horario'));
        $select->where('p.tiempoInicio < ?', $termino);
        $select->where('p.tiempoTermino > ?', $inicio);
        $select->where('p.dia = ?', $dia);
        $select->where('p.idPeriodo = ?', $idperiodo);
        return $this->fetchAll($select)->toArray();



    }

    public function validarangohorariodocente($dia, $inicio, $termino,$idcuenta,$idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'horario'));
        $select->where('p.tiempoInicio < ?', $termino);
        $select->where('p.tiempoTermino > ?', $inicio);
        $select->where('p.dia = ?', $dia);
        $select->where('p.idPeriodo = ?', $idperiodo);
        $select->where('p.idCuenta = ?', $idcuenta);
        return $this->fetchAll($select)->toArray();



    }

    public function validahorariodocente($dia, $inicio, $termino, $idcurso)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'horario'));
        $select->where('p.tiempoInicio < ?', $termino);
        $select->where('p.tiempoTermino > ?', $inicio);
        $select->where('p.dia = ?', $dia);
        $select->where('p.idCursos = ?', $idcurso);
        return  $this->fetchAll($select)->toArray();

    }


    public function eliminarhorario($idhorario)
    {
        $db = new Zend_Db_Table('horario');
        $db->delete('idHorario =' . (int)$idhorario);
    }


    //Nuevos Cursos Docentes

    public function listarcursodocente($idcuenta, $idperiodo, $idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->joinLeft(array('cu' => 'cursosactual'), 'cu.idCursos= h.idCursos')
            ->join(array('es' => 'establecimiento'), 'cu.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');

        $select->where('h.idCuenta = ?', $idcuenta);
        $select->where('h.idPeriodo = ?', $idperiodo);
        $select->where('es.idEstablecimiento = ?', $idestablecimiento);
        $select->order("h.idCuenta");
        $select->group('cu.idCursos');
        return $this->fetchAll($select);
    }


    public function getcursohorario($idcuenta, $idperiodo, $idcurso, $idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->joinLeft(array('cu' => 'cursosactual'), 'cu.idCursos= h.idCursos')
            ->join(array('es' => 'establecimiento'), 'cu.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado')
            ->joinLeft(array('us' => 'cuentasUsuario'), 'us.idCuenta= h.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));

        $select->where('h.idCuenta = ?', $idcuenta);
        $select->where('h.idPeriodo = ?', $idperiodo);
        $select->where('h.idCursos = ?', $idcurso);
        $select->where('es.idEstablecimiento = ?', $idestablecimiento);
        $select->order("h.idCuenta");
        $select->group('cu.idCursos');
        $row = $this->fetchAll($select)->toArray();
        if ($row) {
            return $row;

        } else {
            return false;
        }

    }

    public function gethorariocurso($idperiodo, $idcurso,$idcuenta,$orden)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->joinLeft(array('cu' => 'cursosactual'), 'cu.idCursos= h.idCursos');
        $select->joinLeft(array('as' => 'asignaturas'), 'h.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura');
        $select->joinLeft(array('cue' => 'cuentasUsuario'), 'cue.idCuenta= h.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));

        $select->where('h.idPeriodo = ?', $idperiodo);
        $select->where('h.idCursos = ?', $idcurso);
        if(!empty($idcuenta)){
            $select->where('h.idCuenta = ?', $idcuenta);
        }
        if($orden==2){
            $select->order("h.dia");
        }
        $select->order("h.tiempoInicio");
        return $this->fetchAll($select)->toArray();


    }

    public function gethorariocursos( $idcurso,$idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->where('h.idPeriodo = ?', $idperiodo);
        $select->where('h.idCursos = ?', $idcurso);
        $select->group('h.idCursos');
        return $this->fetchAll($select)->toArray();


    }



    public function getasignaturashorario($idcurso, $idperiodo, $idcuenta)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->joinLeft(array('cu' => 'cursosactual'), 'cu.idCursos= h.idCursos')
            ->joinLeft(array('as' => 'asignaturas'), 'h.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura');

        $select->where('h.idCuenta = ?', $idcuenta);
        $select->where('h.idPeriodo = ?', $idperiodo);
        $select->where('h.idCursos = ?', $idcurso);
        $select->order("h.idCuenta");
        $select->group('h.idAsignatura');
        $row = $this->fetchAll($select)->toArray();
        if ($row) {
            return $row;

        } else {
            return false;
        }

    }

    public function getdiashabilitados($idcuenta, $idcurso, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('h' => 'horario'), '*');
        $select->where('h.idCursos= ' . $idcurso);
        $select->where('h.idPeriodo= ' . $idperiodo);
        $select->where('h.idCuenta= ' . $idcuenta);
        $select->group('h.dia');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


    public function getasignaturabloque($idcuenta, $idcurso, $idperiodo, $dia)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('h' => 'horario'), '*')
            ->joinLeft(array('as' => 'asignaturas'), 'h.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura');
        $select->where('h.idCursos= ' . $idcurso);
        $select->where('h.idPeriodo= ' . $idperiodo);
        $select->where('h.idCuenta= ' . $idcuenta);
        $select->where('h.dia= ?', $dia);
        $select->group('h.idAsignatura');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function getbloque($idcuenta, $idcurso, $idperiodo, $dia, $idasignatura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('h' => 'horario'), '*')
            ->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        $select->where('h.idCursos= ' . $idcurso);
        $select->where('h.idPeriodo= ' . $idperiodo);
        $select->where('h.idCuenta= ' . $idcuenta);
        $select->where('h.idAsignatura= ' . $idasignatura);
        $select->where('h.dia= ?', $dia);

        //$select->group('h.dia');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function getbloquesin($idcuenta, $idcurso, $idperiodo, $dia, $idasignatura, $idhorarios)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('h' => 'horario'), '*')
            ->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        $select->where('h.idCursos= ' . $idcurso);
        $select->where('h.idPeriodo= ' . $idperiodo);
        $select->where('h.idCuenta= ' . $idcuenta);
        $select->where('h.idAsignatura= ' . $idasignatura);
        $select->where('h.dia= ?', $dia);
        $select->where('h.idHorario NOT IN(?)', $idhorarios);

        //$select->group('h.dia');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function gethorario($idcuenta, $idcurso, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('h' => 'horario'), '*');
        $select->where('h.idCursos= ' . $idcurso);
        $select->where('h.idPeriodo= ' . $idperiodo);
        $select->where('h.idCuenta= ' . $idcuenta);
        //$select->group('h.dia');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function gethorariousuariodia($idcuenta, $idcurso, $dia, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('h' => 'horario'), '*');
        $select->where('h.idCursos= ' . $idcurso);
        $select->where('h.idPeriodo= ' . $idperiodo);
        $select->where('h.idCuenta= ' . $idcuenta);
        $select->where('h.dia= ?', $dia);
        //$select->group('h.dia');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


    public function listarbloquesid($idbloque)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'horario'))
            ->where('p.bloque= ?', $idbloque);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function gethorarioin($idhorario)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->joinLeft(array('c' => 'controlcontenidosdetalle'), 'c.idHorario= h.idHorario',array('idHorario'));
        $select->joinLeft(array('ca' => 'controlasistencia'), 'ca.idHorario= h.idHorario',array('idHorario'));
        $select->where('h.idHorario = ?', $idhorario);
        return $this->fetchAll($select)->toArray();


    }


}
