<?php

class Application_Model_DbTable_Json_2 extends Zend_Db_Table_Abstract
{
    protected $_name = 'jsondetalle';
    protected $_primary = 'idJson';

    // ALUMNOS
    public function listarAlumnosJSON($periodo, $idEstablecimiento = null)
    {
        // $db= new Zend_Db_Table("AlumnosActual");
        //devuelve todos los registros de la tabla


        // $select->setIntegrityCheck(false)
        //     ->from(array('ho' => 'horario'))
        //     ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos')
        //     ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');

        //     // $select->where('ho.idPeriodo=' . $periodo);
        // if (!is_null($idEstablecimiento)) {
        //     $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        // }
        // $select->group('ho.idCursos');



        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'))
            ->join(array('e' => 'cursosactual'), 'ho.idCursos = e.idCursos', array())
            ->join(array('p' => 'AlumnosActual'), 'e.idCursos = p.idCursosActual', array ('idAlumnosActual', 'idAlumnos', 'fechaInscripcion', 'idEstadoActual', 'comunaActual','calle','numeroCasa','villa','correo','telefono', 'celular', 'numeroMatricula'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos',array('rutAlumno','nombres','apaterno','amaterno','fechanacimiento','sexo'))
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento', array('idEstablecimiento'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo', array())
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado', array('idCodigoGrado'));

        if (!is_null($idEstablecimiento)) {
            $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('p.idPeriodoActual=' . $periodo);

        // $select->where('al.numeromatricula > 0');
        $select->group('p.idAlumnos');
        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);
    }


    public function listarAlumnosCursoJSON($idCurso, $periodo, $idEstablecimiento = null)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'), array('idAlumnosActual', 'fechaInscripcion', 'idCursosActual', 'idEstadoActual', 'fechaInscripcion', 'numeroMatricula', 'ordenAlumno', 'junaeb'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos'))
            // ->from(array('p' => 'AlumnosActual'), array('idAlumnosActual', 'fechaInscripcion', 'idCursosActual', 'idEstadoActual', 'numeroMatricula', 'ordenAlumno'))
            // ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos', 'junaeb', 'numeromatricula'))
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos', array())
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento', array('idEstablecimiento'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo', array())
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado', array('idCodigoGrado'));



        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idCursosActual=' . $idCurso);
        $select->where('p.idEstadoActual=1');
        if (!is_null($idEstablecimiento)) {
            $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        }

        // $select->where('al.numeromatricula > 0');

        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);
    }


    public function listarAlumnosAsignaturaPorCursoJSON($idAlumno, $idCurso, $periodo, $idEstablecimiento = null) {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'), array())
            ->join(array('ho' => 'horario'), 'p.idCursosActual = ho.idCursos', array('idAsignatura'))
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos', array());

        if (!is_null($idEstablecimiento)) {
            $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('p.idAlumnosActual=' . $idAlumno);
        $select->where('ho.idPeriodo=' . $periodo);
        $select->where('ho.idCursos=' . $idCurso);

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
            ->join(array('apo' => 'apoderados'), 'al.idApoderado = apo.idApoderado')
            ->joinLeft(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento');

        if (!is_null($idEstablecimiento)) {
            $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('p.idPeriodoActual=' . $periodo);

        return $this->fetchAll($select);
    }

    public function listarAlumnosApoderadoSecundarioJSON($periodo, $idEstablecimiento = null)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual', array('idAlumno', 'idApoderados')))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('apo' => 'apoderados'), 'al.idApoderados = apo.idApoderado')
            ->joinLeft(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento');

        if (!is_null($idEstablecimiento)) {
            $select->where('e.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('p.idPeriodoActual=' . $periodo);

        return $this->fetchAll($select);
    }
    // FIN ALUMNOS


    // APODERADOS
    public function listarApoderadosPrincipalJSON($idPeriodo = null, $idEstablecimiento = null) {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('apo' => 'apoderados'), array('apo.*', 'act.idApoderado as alumnoIdApoderado'))
            ->joinLeft(array('act' => 'AlumnosActual'), 'act.idApoderado = apo.idApoderado')
            ->joinLeft(array('alu' => 'alumnos'), 'alu.idAlumnos = act.idAlumnos')
            ->joinLeft(array('alc' => 'alumnosClinico'), 'alc.idAlumnos = alu.idAlumnos')
            ->joinLeft(array('cur' => 'cursosactual'), 'cur.idCursos = act.idCursosActual')
            ->joinLeft(array('ho' => 'horario'), 'cur.idCursos = ho.idCursos');

        // $select->from(array('ho' => 'horario'))
        //     ->joinLeft(array('act' => 'AlumnosActual'), 'act.idCursosActual = cur.idCursos')
        //     ->joinLeft(array('alu' => 'alumnos'), 'alu.idAlumnos = act.idAlumnos')
        //     ->joinLeft(array('apo' => 'apoderados'), 'alu.idApoderado = apo.idApoderado')
        //     ->joinLeft(array('alc' => 'alumnosClinico'), 'alc.idAlumnos = alu.idAlumnos');


        if (!is_null($idPeriodo)) {
            $select->where('act.idPeriodoActual= ?', $idPeriodo);
        }

        if (!is_null($idEstablecimiento)) {
            $select->where('cur.idEstablecimiento= ?', $idEstablecimiento);
        }

        $select->group('apo.idApoderado');

        return $this->fetchAll($select);

    }

    public function listarApoderadosSecundarioJSON($idPeriodo = null, $idEstablecimiento = null) {

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

        return $this->fetchAll($select);

    }
    // FIN APODERADOS


    // CUENTAS
    public function listarDocentesJSON($idPeriodo, $idEstablecimiento = null)
    {
        $select = $this->select();

        //Add next line
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


//    public function listarDocentesCEducativaJSON($idCurso, $idPeriodo, $idEstablecimiento = null)
//    {
//        $select = $this->select();
//
//        //Add next line
//        $select->setIntegrityCheck(false)
//            ->from(array('ho' => 'horario'))
//            ->joinLeft(array('cus' => 'cuentasUsuario'), 'cus.idCuenta = ho.idCuenta')
//            ->joinLeft(array('cro' => 'cuentaRoles'), 'cus.idCuenta = ho.idCuenta')
//            ->joinLeft(array('ro' => 'roles'), 'ro.idRoles = cro.idRol')
//            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = cro.idEstablecimiento')
//            ->where('ho.idPeriodo=?', $idPeriodo)
//            ->where('ho.idCursos=?', $idCurso)
//            ->where('cro.idRol=?', 2);  // Solo Docentes
//
//        if (!is_null($idEstablecimiento)) {
//            $select->where('cro.idEstablecimiento=?', $idEstablecimiento);
//        }
//
//
//        return $this->fetchAll($select);
//    }


    public function listarDocentesPorCursosJSON($idCurso, $idPeriodo, $idEstablecimiento = null)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'), array('idCuenta'))
            // ->joinLeft(array('bl' => 'bloque'), 'ho.bloque = bl.idBloque', array('idEstablecimiento'))
            ->joinLeft(array('cc' => 'cursosactual'), 'ho.idCursos = cc.idCursos', array('idCuentaJefe', 'idEstablecimiento'))
            // ->joinLeft(array('cc' => 'cursosactual'), 'ho.idCursos = cc.idCursos', array('idCuentaJefe'))
            ->where('ho.idCursos = ?', $idCurso)
            ->where('ho.idPeriodo = ?', $idPeriodo);

            if (!is_null($idEstablecimiento)) {
                $select->where('cc.idEstablecimiento = ?', $idEstablecimiento);
            }

            $select->group('ho.idCuenta');

        return $this->fetchAll($select);
    }


    public function listarDocentesCursosJSON($idCuenta, $idEstablecimiento = null)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('cro' => 'cuentaRoles'))
            ->joinLeft(array('cuc' => 'cuentascurso'), 'cuc.idCuentaRol = cro.id')
            ->where('cro.idRol=?', 2); // Solo Docentes

            if (!is_null($idEstablecimiento)) {
                $select->where('cro.idEstablecimiento=?', $idEstablecimiento);
            }

            $select->where('cro.idCuenta=?', $idCuenta);
            $select->where('cro.estadoCuenta = 1');



        return $this->fetchAll($select);
    }
    // FIN CUENTAS


    // ESTABLECIMIENTOS
    public function listarEstablecimientosJSON($idPeriodo = null, $idEstablecimiento = null)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('e' => 'establecimiento'));

            if(!is_null($idEstablecimiento)) {
               $select->where('e.idEstablecimiento=?', $idEstablecimiento);
            }

        return $this->fetchAll($select);
    }

    public function listarRelEstablecimientosJSON($idPeriodo = null, $idEstablecimiento = null)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('e' => 'establecimiento'))
            ->joinLeft(array('ec' => 'establecimientoConfiguracion'), 'e.idestablecimiento = ec.idestablecimiento');

            if(!is_null($idEstablecimiento)) {
               $select->where('e.idEstablecimiento=?', $idEstablecimiento);
            }

        return $this->fetchAll($select);
    }
    // FIN ESTABLECIMIENTOS


    // SOSTENEDORES
    public function listarSostenedoresJSON($idPeriodo = null, $idEstablecimiento = null)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('est' => 'establecimiento'), array('sos.*'))
            ->joinLeft(array('sos' => 'sostenedor'), 'sos.idSostenedor = est.idSostenedor');

            if (!is_null($idEstablecimiento)) {
                $select->where('est.idEstablecimiento=?', $idEstablecimiento);
            }


        return $this->fetchAll($select);
    }
    // FIN SOSTENEDORES


    public function listarAuditoriaJSON($idPeriodo, $idEstablecimiento)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('cu' => 'cuentasUsuario'),array('cu.usuario', 'cu.idCuenta'))
            ->joinLeft(array('cr' => 'cuentaRoles'), 'cu.idCuenta = cr.idCuenta', array(''));

        if (!is_null($idEstablecimiento)) {
            $select->where('cr.idEstablecimiento=?', $idEstablecimiento);
        }
        if (!is_null($idPeriodo)) {
            $select->where('cr.idPeriodo=?', $idPeriodo);
        }

        $select->where('cr.estadoCuenta= 1');

        $stmt = $select->query();
        $docentes = $stmt->fetchAll();

        $auditorias = array();
        foreach ( $docentes as $key => $docente ) {

            $auditorias[$key]['RUT'] = $docente['usuario'];
            $auditoria = $this->select();
            $auditoria->setIntegrityCheck(false)
                    ->from(array('au' => 'auditoria'),
                        array('au.fechaAuditoria', 'au.timezone', 'token'))
                    ->joinLeft(array('ad' => 'auditoriadetalle'), 'au.idAuditoria = ad.idAuditoria', array('ad.TableName', 'ad.FieldName', 'ad.OperationType', 'ad.OldData', 'ad.NewData'));
            $auditoria->where('au.idCuenta= ?', $docente['idCuenta']);
            $stmtd = $auditoria->query();
            $auditoria = $stmtd->fetchAll();

            if (!is_null($auditoria)) {
                $auditorias[$key]['auditoria'] = $auditoria;
            }

        }
        return $auditorias;
    }

    public function listarAsignaturasCursosJSON($idCursos, $idPeriodo, $idEstablecimiento = null)
    {
        //devuelve todos los registros de la tabla
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
    public function listarAsignaturasJSON($periodo = null, $idEstablecimiento = null)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'))
            ->join(array('as' => 'asignaturas'), 'ho.idAsignatura = as.idAsignatura')
            ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos')
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');


        $select->where('as.estado = 1');
        // $select->where('ca.idCodigoGrado != 4');
        // $select->where('ca.idCodigoGrado != 5');
        $select->group('ho.idAsignatura');
        return $this->fetchAll($select);
    }

    //aca
    public function listarControlContenidosJSON ($idPeriodo, $idEstablecimiento, $idCurso, $idAsignatura) {
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('cc' => 'controlcontenidos'), array('idControlContenido', 'fechaControl'))
            ->join(array('cd' => 'controlcontenidosdetalle'), 'cc.idControlContenido = cd.idControlContenido', array('idControlContenidoDetalle', 'contenidos', 'idCuenta as idDocente'))
            // ->join(array('ho' => 'horario'), 'cd.idHorario = ho.idHorario', array('idHorario', 'dia'))
            ->join(array('ho' => 'horario'), 'cd.idHorario = ho.idHorario', array('idHorario', 'dia', 'tiempoInicio', 'tiempoTermino'))
            // ->join(array('bl' => 'bloque'), 'ho.bloque = bl.idBloque', array('idBloque', 'nombreBloque', 'tiempoInicio', 'tiempoTermino', 'tipoBloque'))
            ->join(array('ca' => 'cursosactual'), 'cc.idCursos = ca.idCursos', array('idEstablecimiento'));

        $select->where('cc.idPeriodo = ?', $idPeriodo);
        $select->where('cc.idCursos = ?', $idCurso);

        $select->where('ho.idAsignatura = ?', $idAsignatura);
        $select->where('ca.idEstablecimiento = ?', $idEstablecimiento);

        return $this->fetchAll($select);

    }


    //borrar
    public function listarBloquesAsignaturasJSON($idPeriodo, $idEstablecimiento, $idCurso = null, $idAsignatura) {  // bloque
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('h' => 'horario'), array('bloque', 'dia'))
            ->join(array('b' => 'bloque'), 'h.bloque = b.idBloque', array('nombreBloque', 'tiempoInicio', 'tiempoTermino', 'tipoBloque'));

        $select->where('h.idPeriodo = ?', $idPeriodo);
        if (!is_null($idCurso)) {
            $select->where('h.idCursos = ?', $idCurso);
        }
        $select->where('h.idAsignatura = ?', $idAsignatura);
        $select->where('B.idEstablecimiento = ?', $idEstablecimiento);

        return $this->fetchAll($select);
    }

    public function listarAsignaturasAlumnoJSON($idCursos)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ac' => 'asignaturascursos'))
            ->join(array('as' => 'asignaturas'), 'ac.idAsignatura = as.idAsignatura');


        $select->where('as.estado = 1');
        $select->where('ac.idCursos = '. $idCursos);

        return $this->fetchAll($select);
    }
    // FIN ASIGNATURAS


    // NIVELES
    public function listarNivelesJSON($idPeriodo, $idEstablecimiento )
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('cg' => 'codigogrados'));

        return $this->fetchAll($select);
    }
    // FIN NIVELES

    public function listarNivelesCursosJSON($idPeriodo, $idEstablecimiento )
    {
        // Consultar el nivel de cada curso
        // consultar la jornada
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'))
            ->join(array('ca' => 'cursosactual'),  'ho.idCursos = ca.idCursos', array('idCursos', 'idCodigoGrado',
                'idCodigoTipo', 'tipoJornada', 'codigoSector', 'codigoEspecialidad', 'letra'))
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado', array('idCodigoGrado',
                'nombreGrado', 'idCodigo', 'idGrado'));

            // $select->where('ca.idPeriodo=' . $idPeriodo); // No cuenta para los cursos

        if (!is_null($idEstablecimiento)) {
            $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        }

        $select->group('ho.idCursos');

        // $select->where('ca.idCodigoGrado != 4');
        // $select->where('ca.idCodigoGrado != 5');

        return $this->fetchAll($select);
    }


    // CURSOS
    public function listarCursosJSON($periodo, $idEstablecimiento = null)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ho' => 'horario'))
            ->join(array('ca' => 'cursosactual'), 'ho.idCursos = ca.idCursos')
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');

            // $select->where('ho.idPeriodo=' . $periodo);
        if (!is_null($idEstablecimiento)) {
            $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->group('ho.idCursos');
        // $select->where('ca.idCodigoGrado != 4');
        // $select->where('ca.idCodigoGrado != 5');

        return $this->fetchAll($select);
    }


    // OBSERVACIONES
    public function listarObservacionesJSON($idPeriodo = null, $idEstablecimiento = null) {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('obs'  => 'observaciones'))
            ->join(array('alu' => 'alumnos'        ), 'alu.idAlumnos         = obs.idAlumnos')
            ->join(array('asi' => 'asignaturas'    ), 'asi.idAsignatura      = obs.idAsignatura')
            ->joinLeft(array('cur' => 'cursosactual'   ), 'cur.idCursos          = obs.idCursos')
            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = cur.idEstablecimiento');

        if (!is_null($idPeriodo)) {
            $select->where('obs.idPeriodo= ?', $idPeriodo);
        }

        $select->where('obs.idTipo= 2');

        return $this->fetchAll($select);
    }
    // FIN OBSERVACIONES


    // ASISTENCIAS
    public function listarAsistenciaDiariaAlumnoJSON($idalumno,$idperiodo,$idcurso)
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
            ->joinLeft(array('h' => 'horario'), 'a.idHorario = h.idHorario')
            ->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
        //$select->where('al.idAlumnos NOT in (?)', array('1551'));
        $select->where('alu.idEstadoActual = 1');
        $select->where('as.idAlumnos = ?', $idalumno);
        $select->where('co.idPeriodo = ?', $idperiodo);
        $select->where('co.idCursos = ?', $idcurso);
        $select->order("b.tiempoInicio");
        $select->group('co.fechaControl');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function listarAsistenciaBloqueAlumnoJSON($idalumno, $idperiodo, $idcurso, $idAsignatura)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('cav' => 'controlasistenciavalores'), array('valorasistencia', 'tipoAsistencia'))
            ->joinLeft(array('ca' => 'controlasistencia'), 'cav.idControlAsistencia = ca.idControlAsistencia', array('idAsignatura'))
            ->joinLeft(array('ho' => 'horario'), 'ca.idHorario = ho.idHorario', array(''))
            ->joinLeft(array('bl' => 'bloque'), 'ho.bloque = bl.idBloque', array(''))
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

    public function getdiaseventosJSON($idperiodo, $idestablecimiento)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ce' => 'calendarios'),'*')
            ->joinLeft(array('cev'=>'calendarioEvento'),'cev.idCalendario=ce.idCalendario')
            ->joinLeft(array('pe'=>'periodo'),'pe.idPeriodo=ce.idPeriodo')
            ->joinLeft(array('te'=>'tipoEvento'),'te.idTipoEvento=cev.tipoEvento');
        $select->where('ce.idEstablecimiento = ' . $idestablecimiento);
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
        $select->from(array('ce' => 'calendarios'),'*')
//            ->joinLeft(array('cev'=>'calendarioEvento'),'cev.idCalendarioEstablecimiento=ce.idCalendarioEstablecimiento')
            ->joinLeft(array('pe'=>'periodo'),'pe.idPeriodo=ce.idPeriodo');
//            ->joinLeft(array('te'=>'tipoEvento'),'te.idTipoEvento=cev.tipoEvento');
        $select->where('ce.idEstablecimiento = ' . $idestablecimiento);
        $select->where('ce.idPeriodo = ' . $idperiodo);
        $select->where('ce.tipoCalendario = 1');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


    public function listarAsignaturasDocenteJSON($idCursos, $idCuenta, $idperiodo, $idEstablecimiento = null)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
//            ->distinct('ho.idCuenta')
            ->from(array('ho' => 'horario'), array('ho.idCursos', 'ho.idAsignatura'))

            ->joinLeft(array('cro' => 'cuentaRoles'), 'cro.idCuenta = ho.idCuenta', array());

//        $select->where('ho.idCursos=?', $idCurso);
        $select->where('ho.idPeriodo=?', $idperiodo);
        $select->where('ho.idCuenta=?', $idCuenta);
        $select->where('ho.idCursos=?', $idCursos);
        $select->where('cro.idRol=?', 2); // Solo Docentes
        if (!is_null($idEstablecimiento)) {
            $select->where('cro.idEstablecimiento=?', $idEstablecimiento);
        }
        $select->where('cro.estadoCuenta = 1');
        $select->group('ho.idCuenta');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;
    }

}
