<?php

class Application_Model_DbTable_Json extends Zend_Db_Table_Abstract
{

    protected $_name = 'jsondetalle';
    protected $_primary = 'idJson';

    // ALUMNOS
    public function listarAlumnosJSON($periodo, $idEstablecimiento, $cursos)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('rutAlumno', 'nombres', 'apaterno', 'amaterno', 'fechanacimiento', 'sexo', 'paisNacimiento', 'ciudadNacimiento'))
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado')
            ->joinLeft(array('com' => 'comuna'), 'com.idComuna = p.comunaActual')
            ->joinLeft(array('pr' => 'provincia'), 'pr.idProvincia = com.idProvincia')
            ->joinLeft(array('rg' => 'region'), 'rg.idRegion = pr.idRegion');


        $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idCursosActual IN (?)', $cursos);
        $select->where('p.idEstadoActual=1');
        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);
    }


    public function listarAlumnosCursoJSON($idCurso, $periodo, $idEstablecimiento)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'), array('idAlumnosActual', 'fechaInscripcion', 'idCursosActual', 'idEstadoActual', 'fechaInscripcion', 'numeroMatricula', 'ordenAlumno'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos'))
            ->join(array('ae' => 'alumnosEscolar'), 'ae.idAlumnosActual = p.idAlumnosActual', array('junaeb'))
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos', array())
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento', array('idEstablecimiento'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo', array())
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado', array('idCodigoGrado'));


        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idCursosActual=' . $idCurso);
        $select->where('p.idEstadoActual=1');
        $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);
    }


    public function listarAlumnosAsignaturaPorCursoJSON($idAlumno, $idCurso, $periodo, $idEstablecimiento)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'), array())
            ->join(array('ho' => 'horario'), 'p.idCursosActual = ho.idCursos', array('idAsignatura'))
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos', array());
        $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        $select->where('p.idAlumnosActual=' . $idAlumno);
        $select->where('ho.idPeriodo=' . $periodo);
        $select->where('ho.idCursos=' . $idCurso);
        $select->where('p.idEstadoActual=1');
        $select->order("ho.idAsignatura");
        $select->group("ho.idAsignatura");

        return $this->fetchAll($select);
    }


    public function listarAlumnosApoderadoPrincipalJSON($periodo, $idEstablecimiento = null)
    {


        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual', array('idAlumno', 'idApoderado')))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('apo' => 'apoderados'), 'p.idApoderado = apo.idApoderado')
            ->joinLeft(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento');

        if (!is_null($idEstablecimiento)) {
            $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=1');

        return $this->fetchAll($select);
    }

    public function listarAlumnosApoderadoSecundarioJSON($periodo, $idEstablecimiento = null)
    {


        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual', array('idAlumno', 'idApoderados')))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('apo' => 'apoderados'), 'p.idApoderadoSuplente = apo.idApoderado')
            ->joinLeft(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento');

        if (!is_null($idEstablecimiento)) {
            $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=1');

        return $this->fetchAll($select);
    }
    // FIN ALUMNOS


    // APODERADOS
    public function listarApoderadosPrincipalJSON($idPeriodo = null, $idEstablecimiento = null)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('apo' => 'apoderados'), array('apo.*', 'act.idApoderado as alumnoIdApoderado'))
            //->joinLeft(array('alu' => 'alumnos'), 'alu.idApoderado = apo.idApoderado')
            ->joinLeft(array('act' => 'AlumnosActual'), 'act.idApoderado = apo.idApoderado')
            ->joinLeft(array('alc' => 'alumnosClinico'), 'alc.idAlumnos = act.idAlumnos')
            ->joinLeft(array('cur' => 'cursosactual'), 'cur.idCursos = act.idCursosActual');

        if (!is_null($idPeriodo)) {
            $select->where('act.idPeriodoActual= ?', $idPeriodo);
        }

        if (!is_null($idEstablecimiento)) {
            $select->where('cur.idEstablecimiento= ?', $idEstablecimiento);
        }
        $select->where('act.idEstadoActual=1');

        return $this->fetchAll($select);

    }

    public function listarApoderadosSecundarioJSON($idPeriodo = null, $idEstablecimiento = null)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('apo' => 'apoderados'), array('apo.*', 'apo.idApoderado as apoderadoId', 'act.idApoderadoSuplente as alumnoIdApoderado'))
            ->joinLeft(array('act' => 'AlumnosActual'), 'act.idApoderadoSuplente = apo.idApoderado')
            ->joinLeft(array('alu' => 'alumnos'), 'alu.idAlumnos = act.idAlumnos')
            ->joinLeft(array('cur' => 'cursosactual'), 'cur.idCursos = act.idCursosActual');

        if (!is_null($idPeriodo)) {
            $select->where('act.idPeriodoActual= ?', $idPeriodo);
        }

        if (!is_null($idEstablecimiento)) {
            $select->where('cur.idEstablecimiento= ?', $idEstablecimiento);
        }
        $select->where('act.idEstadoActual=1');

        return $this->fetchAll($select);

    }
    // FIN APODERADOS


    // CUENTAS
    public function listarDocentesJSON($idPeriodo, $idEstablecimiento = null)
    {


        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('cro' => 'cuentaRoles'))
            ->joinLeft(array('cus' => 'cuentasUsuario'), 'cus.idCuenta = cro.idCuenta')
            ->joinLeft(array('ro' => 'roles'), 'ro.idRoles = cro.idRol')
            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = cro.idEstablecimiento')
            ->where('cro.idPeriodo=?', $idPeriodo)
            ->where('cro.idRol=?', 2);  // Solo Docentes

        if (!is_null($idEstablecimiento)) {
            $select->where('cro.idEstablecimiento=?', $idEstablecimiento);
        }


        return $this->fetchAll($select);
    }


    public function listarDocentesPorCursosJSON($idPeriodo, $idEstablecimiento, $idcursos)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'), array('idCuenta'))
            ->joinLeft(array('cc' => 'cursosactual'), 'ho.idCursos = cc.idCursos', array('idCuentaJefe', 'idEstablecimiento'))
            ->joinLeft(array('cus' => 'cuentasUsuario'), 'cus.idCuenta = ho.idCuenta')
            ->where('ho.idCursos IN (?)', $idcursos)
            ->where('ho.idPeriodo = ?', $idPeriodo);
        $select->where('cc.idEstablecimiento = ?', $idEstablecimiento);
        $select->group('ho.idCuenta');

        return $this->fetchAll($select);
    }


    public function listarDocentesCursosJSON($idCuenta, $idEstablecimiento)
    {


        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('cro' => 'cuentaRoles'))
            ->joinLeft(array('cuc' => 'cuentascurso'), 'cuc.idCuentaRol = cro.id')
            ->where('cro.idRol=?', 2); // Solo Docentes
        $select->where('cro.idEstablecimiento=?', $idEstablecimiento);
        $select->where('cro.idCuenta=?', $idCuenta);
        $select->where('cro.estadoCuenta = 1');


        return $this->fetchAll($select);
    }
    // FIN CUENTAS


    // ESTABLECIMIENTOS
    public function listarEstablecimientosJSON($idPeriodo, $idEstablecimiento)
    {


        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('e' => 'establecimiento'))
            ->joinLeft(array('econf' => 'establecimientoConfiguracion'), 'e.idEstablecimiento=econf.idEstablecimiento');
        $select->where('e.idEstablecimiento=?', $idEstablecimiento);
        $select->where('econf.idPeriodo=?', $idPeriodo);


        return $this->fetchAll($select);
    }

//    public function listarRelEstablecimientosJSON($idPeriodo = null, $idEstablecimiento = null)
//    {
//
//
//        $select = $this->select();
//
//        $select->setIntegrityCheck(false)
//            ->from(array('e' => 'establecimiento'))
//            ->joinLeft(array('ec' => 'establecimientoConfiguracion'), 'e.idestablecimiento = ec.idestablecimiento');
//            $select->where('e.idEstablecimiento=?', $idEstablecimiento);
//
//
//        return $this->fetchAll($select);
//    }
    // FIN ESTABLECIMIENTOS


    // SOSTENEDORES
    public function listarSostenedoresJSON($idPeriodo = null, $idEstablecimiento = null)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('est' => 'establecimiento'), array('sos.*'))
            ->joinLeft(array('sos' => 'sostenedor'), 'sos.idSostenedor = est.idSostenedor');

        if (!is_null($idEstablecimiento)) {
            $select->where('est.idEstablecimiento=?', $idEstablecimiento);
        }


        return $this->fetchAll($select);
    }
    // FIN SOSTENEDORES


    // CURSOS
    public function listarCursosJSON($periodo, $idEstablecimiento = null)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'))
            ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos')
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');

        $select->where('ho.idPeriodo=' . $periodo);
        if (!is_null($idEstablecimiento)) {
            $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->group('ho.idCursos');
        // $select->where('ca.idCodigoGrado != 4');
        // $select->where('ca.idCodigoGrado != 5');

        return $this->fetchAll($select);
    }

    public function listarCursosJSON2($periodo, $idEstablecimiento)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ca' => 'cursosactual'))
            //->join(array('ca' => 'cursosactual'), 'c.idCursos = ca.idCursos')
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');

        $select->where('ca.idPeriodo=' . $periodo);
        if (!is_null($idEstablecimiento)) {
            $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->group('ca.idCursos');
        // $select->where('ca.idCodigoGrado != 4');
        // $select->where('ca.idCodigoGrado != 5');

        return $this->fetchAll($select);
    }

    public function listarAsignaturasCursosJSON($idCursos, $idPeriodo, $idEstablecimiento = null)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
//            ->from(array('ac' => 'asignaturascursos'))
            ->from(array('ho' => 'horario'))
            ->join(array('as' => 'asignaturas'), 'ho.idAsignatura = as.idAsignatura')
            ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos')
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');

        $select->where('ho.idCursos =' . $idCursos);
        $select->where('ho.idPeriodo =' . $idPeriodo);
        if (!is_null($idEstablecimiento)) {
            $select->where('ca.idEstablecimiento =' . $idEstablecimiento);
        }
        $select->order('ho.idAsignatura');
        $select->group('ho.idAsignatura');
        // $select->where('ca.idCodigoGrado != 5');


        return $this->fetchAll($select);
    }
    // FIN CURSOS


    // ASIGNATURAS
    public function listarAsignaturasJSON($periodo = null, $idEstablecimiento = null, $cursos)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'))
            ->join(array('as' => 'asignaturas'), 'ho.idAsignatura = as.idAsignatura')
            ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos')
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');


        $select->where('as.estado = 1');
        $select->where('ho.idCursos IN (?)', $cursos);
        // $select->where('ca.idCodigoGrado != 4');
        // $select->where('ca.idCodigoGrado != 5');
        $select->group('ho.idAsignatura');
        return $this->fetchAll($select);
    }

    //aca
    public function listarControlContenidosJSON($idPeriodo, $idEstablecimiento, $idCurso, $idAsignatura)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('cc' => 'controlcontenidos'), array('idControlContenido', 'fechaControl'))
            ->join(array('cd' => 'controlcontenidosdetalle'), 'cc.idControlContenido = cd.idControlContenido', array('idControlContenidoDetalle', 'contenidos', 'idCuenta as idDocente'))
            ->join(array('ho' => 'horario'), 'cd.idHorario = ho.idHorario', array('idHorario', 'dia', 'tiempoInicio', 'tiempoTermino'))
            //->join(array('bl' => 'bloque'), 'ho.bloque = bl.idBloque', array('idBloque', 'nombreBloque', 'tiempoInicio', 'tiempoTermino', 'tipoBloque'))
            ->join(array('ca' => 'cursosactual'), 'cc.idCursos = ca.idCursos', array('idEstablecimiento'));

        $select->where('cc.idPeriodo = ?', $idPeriodo);
        $select->where('cc.idCursos = ?', $idCurso);

        $select->where('ho.idAsignatura = ?', $idAsignatura);
        $select->where('ca.idEstablecimiento = ?', $idEstablecimiento);

        return $this->fetchAll($select);

    }


    //borrar
    public function listarBloquesAsignaturasJSON($idPeriodo, $idEstablecimiento, $idCurso, $idAsignatura)
    {  // bloque


        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('h' => 'horario'), array('dia', 'tiempoInicio', 'tiempoTermino'))
            ->join(array('b' => 'cursosactual'), 'h.idCursos = b.idCursos');

        $select->where('h.idPeriodo = ?', $idPeriodo);
        $select->where('h.idCursos = ?', $idCurso);
        $select->where('h.idAsignatura = ?', $idAsignatura);
        $select->where('b.idEstablecimiento = ?', $idEstablecimiento);

        return $this->fetchAll($select);
    }

    public function listarAsignaturasAlumnoJSON($idCursos)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ac' => 'asignaturascursos'))
            ->join(array('as' => 'asignaturas'), 'ac.idAsignatura = as.idAsignatura');


        $select->where('as.estado = 1');
        $select->where('ac.idCursos = ' . $idCursos);

        return $this->fetchAll($select);
    }
    // FIN ASIGNATURAS


    // NIVELES
    public function listarNivelesJSON($idPeriodo, $idEstablecimiento, $cursos)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'cursosactual'))
//            ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos')
//            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');
            ->join(array('co' => 'codigogrados'), 'c.idCodigoGrado = co.idCodigoGrado');
        $select->where('c.idEstablecimiento=?', $idEstablecimiento);
        $select->where('c.idPeriodo=?', $idPeriodo);
        $select->where('c.idCursos IN (?)', $cursos);

        return $this->fetchAll($select);
    }

    public function listarNivelesCursosJSON($idPeriodo, $idEstablecimiento, $idcursos)
    {
        // Consultar el nivel de cada curso
        // consultar la jornada
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'))
            ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos', array('idCursos', 'idCodigoGrado', 'idCodigoTipo', 'tipoJornada', 'codigoSector', 'codigoEspecialidad', 'letra'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'ca.idCodigoTipo= ce.idCodigoTipo')
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado', array('idCodigoGrado', 'nombreGrado', 'idCodigo', 'idGrado'));
        $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        $select->where('ho.idPeriodo=' . $idPeriodo);
        $select->where('ho.idCursos IN (?)' , $idcursos);
        $select->group('ho.idCursos');
        return $this->fetchAll($select);
    }
    // FIN NIVELES


    // OBSERVACIONES
    public function listarObservacionesJSON($idPeriodo = null, $idEstablecimiento = null)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('obs' => 'observaciones'))
            ->join(array('alu' => 'alumnos'), 'alu.idAlumnos         = obs.idAlumnos')
            ->join(array('asi' => 'asignaturas'), 'asi.idAsignatura      = obs.idAsignatura')
            ->joinLeft(array('cur' => 'cursosactual'), 'cur.idCursos          = obs.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = cur.idEstablecimiento');

        if (!is_null($idPeriodo)) {
            $select->where('obs.idPeriodo= ?', $idPeriodo);
        }

        $select->where('obs.idTipo= 2');

        return $this->fetchAll($select);
    }
    // FIN OBSERVACIONES


    // ASISTENCIAS
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
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario');
        //->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        //$select->where('al.idAlumnos NOT in (?)', array('1551'));
        $select->where('alu.idEstadoActual = 1');
        $select->where('as.idAlumnos = ?', $idalumno);
        $select->where('co.idPeriodo = ?', $idperiodo);
        $select->where('co.idCursos = ?', $idcurso);
        $select->order("h.tiempoInicio");
        $select->group('co.fechaControl');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function listarAsistenciaBloqueAlumnoJSON($idalumno, $idperiodo, $idcurso, $idAsignatura)
    {


        $select = $this->select();

        $select->setIntegrityCheck(false);

        $select->from(array('cav' => 'controlasistenciavalores'), array('valorasistencia', 'tipoAsistencia'))
            ->joinLeft(array('ca' => 'controlasistencia'), 'cav.idControlAsistencia = ca.idControlAsistencia', array('idAsignatura'))
            ->joinLeft(array('ho' => 'horario'), 'ca.idHorario = ho.idHorario', array(''))
            ->joinLeft(array('cc' => 'controlcontenidos'), 'ca.idControl = cc.idControlContenido', array('fechaControl'));

        $select->where('cav.idAlumnos = ?', $idalumno);
        $select->where('ho.idPeriodo = ?', $idperiodo);
        $select->where('ho.idCursos = ?', $idcurso);
        $select->where('ca.idAsignatura = ?', $idAsignatura);
        $select->order("ca.idAsignatura");

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    // FIN ASISTENCIAS

    public function getdiaseventosJSON($idperiodo, $idestablecimiento, $idcurso)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarios'), '*')
            ->joinLeft(array('cev' => 'calendarioEvento'), 'cev.idCalendario=ce.idCalendario')
            ->joinLeft(array('pe' => 'periodo'), 'pe.idPeriodo=ce.idPeriodo')
            ->joinLeft(array('te' => 'tipoEvento'), 'te.idTipoEvento=cev.tipoEvento');
        $select->where('ce.idEstablecimiento = ' . $idestablecimiento);
        $select->where('ce.idCursos = ' . $idcurso);
        $select->where('ce.idPeriodo = ' . $idperiodo);
        $select->where('ce.tipoCalendario = 2');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function getEventosEstablecimientoJSON($idperiodo, $idestablecimiento)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarios'), '*')
            ->joinLeft(array('pe' => 'periodo'), 'pe.idPeriodo=ce.idPeriodo');
        $select->where('ce.idEstablecimiento = ' . $idestablecimiento);
        $select->where('ce.idPeriodo = ' . $idperiodo);
        $select->where('ce.tipoCalendario = 1');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


    public function listarAsignaturasDocenteJSON($idCursos, $idCuenta, $idperiodo, $idEstablecimiento)
    {


        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'), array('ho.idCursos', 'ho.idAsignatura'))
            ->joinLeft(array('cro' => 'cuentaRoles'), 'cro.idCuenta = ho.idCuenta', array());
        $select->where('ho.idPeriodo=?', $idperiodo);
        $select->where('ho.idCuenta=?', $idCuenta);
        $select->where('ho.idCursos=?', $idCursos);
        $select->where('cro.idRol=?', 2); // Solo Docentes
        $select->where('cro.idEstablecimiento=?', $idEstablecimiento);
        $select->where('cro.estadoCuenta = 1');
        $select->group('ho.idCuenta');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;
    }

}
