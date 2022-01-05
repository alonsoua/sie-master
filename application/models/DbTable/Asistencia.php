<?php

class Application_Model_DbTable_Asistencia extends Zend_Db_Table_Abstract
{

    protected $_name = 'asistencia';


    public function get($id)
    {
        $idcurso = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnos'), '*');
        $select->join(array('c' => 'cursos'), 'c.idCursos = a.idCursos');

        $select->where('a.idCursos = ' . $idcurso);
        return $this->fetchAll($select);

    }


    public function agregar($fecha, $periodo, $curso, $usuario)
    {
        $data = array('fechaAsistencia' => $fecha, 'idPeriodo' => $periodo, 'idCursos' => $curso, 'idCuenta' => $usuario);
        $this->insert($data);

    }

    public function agregarvalores($valor, $idalumno, $idasistencia)
    {
        $db = new Zend_Db_Table('asistenciavalores');
        $data = array('valor' => $valor, 'idAlumnos' => $idalumno, 'idAsistencia' => $idasistencia);
        $db->insert($data);
    }


    public function cambiar($idasistencia, $asistencia)
    {
        $db = new Zend_Db_Table('asistenciavalores');
        $data = array('valor' => $asistencia);
        $db->update($data, 'idAsistenciaValores = ' . (int)$idasistencia);
    }


    public function listarbasica($usuario, $curso, $periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'asistencia'), '*');
        $select->join(array('c' => 'cursos'), 'c.idCursos = a.idCursos');
        $select->where('a.idCursos = ' . $curso);
        $select->where('a.idCuenta =? ', $usuario);
        $select->where('a.idPeriodo = ' . $periodo);
        $select->order("a.fechaAsistencia");
        $select->group(array('a.fechaAsistencia'));

        return $this->fetchAll($select);

    }


    public function getdias($curso, $periodo)
    {
        $idcurso = (int)$curso;
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asistencia'), array('fechaAsistencia'));
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo = ' . $periodo);
        $select->group('a.fechaAsistencia');
        return $this->fetchAll($select);

    }

    public function listaralumnosbasica($id)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asistencia'), '*');
        $select->join(array('as' => 'asistenciavalores'),'a.idAsistencia = as.idAsistencia');
        $select->join(array('al' => 'alumnos'), 'al.idAlumnos =as.idAlumnos', array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno'));
        $select->join(array('alu' => 'AlumnosActual'), 'alu.idAlumnos =as.idAlumnos', array('idAlumnosActual', 'idAlumnos', 'idEstadoActual', 'ordenAlumno'));
        $select->where('alu.idEstadoActual = 1');
        $select->where('a.idAsistencia = ?', $id);
        $select->order("alu.ordenAlumno");
        $select->group('alu.idAlumnos');

        return $this->fetchAll($select);

    }

    public function listaradmin($curso, $periodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asistencia'), '*');
        $select->joinLeft(array('e' => 'cursosactual'), 'a.idCursos = e.idCursos');
        $select->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = e.idEstablecimiento');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->join(array('p' => 'periodo'),
            'a.idPeriodo = p.idPeriodo');
        $select->where('a.idCursos = ' . $curso);
        $select->where('a.idPeriodo = ' . $periodo);
        $select->order('a.fechaAsistencia ASC');

        return $this->fetchAll($select);


    }

    public function listarall($periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asistencia'), '*');
        $select->joinLeft(array('e' => 'cursosactual'), 'a.idCursos = e.idCursos');
        $select->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = e.idEstablecimiento');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->join(array('p' => 'periodo'),
            'a.idPeriodo = p.idPeriodo');
        $select->where('a.idPeriodo =? ', $periodo);
        $select->order('g.idCodigoGrado ASC');
        $select->order('a.fechaAsistencia DESC');
        return $this->fetchAll($select);


    }

    public function ultimocodigo($idasistencia)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'codigoasistencia'), '*');
        $select->where('a.idAsistenciac = ?', $idasistencia);
        $select->order("a.idCodigoAsistencia DESC");
        return $this->fetchAll($select);


    }

    public function getasistenciacurso($curso, $mes, $periodo)
    {
        $idcurso = (int)$curso;
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asistencia'), '*');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo = ' . $periodo);
        $select->where('MONTH(a.fechaAsistencia) = ?', $mes);

        return $this->fetchAll($select);

    }

    public function getvaloresasistencia($idasistencia, $idalumno)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asistenciavalores'), '*');
        $select->where('a.idAsistencia = ' . $idasistencia);
        $select->where('a.idAlumnos = ' . $idalumno);

        return $this->fetchAll($select)->toArray();


    }

    public function getasistenciaalumno($idalumno, $idcurso, $fechainicio, $fechatermino, $periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asistencia'), '*');
        $select->joinLeft(array('av' => 'asistenciavalores'), 'av.idAsistencia = a.idAsistencia');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo = ' . $periodo);
        $select->where('av.idAlumnos = ' . $idalumno);

        $select->where('a.fechaAsistencia >= ?', $fechainicio)
            ->where('a.fechaAsistencia <= ?', $fechatermino)
            ->order('a.fechaAsistencia');

        return $this->fetchAll($select);

    }

//Control Asistencia

    public function agregarcontrol($idasignatura, $idcuenta, $idhorario,$tipo,$idcontrol)
    {
        $db = new Zend_Db_Table('controlasistencia');
        $data = array('idAsignatura' => $idasignatura, 'idCuenta' => $idcuenta, 'idHorario' => $idhorario,'tipoModalidadAsistencia'=>$tipo, 'idControl' => $idcontrol);
        $db->insert($data);

    }

    public function editarcontrol($idasistencia, $tipo)
    {
        $db = new Zend_Db_Table('controlasistencia');
        $data = array('tipoModalidadAsistencia' => $tipo);
        $db->update($data, 'idControlAsistencia = ' . (int)$idasistencia);
    }

    public function agregarcontrolvalores($idalumno, $asistencia, $tipoasistencia, $idcontrolasistencia)
    {
        $db = new Zend_Db_Table('controlasistenciavalores');
        $data = array('idAlumnos' => $idalumno, 'valorasistencia' => $asistencia, 'tipoAsistencia' => $tipoasistencia, 'idControlAsistencia' => $idcontrolasistencia);
        $db->insert($data);
    }

    public function editarcontrolvalores($idasistencia, $tipo)
    {
        $db = new Zend_Db_Table('controlasistenciavalores');
        $data = array('tipoAsistencia' => $tipo);
        $db->update($data, 'idControlAsistencia = ' . (int)$idasistencia);
    }

    public function listarcategoriaasistencia($tipo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'categoriaasistencia'), '*');
        $select->where('a.tipo = ' . $tipo);
        return $this->fetchAll($select)->toArray();

    }


    public function listarasistencia($idcurso, $idasignatura, $fecha, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*')
            ->joinLeft(array('cd' => 'controlasistencia'), 'a.idControlContenido = cd.idControl');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('cd.idAsignatura = ' . $idasignatura);
        $select->where('a.fechaControl = ?', $fecha);
        $select->where('a.idPeriodo= ' . $idperiodo);

        $row = $this->fetchAll($select)->toArray();
        if ($row) {
            return $row;
        } else {
            return false;
        }

    }


    public function listarasistencias($idcurso, $idcuenta, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*')
            ->joinLeft(array('cd' => 'controlasistencia'), 'a.idControlContenido = cd.idControl');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('cd.idCuenta = ' . $idcuenta);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->group('a.idControlContenido');
        $select->order('a.fechaControl DESC');
        return $this->fetchAll($select)->toArray();


    }

    public function listaradminasistencias($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*');
        $select->joinLeft(array('cd' => 'controlasistencia'), 'a.idControlContenido = cd.idControl');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->group('a.idControlContenido');
        return $this->fetchAll($select)->toArray();


    }

    public function getasistenciafecha($idcurso, $idperiodo,$idcuenta,$fecha)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*');
        $select->joinLeft(array('cd' => 'controlasistencia'), 'a.idControlContenido = cd.idControl');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->where('a.fechaControl= ?' ,$fecha);
        if(!empty($idcuenta)){
            $select->where('cd.idCuenta= ?' ,$idcuenta);
        }
        return $this->fetchAll($select)->toArray();


    }

    public function listaradminasistenciasmes($idcurso, $idperiodo, $mes)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlcontenidos'), '*');
        $select->joinLeft(array('cd' => 'controlasistencia'), 'a.idControlContenido = cd.idControl');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo= ' . $idperiodo);
        $select->where('MONTH(a.fechaControl) = ?', $mes);
        $select->order('a.fechaControl ASC');
        $select->group('a.idControlContenido');
        return $this->fetchAll($select)->toArray();


    }


    public function listarbloquesasistencia($idcontenido,$idcuenta)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlasistencia'), '*')
            ->joinLeft(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura')
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');
        $select->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta = a.idCuenta',array('nombrescuenta','paternocuenta','maternocuenta'));
        $select->where('a.idControl = ' . $idcontenido);
        $select->where('a.idCuenta = ' . $idcuenta);
        $select->order('h.idHorario ASC');
        return $this->fetchAll($select)->toArray();

    }


    public function listarbloquesasistenciaid($idcontenido, $idhorario)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlasistencia'), '*');
        $select->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');
        $select->where('a.idControl = ' . $idcontenido);
        $select->where('a.idHorario = ' . $idhorario);
        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return $row;
        } else {
            return false;
        }


    }


    public function listarcontrolasistencia($idcurso, $idcuenta, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlasistencia'), '*');
        $select->joinLeft(array('e' => 'cursosactual'), 'a.idCursos = e.idCursos');
        $select->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = e.idEstablecimiento');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->join(array('p' => 'periodo'), 'a.idPeriodo = p.idPeriodo');
        $select->where('a.idCursos = ' . $idcurso);
        $select->where('a.idPeriodo = ' . $idperiodo);
        $select->where('a.idPeriodo = ' . $idcuenta);
        $select->order('a.fechaAsistencia ASC');

        return $this->fetchAll($select);


    }

    public function listarasistenciavalor($idcontrol)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'controlasistencia'), '*')
            ->joinLeft(array('co' => 'controlcontenidos'), 'a.idControl = co.idControlContenido')
            ->joinLeft(array('as' => 'controlasistenciavalores'), 'a.idControlAsistencia = as.idControlAsistencia')
            ->joinLeft(array('asi' => 'asignaturas'), 'a.idAsignatura = asi.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = asi.idAsignatura');
        $select->where('a.idControlAsistencia = ?', $idcontrol);

        return $this->fetchAll($select);

    }

    public function listarasistenciavalores($idcontrol, $idalumno)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'controlasistencia'), '*')
            ->joinLeft(array('co' => 'controlcontenidos'), 'a.idControl = co.idControlContenido')
            ->joinLeft(array('as' => 'controlasistenciavalores'), 'a.idControlAsistencia = as.idControlAsistencia')
            ->joinLeft(array('asi' => 'asignaturas'), 'a.idAsignatura = asi.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = asi.idAsignatura')
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');

        $select->where('a.idControlAsistencia = ?', $idcontrol);
        $select->where('as.idAlumnos = ?', $idalumno);

        return $this->fetchAll($select)->toArray();


    }

    public function listarasistenciavaloress($idcontrol)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'controlasistencia'), '*')
            ->joinLeft(array('co' => 'controlcontenidos'), 'a.idControl = co.idControlContenido')
            ->joinLeft(array('as' => 'controlasistenciavalores'), 'a.idControlAsistencia = as.idControlAsistencia')
            ->joinLeft(array('al' => 'alumnos'), 'al.idAlumnos =as.idAlumnos', array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno'))
            ->joinLeft(array('alu' => 'AlumnosActual'), 'alu.idAlumnos =as.idAlumnos', array('idAlumnosActual', 'idAlumnos', 'idEstadoActual', 'ordenAlumno'))
            ->joinLeft(array('asi' => 'asignaturas'), 'a.idAsignatura = asi.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = asi.idAsignatura')
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');
        $select->where('alu.idEstadoActual = 1');
        $select->where('a.idControlAsistencia = ?', $idcontrol);
        $select->order("alu.ordenAlumno");
        $select->group('alu.idAlumnos');
        return $this->fetchAll($select);

    }

    public function listarasistenciavaloresalumnos($idcontrol)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlasistenciavalores'), '*');
        $select->where('a.idControlAsistencia = ?', $idcontrol);


        return $this->fetchAll($select);

    }


    public function cambiarcontrolasistencia($idasistencia, $asistencia, $categoria)
    {
        $db = new Zend_Db_Table('controlasistenciavalores');
        $data = array('valorasistencia' => $asistencia, 'tipoAsistencia' => $categoria);
        $db->update($data, 'idAsistenciaValores = ' . (int)$idasistencia);
    }


    public function listarAsistenciaDiariaAlumnoJSON($idalumno, $idperiodo, $idcurso)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'controlasistencia'), '*')
            ->joinLeft(array('co' => 'controlcontenidos'), 'a.idControl = co.idControlContenido')
            ->joinLeft(array('as' => 'controlasistenciavalores'), 'a.idControlAsistencia = as.idControlAsistencia')
            ->joinLeft(array('al' => 'alumnos'), 'al.idAlumnos =as.idAlumnos', array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno'))
            ->joinLeft(array('alu' => 'AlumnosActual'), 'alu.idAlumnos =as.idAlumnos', array('idAlumnosActual', 'idAlumnos', 'idEstadoActual', 'ordenAlumno'))
            ->joinLeft(array('asi' => 'asignaturas'), 'a.idAsignatura = asi.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = asi.idAsignatura')
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario')
            ->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        $select->where('alu.idEstadoActual = 1');
        $select->where('as.idAlumnos = ?', $idalumno);
        $select->where('co.idPeriodo = ?', $idperiodo);
        $select->where('co.idCursos = ?', $idcurso);
        $select->order("b.tiempoInicio");
        $select->group('co.fechaControl');
        return $this->fetchAll($select);

    }

    public function validarcambioasistencia($idasistenciavalor, $valorasistencia, $tipoasistencia)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'controlasistenciavalores'), '*');
        $select->where('a.idAsistenciaValores = ?', $idasistenciavalor);
        $select->where('a.valorasistencia= ?', $valorasistencia);
        $select->where('a.tipoAsistencia= ?', $tipoasistencia);
        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return false;
        } else {
            return true;
        }

    }


}
