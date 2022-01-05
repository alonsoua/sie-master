<?php

class Application_Model_DbTable_Notas extends Zend_Db_Table_Abstract
{

    protected $_name = 'notas';


    public function get($rut)
    {
        $RUT = (int)$rut;
        //devuelve todos los registros de la tabla

        //$this->fetchRow devuelve fila donde idPeriodo = $idPeriodo
        $row = $this->fetchRow('idNotas = ' . $RUT);
        if (!$row) {
            throw new Exception("No se Encuentra el dato $RUT");
        }

        return $row->toArray();
    }


    public function agregar($rut, $asignatura, $curso, $nota, $usuario, $idprueba, $fecha, $periodo)
    {
        $data = array('idAlumnos' => $rut, 'idAsignatura' => $asignatura, 'idCursos' => $curso, 'nota' => $nota, 'idCuenta' => $usuario, 'idPruebas' => $idprueba, 'fechaNota' => $fecha, 'idPeriodo' => $periodo,
        );
        $this->insert($data);

    }


    public function cambiar($id, $nota)
    {
        $data = array('nota' => $nota);
        //$this->update cambia datos de Alumno con Rut= $rut
        $this->update($data, 'idNotas = ' . (int)$id);

    }

    public function actualizarborrar($id, $estado)
    {
        $data = array('estadoNota' => $estado);
        $this->update($data, 'idPruebas = ' . (int)$id);

    }

    public function actualizarnotaponderacion($id, $estado)
    {
        $data = array('valorGuia' => $estado);
        $this->update($data, 'idNotas = ' . (int)$id);

    }


    public function borrar($id)
    {
        //$this->delete borrar album donde RBD=$rbd
        $this->delete('idPruebas =' . (int)$id);

    }

    public function borrarusuarios($idalumno, $idcurso, $idperiodo)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idAlumnos = ?', $idalumno);
        $where[] = $this->getAdapter()->quoteInto('idCursos= ?', $idcurso);
        $where[] = $this->getAdapter()->quoteInto('idPeriodo= ?', $idperiodo);
        $this->delete($where);
    }

    public function listarbasica($rut, $curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), '*');

        $select->join(array('c' => 'cursos'),
            'c.idCursos =a.idCursos');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->where('a.idCuenta = ?', $rut);
        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura =a.idAsignatura');

        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->group('a.idPruebas');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarnotasbasica($id, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), '*');
        $select->join(array('al' => 'alumnos', array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno')),
            'al.idAlumnos =a.idAlumnos');

        $select->join(array('alu' => 'AlumnosActual'),
            'a.idAlumnos =alu.idAlumnos');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->join(array('asc' => 'asignaturascursos'),
            'asc.idAsignatura =a.idAsignatura');

        $select->join(array('as' => 'asignaturas'), 'as.idAsignatura = asc.idAsignatura', array('idAsignatura', 'nombreAsignatura'));

        $select->where('a.idPruebas = ?', $id);
        $select->where('alu.idPeriodoActual = ?', $idperiodo);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->where('alu.idEstadoActual = 1');
        $select->order('alu.ordenAlumno');
        $select->group("a.idAlumnos");


        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function generanotasalumnobasica($id, $idperiodo, $idcurso)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();


        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*')
            ->join(array('e' => 'cursosactual'), 'a.idCursos = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->join(array('al' => 'alumnos', array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno', 'sexo', 'idCursos', 'numeromatricula')),
            'al.idAlumnos =a.idAlumnos');

        $select->where('a.idAlumnos = ?', $id);
        $select->where('a.idPeriodo = ?', $idperiodo);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order("a.idAsignatura");
        $select->group('a.idAsignatura');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function generasolonotas($id, $idcurso, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idAlumnos = ?', $id);
        $select->where('as.promedio = 1');
        $select->where('a.idPeriodo = ?', $idperiodo);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order("pr.tiempo");
        $select->order("a.idAsignatura");
        $select->group("pr.idEvaluacion");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function generasolonotasperiodo($id, $segmento, $estado, $idperiodo, $idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), '*');

        $select->joinLeft(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->joinLeft(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');


        $select->where('a.idAlumnos = ?', $id);
        $select->where('pr.tiempo = ?', $segmento);
        $select->where('as.promedio = ?', $estado);
        $select->where('a.idPeriodo = ?', $idperiodo);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order("pr.tiempo");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group("pr.idEvaluacion");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }


    public function generasolonotasanual($id, $idcurso)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from('notas', array('avg(nota) as promedio', 'idAsignatura'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =notas.idPruebas', array('idEvaluacion', 'tiempo'));

        $select->where('notas.idAlumnos = ?', $id);
        $select->where('notas.idCursos = ?', $idcurso);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order("pr.tiempo");
        $select->order("pr.fechaEvaluacion");
        $select->group("pr.tiempo");
        $select->group("notas.idAsignatura");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarinforme($rut, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), array('idAlumnos', 'idCursos', 'idPeriodo', 'idCuenta'));

        $select->join(array('c' => 'cursos'),
            'c.idCursos =a.idCursos');

        $select->join(array('ala' => 'AlumnosActual'),
            'ala.idAlumnos =a.idAlumnos');

        $select->join(array('al' => 'alumnos'),
            'al.idAlumnos =ala.idAlumnos');

        $select->join(array('es' => 'establecimiento'),
            'es.idEstablecimiento =c.idEstablecimiento');

        $select->where('c.idCuentaJefe = ?', $rut);
        //$select->where('a.idCuenta = ?', $rut );

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('ala.idEstadoActual = 1');
        $select->where('a.estadoNota != 9');
        $select->order("es.idEstablecimiento");
        $select->order("c.idNiveles");
        $select->order("c.nombreCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        $select->group('a.idAlumnos');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarinformeesta($id, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), array('idAlumnos', 'idCursos', 'idPeriodo', 'idCuenta'));

        $select->join(array('c' => 'cursos'),
            'c.idCursos =a.idCursos');

        $select->join(array('ala' => 'AlumnosActual'),
            'ala.idAlumnos =a.idAlumnos');

        $select->join(array('al' => 'alumnos'),
            'al.idAlumnos =ala.idAlumnos');

        $select->join(array('es' => 'establecimiento'),
            'es.idEstablecimiento =c.idEstablecimiento');

        $select->where('es.idEstablecimiento = ?', $id);
        //$select->where('a.idCuenta = ?', $rut );

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('ala.idEstadoActual = 1');
        $select->where('a.estadoNota != 9');
        $select->order("es.idEstablecimiento");
        $select->order("c.idNiveles");
        $select->order("c.nombreCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        $select->group('a.idAlumnos');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarinformeadmin($periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), array('idAlumnos', 'idCursos', 'idPeriodo', 'idCuenta'));

        $select->join(array('c' => 'cursosactual'),
            'c.idCursos =a.idCursos');

        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'c.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'c.idGrado= g.idCodigoGrado');

        $select->join(array('ala' => 'AlumnosActual'),
            'ala.idAlumnos =a.idAlumnos');

        $select->join(array('al' => 'alumnos'),
            'al.idAlumnos =ala.idAlumnos');

        $select->join(array('es' => 'establecimiento'),
            'es.idEstablecimiento =c.idEstablecimiento');

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('ala.idEstadoActual = 1');
        $select->where('a.estadoNota != 9');
        $select->order("es.idEstablecimiento");
        $select->order("c.idNiveles");
        $select->order("c.nombreCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        $select->group('a.idAlumnos');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listaradmin($curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), array('a.idNotas', 'a.idPruebas', 'a.idCursos', 'a.idAsignatura'));

        $select->join(array('c' => 'cursosactual'),
            'c.idCursos =a.idCursos');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'c.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'c.idCodigoGrado= g.idCodigoGrado');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura =a.idAsignatura');
        $select->join(array('asi' => 'asignaturas'),
            'as.idAsignatura =asi.idAsignatura');

        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);

        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->group('a.idPruebas');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarpordocente($idcuenta, $curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), '*');

        $select->join(array('c' => 'cursosactual'),
            'c.idCursos =a.idCursos');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'c.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'c.idCodigoGrado= g.idCodigoGrado');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('asc' => 'asignaturascursos'),
            'asc.idAsignatura =a.idAsignatura');
        $select->join(array('as' => 'asignaturas'), 'as.idAsignatura = asc.idAsignatura',
            array('idAsignatura', 'nombreAsignatura'));

        $select->where('a.idCuenta = ?', $idcuenta);
        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);

        $select->where('asc.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->group('a.idPruebas');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function generanivel($id)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'alumnos'), '*');

        $select->join(array('c' => 'cursos'),
            'c.idCursos =a.idCursos');

        $select->where('a.idAlumnos = ?', $id);


        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listaralumnoscurso($curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));

        $select->where('p.idCursosActual=?', $curso);
        $select->where('p.idPeriodoActual = ?', $periodo);
        $select->where('p.idEstadoActual = 1');
        $select->order("p.ordenAlumno");
        $select->order("al.apaterno");
        $select->order("al.amaterno");
        $select->order("al.nombres");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listaralumno($idalumno, $curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));

        $select->where('p.idCursosActual=?', $curso);
        $select->where('p.idPeriodoActual = ?', $periodo);
        $select->where('p.idAlumnosActual = ?', $idalumno);
        $select->where('p.idEstadoActual = 1');
        $select->order("p.ordenAlumno");
        $select->order("al.apaterno");
        $select->order("al.amaterno");
        $select->order("al.nombres");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarnotasporalumno($id, $periodo, $idcurso)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');

        $select->joinLeft(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idAlumnos = ?', $id);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.tiempo NOT IN(?)', array(6));
        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function listarnotasporalumnoasignatura($id, $periodo, $idcurso, $idasigantura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');

        $select->joinLeft(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idAlumnos = ?', $id);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('a.idAsignatura= ?', $idasigantura);
        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function listarnotasporalumnoperiodo($id, $periodo, $segmento, $idcurso)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), '*');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idAlumnos = ?', $id);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function listarnotasporalumnoperiodoestado($id, $periodo, $segmento, $idcurso)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), '*');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idAlumnos = ?', $id);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('as.promedio = 0');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function listarnotascurso($curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group("a.idPruebas");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarnotascursoperiodo($curso, $periodo, $segmento)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('pr.tiempo IN(?)', $segmento );
        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group("a.idPruebas");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarnotascursoperiodo2($curso, $periodo, $segmento)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);
        //$select->where('pr.tiempo=?', $segmento);
        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("pr.fechaEvaluacion");
        $select->group("a.idPruebas");

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listaradmincursos($curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'), '*');

        $select->join(array('c' => 'cursos'),
            'c.idCursos =a.idCursos');
        $select->join(array('al' => 'alumnos'),
            'al.idAlumnos =a.idAlumnos');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura =a.idAsignatura');

        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->group('a.idPruebas');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listartotalprueba($curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('as.promedio = 1');
        $select->where('pr.estadoev= 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->group('a.idPruebas');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listartotalpruebaperiodo($curso, $periodo, $segmento)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idCursos = ?', $curso);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('as.promedio = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->group('a.idPruebas');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function listarpromedios($id, $periodo, $segmento, $asignatura, $idcurso)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'notas'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idAlumnos = ?', $id);

        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('a.idAsignatura=?', $asignatura);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('a.idCursos = ?', $idcurso);

        $select->where('as.promedio = 1');
        $select->where('as.estado = 1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;
    }

    public function listarpromedioasignatura($id, $idperiodo, $segmento, $idasignatura, $idcurso)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), array('promedio' => new Zend_Db_Expr('AVG(nota)')));
        $select->join(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas');
        $select->where('a.idAlumnos  = ?', $id);
        $select->where('a.idPeriodo  = ?', $idperiodo);
        $select->where('a.idAsignatura  = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function listarpromedioasignaturafinal($id, $idperiodo, $idasignatura, $idcurso)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), array('promedio' => new Zend_Db_Expr('AVG(nota)')));
        $select->where('a.idAlumnos  = ?', $id);
        $select->where('a.idPeriodo  = ?', $idperiodo);
        $select->where('a.idAsignatura  = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('a.estadoNota != 9');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;
    }

    public function generanotasalumnobasicapre($id, $idperiodo, $idcurso)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');
        $select->join(array('c' => 'cursos'),
            'c.idCursos =a.idCursos');
        $select->join(array('es' => 'establecimiento', array('idEstablecimiento', 'nombreEstablecimiento')),
            'es.idEstablecimiento =c.idEstablecimiento');

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->join(array('al' => 'alumnos', array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno', 'sexo', 'idCursos', 'numeromatricula', 'comuna')),
            'al.idAlumnos =a.idAlumnos');
        $select->where('a.idAlumnos = ?', $id);
        $select->where('a.idPeriodo = ?', $idperiodo);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->join(array('as' => 'asignaturas'),
            'as.idAsignatura =a.idAsignatura');
        $select->order("a.idAsignatura");
        $select->group('a.idAsignatura');
        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;

    }

    public function validar($idasignatura, $idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');

        $select->where('a.idAsignatura = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('a.idPeriodo = ?', $idperiodo);
        $select->where('a.estadoNota != 9');

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        if (!$result) {
            return true;
        } else {
            return false;
        }

    }

    public function validarnotaalumno($idalumno, $idprueba, $idasignatura, $idcurso, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');

        $select->where('a.idAlumnos= ?', $idalumno);
        $select->where('a.idPruebas = ?', $idprueba);
        $select->where('a.idAsignatura = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('a.idPeriodo = ?', $idperiodo);
        $select->where('a.estadoNota != 9');

        $result = $this->fetchAll($select)->toArray();

        if ($result) {
            return false;
        } else {
            return true;
        }

    }

    public function validarnotaalumnoguia($idalumno, $idguia)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'valoresGuia'), '*');
        $select->where('a.idAlumnos= ?', $idalumno);
        $select->where('a.idGuia = ?', $idguia);

        $result = $this->fetchAll($select)->toArray();

        if ($result) {
            return false;
        } else {
            return true;
        }

    }

    public function validarnotaalumnoponderacion($idnota)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'ponderacionnotas'), '*');
        $select->where('a.idNotasPonderacion= ?', $idnota);

        $result = $this->fetchAll($select)->toArray();

        if ($result) {
            return false;
        } else {
            return true;
        }

    }

    public function listarcantidadnotasasignatura($curso, $periodo, $segmento, $idasignatura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'));
        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->where('a.idCursos = ?', $curso);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('a.idAsignatura=?', $idasignatura);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function listarcantidadnotasanual($curso, $periodo, $idasignatura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'));
        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->where('a.idCursos = ?', $curso);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('a.idAsignatura=?', $idasignatura);
        $select->where('pr.tipoNota=1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function listarcantidadnotasanualalumno($curso, $periodo, $idasignatura, $idalumno)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'));
        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->where('a.idCursos = ?', $curso);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('a.idAsignatura=?', $idasignatura);
        $select->where('a.idAlumnos=?', $idalumno);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function listarpromedioalumnotaller($id, $periodo, $segmento, $idcurso, $idasignatura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');
        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->join(array('as' => 'asignaturascursos'),
            'as.idAsignatura = a.idAsignatura');

        $select->where('a.idAlumnos = ?', $id);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('a.idAsignatura = ?', $idasignatura);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order("a.idAlumnos");
        $select->order("a.idAsignatura");
        $select->order("a.idPruebas");
        $select->group('a.idPruebas');

        return $this->fetchAll($select)->toArray();

    }

    public function getnotasasignatura($idevaluacion, $curso, $periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'));
        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('alu' => 'alumnos'), 'al.idAlumnos = alu.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));
        $select->join(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas');
        $select->joinleft(array('po' => 'ponderacionnotas'), 'po.idNotasPonderacion=a.idNotas');

        $select->where('a.idCursos = ?', $curso);
        $select->where('al.idCursosActual = ?', $curso);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('al.idPeriodoActual = ?', $periodo);
        $select->where('a.idPruebas=?', $idevaluacion);
        $select->where('al.idEstadoActual=1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order("al.ordenAlumno");
        $select->group('a.idAlumnos');


        return $this->fetchAll($select)->toArray();

    }

    public function getnotasasignaturaporalumno($idevaluacion, $curso, $periodo, $alumno)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'));
        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('alu' => 'alumnos'), 'al.idAlumnos = alu.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));
        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');

        $select->where('a.idCursos = ?', $curso);
        $select->where('al.idCursosActual = ?', $curso);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('al.idPeriodoActual = ?', $periodo);
        $select->where('a.idPruebas=?', $idevaluacion);
        $select->where('a.idAlumnos =?', $alumno);
        $select->where('al.idEstadoActual=1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        return $this->fetchAll($select)->toArray();

    }

    public function generanotasalumnobasicapresegmento($id, $idcurso, $idperiodo, $segmento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');
        $select->joinLeft(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas');
        $select->joinLeft(array('asc' => 'asignaturascursos'), 'a.idAsignatura = asc.idAsignatura');
        $select->joinLeft(array('as' => 'asignaturas'), 'as.idAsignatura = asc.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('n' => 'nucleos'), 'n.idNucleo = as.idNucleo');
        $select->joinLeft(array('am' => 'ambitos'), 'am.idAmbito = n.idAmbito');

        $select->where('a.idAlumnos = ?', $id);
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.idPeriodo = ?', $idperiodo);
        $select->where('pr.tiempo = ?', $segmento);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order('a.idAsignatura');
        $select->order('a.idNotas DESC');
        $select->group('a.idAsignatura');

        return $this->fetchAll($select)->toArray();

    }

    public function getnotaspresegmento($id, $idperiodo, $segmento, $idasignatura, $idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');
        $select->join(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas');
        $select->join(array('asc' => 'asignaturascursos'), 'a.idAsignatura = asc.idAsignatura');
        $select->join(array('as' => 'asignaturas'), 'as.idAsignatura = asc.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('n' => 'nucleos'), 'n.idNucleo = a.idAsignatura');
        $select->joinLeft(array('am' => 'ambitos'), 'am.idAmbito = n.idAmbito');


        $select->where('a.idAlumnos  = ?', $id);
        $select->where('a.idPeriodo  = ?', $idperiodo);
        $select->where('a.idAsignatura  = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order('a.idAsignatura');
        $select->order('a.idNotas DESC');
        $select->group('a.idNotas');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function getnotaspresegmentogreda($id, $idperiodo, $segmento, $idasignatura, $idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');
        $select->join(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas');
        $select->joinLeft(array('n' => 'nucleos'), 'n.idNucleo = a.idAsignatura');
        $select->joinLeft(array('am' => 'ambitos'), 'am.idAmbito = n.idAmbito');

        $select->where('a.idAlumnos  = ?', $id);
        $select->where('a.idPeriodo  = ?', $idperiodo);
        $select->where('a.idAsignatura  = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');

        $select->order('a.idAsignatura');
        $select->order('a.idNotas DESC');
        $select->group('a.idNotas');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }

    public function getnotasexamenalumno($idevaluacion, $idcurso, $idasignatura, $idperiodo, $segmento, $idalumno)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');

        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('alu' => 'alumnos'), 'al.idAlumnos = alu.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));

        $select->join(array('pr' => 'evaluaciones'),
            'pr.idEvaluacion =a.idPruebas');
        $select->where('pr.idEvaluacion  = ?', $idevaluacion);
        $select->where('a.idPeriodo  = ?', $idperiodo);
        $select->where('a.idAsignatura  = ?', $idasignatura);
        $select->where('al.idCursosActual = ?', $idcurso);
        $select->where('pr.tiempo=?', $segmento);
        $select->where('a.idAlumnos=?', $idalumno);
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        $select->order('al.ordenAlumno');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


    public function cambiarguia($id, $valor)
    {
        $data = array('valorGuia' => $valor);
        $this->update($data, 'idNotas = ' . (int)$id);

    }

    public function agregarnotaguia($rut, $asignatura, $curso, $nota, $usuario, $idprueba, $fecha, $periodo, $valorguia, $porcentaje)
    {
        $data = array('idAlumnos' => $rut, 'idAsignatura' => $asignatura, 'idCursos' => $curso, 'nota' => $nota, 'idCuenta' => $usuario, 'idPruebas' => $idprueba, 'fechaNota' => $fecha, 'idPeriodo' => $periodo, 'valorGuia' => $valorguia, 'porcentajenota' => $porcentaje);
        $this->insert($data);

    }

    public function cambiarporcentaje($id, $nota, $porcentaje)
    {
        $data = array('nota' => $nota, 'porcentajenota' => $porcentaje);
        $this->update($data, 'idNotas = ' . (int)$id);

    }

    //CGV

    public function agregarponderacion($idnota, $valor_10, $nota_10, $valor_30, $nota_30, $valor_60, $nota_60)
    {
        $db = new Zend_Db_Table('ponderacionnotas');
        $data = array('ponderacion_10' => $valor_10, 'nota_10' => $nota_10, 'ponderacion_30' => $valor_30, 'nota_30' => $nota_30, 'ponderacion_60' => $valor_60, 'nota_60' => $nota_60, 'idNotasPonderacion' => $idnota);
        $db->insert($data);

    }

    public function cambiarponderacion($valor_u, $valor_u_n, $valor_d, $valor_d_n, $valor_t, $valor_t_n, $idnota)
    {
        $db = new Zend_Db_Table('ponderacionnotas');
        $data = array('ponderacion_10' => $valor_u, 'nota_10' => $valor_u_n, 'ponderacion_30' => $valor_d, 'nota_30' => $valor_d_n, 'ponderacion_60' => $valor_t, 'nota_60' => $valor_t_n);
        $db->update($data, 'idNotasPonderacion = ' . (int)$idnota);

    }


    //CESA

    public function agregarvaloresguia($valorguia, $formativa, $idGuia, $idalumnos)
    {
        $db = new Zend_Db_Table('valoresGuia');
        $data = array('valorGuias' => $valorguia, 'valorFormativa' => $formativa, 'porcentajeGuia' => 0, 'porcentajeGuiaFinal' => 0, 'idGuia' => $idGuia, 'idAlumnos' => $idalumnos);
        $db->insert($data);

    }

    public function getvaloresguia($idevaluacion, $curso, $periodo, $seg)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'valoresGuia'));
        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('alu' => 'alumnos'), 'al.idAlumnos = alu.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));
        $select->join(array('pr' => 'guiasevaluacion'), 'pr.idGuia =a.idGuia');
        $select->where('al.idCursosActual = ?', $curso);
        $select->where('al.idPeriodoActual = ?', $periodo);
        $select->where('al.idEstadoActual = 1');
        $select->where('a.idGuia=?', $idevaluacion);
        $select->where('pr.tiempoGuia=?', $seg);
        $select->group('a.idAlumnos');
        $select->order("al.ordenAlumno");


        return $this->fetchAll($select)->toArray();

    }

    public function getvaloresguiasegsem($idalumno, $idasignatura, $periodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'valoresGuia'));

        $select->join(array('ge' => 'guiasevaluacion'), 'ge.idGuia = a.idGuia');
        $select->join(array('ev' => 'evaluaciones'), 'ev.idEvaluacion = ge.idEvaluacionGuia', array('idEvaluacion'));

        $select->where('a.idAlumnos=?', $idalumno);
        $select->where('ge.idAsignatura=?', $idasignatura);
        $select->where('ge.idPeriodo = ?', $periodo);
        $select->where('ev.tiempo = 2');


        return $this->fetchAll($select)->toArray();

    }

    public function getvaloresguias($id)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'valoresGuia'));
        $select->join(array('g' => 'guiasevaluacion'), 'g.idGuia =a.idGuia');
        $select->where('a.idValorGuia = ?', $id);
        return $this->fetchAll($select)->toArray();

    }

    public function cambiarguianota($id, $valor, $formativa, $porcentajeGuia = null, $porcentajeGuiaFinal = null)
    {
        $db = new Zend_Db_Table('valoresGuia');
        $data = array('valorGuias' => $valor, 'valorFormativa' => $formativa, 'porcentajeGuia' => $porcentajeGuia, 'porcentajeGuiaFinal' => $porcentajeGuiaFinal);
        $db->update($data, 'idValorGuia = ' . (int)$id);

    }

    public function cambiarnotaalumno($id, $nota, $porcentaje, $idalumno)
    {
        $data = array('nota' => $nota, 'porcentajenota' => $porcentaje);
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idPruebas= ?', $id);
        $where[] = $this->getAdapter()->quoteInto('idAlumnos = ?', $idalumno);
        $this->update($data, $where);

    }

    public function getnotacursoguiasegsem($idasigantura, $curso, $periodo, $idalumno)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'));
        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos', array('idAlumnosActual'));

        $select->join(array('alu' => 'alumnos'), 'al.idAlumnos = alu.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));
        $select->join(array('ev' => 'evaluaciones'), 'ev.idEvaluacion = a.idPruebas', array('tiempo'));

        $select->where('al.idCursosActual = ?', $curso);
        $select->where('ev.tiempo = 2');
        $select->where('al.idPeriodoActual = ?', $periodo);
        $select->where('a.idAlumnos=?', $idalumno);
        $select->where('a.idAsignatura=?', $idasigantura);

        $select->order("al.ordenAlumno");


        return $this->fetchAll($select)->toArray();

    }

    public function getnotacursoguia($idevaluacion, $curso, $periodo, $tiempo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas', 'idNotas', 'idPruebas', 'idAlumnos'));
        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos', array('idAlumnosActual'));
        //$select->join(array('alu' => 'alumnos'), 'al.idAlumnos = alu.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));

        $select->join(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas');
        //$select->join(array('po'=>'ponderacionnotas'),'po.idNotasPonderacion=a.idNotas');
        //$select->where('a.idCursos = ?', $curso);
        $select->where('al.idCursosActual = ?', $curso);
        $select->where('al.idEstadoActual = 1');
        $select->where('pr.tiempo = ?', $tiempo);
        $select->where('al.idPeriodoActual = ?', $periodo);
        $select->where('a.idPruebas=?', $idevaluacion);
        //$select->order("a.fechaNota");

        $select->group('al.idAlumnos');
        $select->order("al.ordenAlumno");
        $select->order("a.idPruebas DESC");


        return $this->fetchAll($select)->toArray();

    }

    public function borrarvaloresguia($id)
    {
        $db = new Zend_Db_Table('valoresGuia');
        $db->delete('idGuia =' . (int)$id);


    }


    public function agregarnotaguialag($idalumno, $asignatura, $curso, $usuario, $fecha, $periodo, $nota_1, $nota_2, $nota_3, $idguia, $idnota)
    {
        $db = new Zend_Db_Table('notasGuia');
        $data = array('idAlumnos' => $idalumno, 'idAsignatura' => $asignatura, 'idCursos' => $curso, 'idCuenta' => $usuario, 'fechaNotaGuia' => $fecha, 'idPeriodo' => $periodo, 'nota_1' => $nota_1, 'nota_2' => $nota_2, 'nota_3' => $nota_3, 'idGuia' => $idguia, 'idNota' => $idnota);
        $db->insert($data);

    }

    public function cambiarnotaguialag($nota_1, $nota_2, $nota_3, $idnota)
    {
        $db = new Zend_Db_Table('notasGuia');
        $data = array('nota_1' => $nota_1, 'nota_2' => $nota_2, 'nota_3' => $nota_3);
        $db->update($data, 'idNotasGuia = ' . (int)$idnota);

    }

    public function getnotasasignaturalag($idevaluacion, $curso, $periodo)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas', array('nota')));
        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos', array('idAlumnosActual'));
        $select->join(array('alu' => 'alumnos'), 'al.idAlumnos = alu.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));

        $select->join(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas', array('tiempo'));
        $select->joinleft(array('po' => 'notasGuia'), 'po.idNota=a.idNotas');
        $select->where('a.idCursos = ?', $curso);
        $select->where('al.idCursosActual = ?', $curso);
        $select->where('a.idPeriodo = ?', $periodo);
        $select->where('al.idPeriodoActual = ?', $periodo);
        $select->where('a.idPruebas=?', $idevaluacion);
        $select->where('al.idEstadoActual=1');
        $select->where('pr.estadoev != 9');
        $select->where('a.estadoNota != 9');
        //$select->order("a.fechaNota");
        //$select->order("a.idPruebas");
        $select->order("al.ordenAlumno");


        return $this->fetchAll($select)->toArray();

    }

    public function validarnotaguia($idalumno, $idasignatura, $idcurso, $idperiodo,$idnota)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notasGuia'), '*');
        $select->where('a.idAlumnos= ?', $idalumno);
        //$select->where('a.idPruebas = ?', $idprueba);
        $select->where('a.idAsignatura = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('a.idPeriodo = ?', $idperiodo);
        //$select->where('a.idNotas = ?', $idnota);

        $result = $this->fetchAll($select)->toArray();

        if ($result) {
            return false;
        } else {
            return true;
        }

    }

    public function borrarnotasguia($id)
    {
        $db = new Zend_Db_Table('notasGuia');
        $db->delete('idGuia =' . (int)$id);


    }

    public function getnotaspresegmentomonitorlag($id, $idperiodo, $segmento, $idasignatura, $idcurso)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'notas'), '*');
        $select->join(array('pr' => 'evaluaciones'), 'pr.idEvaluacion =a.idPruebas');

       // $select->join(array('asc' => 'asignaturascursos'), 'a.idAsignatura = asc.idAsignatura');
        //$select->join(array('as' => 'asignaturas'), 'as.idAsignatura = asc.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        //$select->joinLeft(array('n' => 'nucleos'), 'n.idNucleo = a.idAsignatura');

        //$select->joinLeft(array('am' => 'ambitos'), 'am.idAmbito = n.idAmbito');


        $select->where('a.idAlumnos  = ?', $id);
        $select->where('a.idPeriodo  = ?', $idperiodo);
        $select->where('a.idAsignatura  = ?', $idasignatura);
        $select->where('a.idCursos = ?', $idcurso);
        $select->where('pr.tiempo=?', $segmento);
        $select->order('a.idAsignatura');
        $select->order('a.idNotas DESC');
        $select->group('a.idNotas');

        $stmt = $select->query();
        $result = $stmt->fetchAll();
        return $result;

    }


}
