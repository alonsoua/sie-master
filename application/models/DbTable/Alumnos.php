<?php

class Application_Model_DbTable_Alumnos extends Zend_Db_Table_Abstract
{

    protected $_name = 'alumnos';
    protected $_primary = 'idAlumnos';


    public function get($id)
    {
        $Id = (int)$id;
        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnos'), array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno', 'sexo', 'fechanacimiento', 'nacionalidad', 'etnia'));
        $select->joinLeft(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos');
        $select->joinLeft(array('cur' => 'cursosactual'),
            'cur.idCursos = al.idCursosActual');
        $select->joinLeft(array('es' => 'establecimiento'), 'cur.idEstablecimiento = es.idEstablecimiento', array('rbd', 'nombreEstablecimiento', 'extension', 'tipoEvaluacion', 'idDirector'));
        $select->joinLeft(array('alc' => 'alumnosClinico'), 'alc.idAlumnos = a.idAlumnos');
        $select->joinLeft(array('aln' => 'alumnosNucleo'), 'aln.idAlumnos = a.idAlumnos');
        $select->joinLeft(array('ap' => 'apoderados'), 'ap.idApoderado = al.idApoderado');
        $select->where('a.idAlumnos  = ' . $Id);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getidperiodo($id, $idperiodo)
    {
        $Id = (int)$id;
        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnos'), array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno', 'fechanacimiento', 'etnia', 'paisNacimiento', 'ciudadNacimiento'));
        $select->joinLeft(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos');
        $select->joinLeft(array('ales' => 'alumnosEscolar'), 'al.idAlumnosActual = ales.idAlumnosActual');
        $select->joinLeft(array('cur' => 'cursosactual'),
            'cur.idCursos = al.idCursosActual');
        $select->joinLeft(array('es' => 'establecimiento'), 'cur.idEstablecimiento = es.idEstablecimiento', array('rbd', 'nombreEstablecimiento', 'extension', 'tipoEvaluacion', 'idDirector'));
        $select->joinLeft(array('alc' => 'alumnosClinico'), 'alc.idAlumnos = a.idAlumnos');
        $select->joinLeft(array('aln' => 'alumnosNucleo'), 'aln.idAlumnos = a.idAlumnos');
        $select->joinLeft(array('ap' => 'apoderados'), 'ap.idApoderado = al.idApoderado');
        $select->where('a.idAlumnos  = ' . $Id);
        $select->where('al.idPeriodoActual  = ?', $idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;


    }

    public function getcomunaalumno($id)
    {
        $Id = (int)$id;
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'alumnos'), '*');


        $select->where('a.idAlumnos  = ' . $Id);

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }


    public function agregar($rut, $nombres, $apaterno, $amaterno, $sexo, $fechanacimiento, $nacionalidad, $etnia, $pais, $ciudad)
    {
        $data = array('rutAlumno' => $rut, 'nombres' => $nombres, 'apaterno' => $apaterno, 'amaterno' => $amaterno, 'sexo' => $sexo, 'fechanacimiento' => $fechanacimiento, 'procedencia' => 0, 'nacionalidad' => $nacionalidad, 'etnia' => $etnia, 'paisNacimiento' => $pais, 'ciudadNacimiento' => $ciudad);
        //$this->insert inserta nuevo Personal
        $this->insert($data);

    }


    public function cambiar($id, $rut, $nombres, $apaterno, $amaterno, $sexo, $fechanacimiento, $nacionalidad, $etnia, $pais, $ciudad)
    {
        $data = array('rutAlumno' => $rut, 'nombres' => $nombres, 'apaterno' => $apaterno, 'amaterno' => $amaterno, 'sexo' => $sexo, 'fechanacimiento' => $fechanacimiento, 'procedencia' => 0, 'nacionalidad' => $nacionalidad, 'etnia' => $etnia, 'paisNacimiento' => $pais, 'ciudadNacimiento' => $ciudad);
        $this->update($data, 'idAlumnos = ' . (int)$id);
    }


    public function borrar($id)
    {

        $this->delete('idAlumnos =' . (int)$id);
    }

    public function listar($periodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array('tipoModalidad'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');


        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('esc.idPeriodo=' . $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);

    }

    public function listarretirado($periodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=4');
        $select->order("es.idEstablecimiento");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);

    }

    public function validar($rut)

    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->joinLeft(array('ap' => 'apoderados'), 'p.idApoderado= ap.idApoderado');
        $select->where('al.rutAlumno =? ', $rut);
        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return $row;
        } else {
            return false;
        }

    }

    public function validaralumno($rut)


    {
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'alumnos'));
        $select->where('p.rutAlumno =? ', $rut);
        $row = $this->fetchAll($select)->toArray();

        if ($row) {
            return $row;
        } else {
            return false;
        }

    }

    public function getAsKeyValueJSON($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'alumnos'))
            ->join(array('e' => 'cursos', '' => ''), 'p.idCursos = e.idCursos')
            ->join(array('es' => 'establecimiento', '' => ''), 'e.idEstablecimiento = es.idEstablecimiento');
        $select->where('p.idAlumnos = ' . $marca);
        $select->order("p.idCursos");

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

    public function listarusuario($periodo, $curso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array('tipoModalidad'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('esc.idPeriodo=' . $periodo);
        $select->where('p.idCursosActual=' . $curso);
        $select->where('p.idEstadoActual=1');
        $select->order("e.idCodigoGrado");
        $select->order("e.letra");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);

    }

    public function listarestablecimiento($idestablecimiento, $periodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array('tipoModalidad'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');

        $select->where('es.idEstablecimiento=' . $idestablecimiento);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('esc.idPeriodo=' . $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);

    }

    public function listardocente($idusuario, $idestablecimiento, $periodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');

        $select->where('es.idEstablecimiento=' . $idestablecimiento);
        $select->where('e.idCuentaJefe=' . $idusuario);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=1');
        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);

    }

    public function listardocenteretirado($idusuario, $idestablecimiento, $periodo)
    {
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');

        $select->where('es.idEstablecimiento=' . $idestablecimiento);
        $select->where('e.idCuentaJefe=' . $idusuario);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=4');
        $select->order("es.idEstablecimiento");
        $select->order("ce.idCodigoTipo");
        $select->order("g.idCodigoGrado");
        $select->order("e.idCursos");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);

    }

    public function listarestablecimientoretirado($id, $periodo)
    {
        $Id = (int)$id;
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos')
            ->join(array('e' => 'cursosactual'), 'p.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('es.idEstablecimiento=' . $Id);
        $select->where('p.idPeriodoActual=' . $periodo);
        $select->where('p.idEstadoActual=4');
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);

    }

    public function listaralumnoscurso($curso)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'AlumnosActual'))
            ->join(array('e' => 'cursos'), 'p.idCursos = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento');

        $select->where('p.idCursos=' . $curso);
        $select->order("p.apaterno");
        $select->order("p.amaterno");
        $select->order("p.nombres");

        return $this->fetchAll($select);

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
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->join(array('al' => 'alumnos'), 'al.idAlumnos = a.idAlumnos');
        $select->joinLeft(array('cur' => 'cursosactual'), 'cur.idCursos = a.idCursosActual');
        $select->joinLeft(array('es' => 'establecimiento', '' => ''), 'cur.idEstablecimiento = es.idEstablecimiento');
        $select->joinLeft(array('alm' => 'alumnosClinico'), 'alm.idalumnos = a.idAlumnos ');
        $select->joinLeft(array('adn' => 'alumnosNucleo'), 'adn.idalumnos  = a.idAlumnos ');
        $select->joinLeft(array('apod' => 'apoderados'), 'apod.idApoderado = a.idApoderado');
        $select->where('a.idAlumnosActual  = ' . $Id);

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function getcertificadoalumno($id)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'AlumnosActual'), '*');
        $select->join(array('al' => 'alumnos'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('cur' => 'cursosactual'), 'cur.idCursos = a.idCursosActual');
        $select->join(array('es' => 'establecimiento', '' => ''), 'cur.idEstablecimiento = es.idEstablecimiento');


        $select->where('a.idAlumnosActual  = ' . $Id);

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    public function getcertificadoperso($id, $p, $idperiodo)
    {
        $Id = (int)$id;
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnos', array('idCursos', 'idAlumnos', 'nombres', 'apaterno', 'amaterno', 'rutAlumno', 'numeromatricula')));
        $select->join(array('al' => 'AlumnosActual'), 'al.idAlumnos = a.idAlumnos');
        $select->join(array('e' => 'cursosactual'), 'al.idCursosActual = e.idCursos');
        $select->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->join(array('per' => 'personalidad'),
            'per.idAlumnos = a.idAlumnos ');
        $select->where('per.idAlumnos  = ' . $Id);
        $select->where('per.segmento  = ?', $p);
        $select->where('per.idPeriodo  = ?', $idperiodo);
        $select->where('al.idPeriodoActual  = ?', $idperiodo);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;

    }

    public function getalumno($dato, $tipo, $materno)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'alumnos'), array('idAlumnos', 'rutAlumno', 'nombres', 'apaterno', 'amaterno', 'direccion', 'telefono', 'celular', 'comuna', 'sexo', 'correo', 'fechanacimiento', 'idApoderado', 'idApoderados', 'prioritario', 'beneficio', 'numeromatricula', 'foto', 'procedencia', 'saludescolar', 'especialidad', 'pie', 'pae'));
        if ($tipo == 1) {
            $select->where("a.apaterno LIKE ?", "%{$dato}%");
        } elseif ($tipo == 2) {
            $select->where("a.rutAlumno LIKE ?", "%{$dato}%");
        } elseif ($tipo == 3) {
            $select->where("a.apaterno LIKE ?", "%{$dato}%");
            $select->where("a.amaterno LIKE ?", "%{$materno}%");
        }

        $stmt = $select->query();

        $result = $stmt->fetchAll();
        return $result;


    }

}
