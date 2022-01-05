<?php

class Application_Model_DbTable_Alumnosactual extends Zend_Db_Table_Abstract
{

    protected $_name = 'AlumnosActual';
    protected $_primary = 'idAlumnosActual';


    public function get($id)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->join(array('al' => 'alumnos'), 'al.idAlumnos = a.idAlumnos');
        $select->where('a.idAlumnosActual  = ' . $Id);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getactual($id, $idperiodo)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->join(array('al' => 'alumnos'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('e' => 'cursosactual'), 'a.idCursosActual = e.idCursos');

        $select->where('a.idAlumnos = ' . $Id);
        $select->where('a.idPeriodoActual  =? ', $idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getactualdatos($id, $idperiodo)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'AlumnosActual'), array('calle', 'numeroCasa', 'villa', 'ciudadActual'));
        $select->where('a.idAlumnos = ' . $Id);
        $select->where('a.idPeriodoActual  =? ', $idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getactualperiodo($id, $idperiodo)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->join(array('al' => 'alumnos'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('e' => 'cursosactual'), 'a.idCursosActual = e.idCursos');
        $select->where('a.idAlumnosActual = ' . $Id);
        $select->where('a.idPeriodoActual  =? ', $idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getalumnoestado($id, $idperiodo, $idestado)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->join(array('al' => 'alumnos'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('e' => 'cursosactual'), 'a.idCursosActual = e.idCursos');
        $select->where('a.idAlumnos = ' . $Id);
        $select->where('a.idPeriodoActual  =? ', $idperiodo);
        $select->where('a.idEstadoActual  =? ', $idestado);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getcomunaalumno($id)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->where('a.idAlumnos  = ' . $Id);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }


    public function agregar($idalumno, $idcurso, $idperiodo, $orden, $fechainscripcion, $numeromatricula, $calle, $numero, $villa, $ciudad, $telefono, $celular, $comuna, $correo, $idapoderado, $idsuplente, $repitencia, $tipoidentificacion)
    {
        $data = array('idAlumnos' => $idalumno, 'idCursosActual' => $idcurso, 'idPeriodoActual' => $idperiodo, 'idEstadoActual' => '1', 'ordenAlumno' => $orden, 'fechaInscripcion' => $fechainscripcion, 'numeroMatricula' => $numeromatricula,
            'calle' => $calle, 'numeroCasa' => $numero, 'villa' => $villa, 'ciudadActual' => $ciudad, 'telefono' => $telefono, 'celular' => $celular, 'comunaActual' => $comuna, 'correo' => $correo, 'idApoderado' => $idapoderado, 'idApoderadoSuplente' => $idsuplente, 'repitencia' => $repitencia, 'tipoIdentificacion' => $tipoidentificacion);
        $this->insert($data);


    }


    public function cambiar($id, $numeromatricula, $calle, $numero, $villa, $ciudad, $telefono, $celular, $comuna, $correo, $repitencia)
    {
        $data = array('numeroMatricula' => $numeromatricula, 'calle' => $calle, 'numeroCasa' => $numero, 'villa' => $villa, 'ciudadActual' => $ciudad, 'telefono' => $telefono, 'celular' => $celular, 'comunaActual' => $comuna, 'correo' => $correo, 'retitencia' => $repitencia);
        $this->update($data, 'idAlumnosActual = ' . (int)$id);
    }

    public function actualizarcurso($idalumno, $idperiodo, $idcurso, $fechainscripcion, $numeromatricula, $calle, $numero, $villa, $ciudad, $telefono, $celular, $comuna, $correo, $idapoderado, $idsuplente, $repitencia, $tipoidentificacion)
    {
        $data = array('idCursosActual' => $idcurso, 'fechaInscripcion' => $fechainscripcion, 'numeroMatricula' => $numeromatricula, 'calle' => $calle, 'numeroCasa' => $numero, 'villa' => $villa, 'ciudadActual' => $ciudad, 'telefono' => $telefono, 'celular' => $celular, 'comunaActual' => $comuna, 'correo' => $correo, 'idApoderado' => $idapoderado, 'idApoderadoSuplente' => $idsuplente, 'repitencia' => $repitencia, 'tipoIdentificacion' => $tipoidentificacion);
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idAlumnos= ?', $idalumno);
        $where[] = $this->getAdapter()->quoteInto('idPeriodoActual= ?', $idperiodo);
        $this->update($data, $where);
    }


    public function actualizardireccion($idalumno, $idperiodo, $idcurso, $calle, $numero, $villa, $ciudad)
    {
        $data = array('calle' => $calle, 'numeroCasa' => $numero, 'villa' => $villa, 'ciudadActual' => $ciudad);
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idAlumnos= ?', $idalumno);
        $where[] = $this->getAdapter()->quoteInto('idPeriodoActual= ?', $idperiodo);
        $where[] = $this->getAdapter()->quoteInto('idCursosActual= ?', $idcurso);
        $this->update($data, $where);
    }


    public function cambiarestado($id, $estado)
    {
        $data = array('idEstadoActual' => $estado);
        $this->update($data, 'idAlumnosActual = ' . (int)$id);
    }

    public function cambiarcurso($id, $curso)
    {
        $data = array('idCursosActual' => $curso);
        $this->update($data, 'idAlumnosActual = ' . (int)$id);
    }

    public function borrar($id)
    {

        $this->delete('idAlumnos =' . (int)$id);
    }

    public function remove($first_id, $second_id)
    {
        $where = array();
        $where[] = $this->getAdapter()->quoteInto('idAlumnos= ?', $first_id);
        $where[] = $this->getAdapter()->quoteInto('idPeriodoActual= ?', $second_id);
        $this->delete($where);
    }

    public function listar($periodo)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento', array('monitoreo','tipoModalidad'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('esdet.idPeriodo=?' , $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("es.idEstablecimiento");
        $select->order("g.idCodigoGrado");
        $select->order("p.ordenAlumno");

        return $this->fetchAll($select);


    }

    public function validar($idalumno, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->where('a.idAlumnos = ?', $idalumno);
        $select->where('a.idPeriodoActual  = ?', $idperiodo);
        $stmt = $select->query();
        $row = $stmt->fetchAll();
        if (!$row) {
            return TRUE;
        } else {

            return FALSE;
        }


    }

    public function getAsKeyValueJSON($marca)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursos', '' => ''), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento', '' => ''), 'e.idEstablecimiento = es.idEstablecimiento');
        $select->where('p.idAlumnos = ' . $marca);
        $select->order("e.idCursos");

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getcurso($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cursos'), '*');
        $select->where('c.idCursos = ?', $marca);
        $select->order("c.nombreCurso");
        $rowset = $this->fetchAll($select);
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idCursos] = $row->nombreCurso;
        }
        return $data;
    }

    public function listarestablecimiento($id, $periodo)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento', array('monitoreo','tipoModalidad'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('es.idEstablecimiento=' . $Id);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('esdet.idPeriodo=?' , $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("es.idEstablecimiento");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);


    }

    public function listaralumnoscurso($curso, $periodo)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento', array('monitoreo','tipoModalidad'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idCursosActual=' . $curso);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('esdet.idPeriodo=?' , $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("p.ordenAlumno");


        return $this->fetchAll($select);


    }

    public function listaralumnoscursoactual($curso, $periodo)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));

        $select->where('p.idCursosActual=' . $curso);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("p.ordenAlumno");

        $row = $this->fetchAll($select);
        return $row->toArray();


    }

    public function getalumnoscorreoapoderado($curso)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'alumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'idApoderados')))
            ->join(array('e' => 'cursos'), 'p.idCursos = e.idCursos')
            ->join(array('apo' => 'apoderados'), 'p.idApoderado = apo.idApoderado', array('correoApoderado'))
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento');

        $select->where('p.idAlumnos=' . $curso);


        return $this->fetchAll($select);


    }

    public function getcertificado($id)
    {
        $Id = (int)$id;
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'alumnos', array('idCursos', 'idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno', 'numeromatricula')));

        $select->join(array('cur' => 'cursos'),
            'cur.idCursos = a.idCursos ');

        $select->join(array('alm' => 'alumnosClinico'),
            'alm.idalumnos = a.idalumnos ');


        $select->join(array('adn' => 'alumnosNucleo'),
            'adn.idalumnos  = a.idalumnos ');
        $select->join(array('apod' => 'apoderados'),
            'apod.idApoderado = a.idApoderado');
        /*$select->join(array('com' => 'comuna'),
                            'com.COMUNA_ID = a.comuna');*/

        $select->where('a.idAlumnos  = ' . $Id);
        $select->join(array('es' => 'establecimiento', '' => ''), 'cur.idEstablecimiento = es.idEstablecimiento');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;


    }

    public function getcertificadoperso($id, $idperiodo)
    {
        $Id = (int)$id;
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'alumnos', array('idCursos', 'idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno', 'numeromatricula')));

        $select->join(array('cur' => 'cursos'),
            'cur.idCursos = a.idCursos ');

        $select->join(array('per' => 'personalidad'),
            'per.idAlumnos = a.idAlumnos ');

        $select->join(array('alm' => 'alumnosClinico'),
            'alm.idalumnos = a.idalumnos ');


        $select->join(array('adn' => 'alumnosNucleo'),
            'adn.idalumnos  = a.idalumnos ');
        $select->join(array('apod' => 'apoderados'),
            'apod.idApoderado = a.idApoderado');


        $select->where('per.idAlumnos  = ' . $Id);
        $select->where('per.idPeriodo  = ' . $idperiodo);
        $select->join(array('es' => 'establecimiento', '' => ''), 'cur.idEstablecimiento = es.idEstablecimiento');

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;


    }

    public function cambiarorden($idalumnoactual, $orden)
    {
        $data = array('ordenAlumno' => $orden);
        $this->update($data, 'idAlumnosActual = ' . (int)$idalumnoactual);
    }

    public function listaralumnoscursoactualnot($curso, $periodo, $listalumnos)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));

        $select->where('p.idCursosActual=' . $curso);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idAlumnos NOT IN(?)', $listalumnos);
        $select->where('p.idEstadoActual=1');
        $select->order("p.ordenAlumno");

        $row = $this->fetchAll($select);
        return $row->toArray();


    }


    public function getalumnostaller($idconfig,$tipo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnos', array('idCursos', 'idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno', 'numeromatricula')));
        if($tipo==1){
            $select->join(array('det' => 'configuraciontallerdetalle'), 'det.idAlumnos = a.idAlumnos ');
            $select->where('det.idConfiguracionTaller = ?', $idconfig);
        }elseif ($tipo==2){
            $select->join(array('det' => 'configuracionelectivoalumnos'), 'det.idAlumnos = a.idAlumnos ');
            $select->where('det.idConfiguracionElectivo = ?', $idconfig);
        }

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getactualalumno($id, $idperiodo, $idcurso)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->where('a.idAlumnos = ' . $Id);
        $select->where('a.idPeriodoActual  =? ', $idperiodo);
        $select->where('a.idCursosActual  =? ', $idcurso);

        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return $row;
        } else {
            return false;
        }


    }

    public function totalalumnos($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'AlumnosActual'), array("total" => "COUNT(*)"));
        $select->where('a.idPeriodoActual  =? ', $idperiodo);
        $select->where('a.idEstadoActual=1');
        $row = $this->fetchAll($select)->toArray();
        return $row;
    }

    public function totalalumnosest($idperiodo, $idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'AlumnosActual'), array("total" => "COUNT(*)"));
        $select->join(array('cur' => 'cursosactual', array('')),
            'cur.idCursos = a.idCursosActual ');
        $select->where('a.idPeriodoActual  =? ', $idperiodo);
        $select->where('cur.idEstablecimiento  =? ', $idestablecimiento);
        $select->where('a.idEstadoActual=1');
        $row = $this->fetchAll($select)->toArray();
        return $row;
    }

    public function ultimo($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'AlumnosActual'), array('max' => new Zend_Db_Expr('MAX(ordenAlumno)')));
        $select->where('a.idCursosActual=' . $idcurso);
        $select->where('a.idPeriodoActual=' . $idperiodo);

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function getnumeromatricula($idestablecimiento, $tipo, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'AlumnosActual'), array('max' => new Zend_Db_Expr('MAX(numeroMatricula)')));
        $select->join(array('cu' => 'cursosactual'), 'cu.idCursos=a.idCursosActual',array());
        $select->where('cu.idEstablecimiento=?' , $idestablecimiento);
        $select->where('cu.idCodigoTipo=?' , $tipo);
        $select->where('a.idPeriodoActual=?' , $idperiodo);
        return $this->fetchAll($select)->toArray();


    }


    public function retiraralumno($id, $fecharetiro, $motivo)
    {
        $data = array('fechaRetiro' => $fecharetiro, 'motivo' => $motivo, 'idEstadoActual' => '4');
        $this->update($data, 'idAlumnosActual = ' . (int)$id);
    }

    public function reincorporaralumno($id, $fecha, $motivo)
    {
        $data = array('fechaReincorporacion' => $fecha, 'motivo' => $motivo, 'idEstadoActual' => '1');
        $this->update($data, 'idAlumnosActual = ' . (int)$id);
    }

    public function cambiarestadoapp($id, $estado)
    {
        $data = array('idEstadoApp' => $estado);

        //$this->update cambia datos de Alumno con Rut= $rut
        $this->update($data, 'idAlumnosActual = ' . (int)$id);
    }


    public function listartotalalumnos($periodo, $grados)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'))
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('est' => 'establecimiento'), 'est.idEstablecimiento= e.idEstablecimiento ')
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'est.idEstablecimiento= esdet.idEstablecimiento', array('activarapp'));


        $select->where('e.idCodigoTipo NOT IN(?)', $grados);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=1');
        $select->where('esdet.activarapp=1');
        $select->order("p.ordenAlumno");

        $row = $this->fetchAll($select);
        return $row->toArray();


    }

    public function listaralumnoscursoselect($curso, $periodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'), array('idAlumnos', 'idAlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('rutAlumno', 'apaterno', 'amaterno', 'nombres'));
        $select->where('p.idCursosActual=' . $curso);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("p.ordenAlumno");

        return $this->fetchAll($select);

    }


    public function listaralumnoscursoactualIN($curso, $periodo, $listalumnos)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos', array('idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno'));
        $select->where('p.idCursosActual=' . $curso);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idAlumnosActual IN(?)', $listalumnos);
        $select->where('p.idEstadoActual=1');
        $select->order("p.ordenAlumno");

        $row = $this->fetchAll($select);
        return $row->toArray();


    }


}
