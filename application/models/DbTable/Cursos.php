<?php

class Application_Model_DbTable_Cursos extends Zend_Db_Table_Abstract
{

    protected $_name = 'cursos';
    protected $_primary = 'idCursos';


    public function get($idcurso)
    {
        $idcurso = (int)$idcurso;
        $db = new Zend_Db_Table('cursosactual');
        $row = $db->fetchAll('idCursos = ' . $idcurso);
        if (!$row) {
            throw new Exception("Could not find row $idcurso");
        }

        return $row->toArray();
    }


    public function borrar($idcurso)
    {
        //$this->delete borrar album donde RBD=$rbd
        $this->delete('idCursos =' . (int)$idcurso);
    }

    public function listar($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento = es.idEstablecimiento', array('nombreEstablecimiento'))
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento', array('monitoreo','tipoModalidad'))
            ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= p.idCuentaJefe')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idPeriodo =? ', $idperiodo);
        $select->where('esdet.idPeriodo =? ', $idperiodo);
        $select->order("es.nombreEstablecimiento");
        $select->order("p.idCodigoTipo");


        return $this->fetchAll($select);

    }


    public function getValueCurso($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cursosactual'), '*');
        $select->join(array('es' => 'establecimiento'), 'c.idEstablecimiento= es.idEstablecimiento');
        $select->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento');
        $select->join(array('com' => 'comuna'), 'com.idComuna= es.comuna')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'c.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'c.idCodigoGrado= g.idCodigoGrado');
        $select->where('c.idEstablecimiento = ?', $marca);
        $select->order('c.idCodigoTipo');

        $rowset = $this->fetchAll($select);
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idCursos] = $row->nombreGrado . " " . $row->letra;
        }
        return $data;
    }

    public function listarcursoestablecimiento($idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cursosactual'), '*');
        $select->join(array('es' => 'establecimiento'), 'c.idEstablecimiento= es.idEstablecimiento');
        $select->joinLeft(array('ce' => 'codigotipoensenanza'), 'c.idCodigoTipo= ce.idCodigoTipo');
        $select->joinLeft(array('g' => 'codigogrados'), 'c.idCodigoGrado= g.idCodigoGrado');
        $select->where('c.idEstablecimiento = ?', $idestablecimiento);
        $select->where('c.idPeriodo=?',$idperiodo);
        return $this->fetchAll($select);

    }


    public function actualizardocente($iddocente, $idcurso)
    {
        $db = new Zend_Db_Table('cursosactual');
        $data = array('idCuentaJefe' => $iddocente,
        );
        $db->update($data, 'idCursos = ' . (int)$idcurso);
    }

    public function getcursocuenta($id)
    {
        $db = new Zend_Db_Table('cursosactual');
        $row = $db->fetchAll('idCuentaJefe = ' . $id);
        if ($row) {
            return $row->toArray();
        }

    }

    public function getcursocuentaestablecimiento($id, $idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'cursosactual'));
        $select->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento',array('idEstablecimiento'));
        $select->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento',array('monitoreo'));
        $select->where('p.idCuentaJefe = ?', $id);
        $select->where('p.idEstablecimiento= ?', $idestablecimiento);
        $select->where('p.idPeriodo = ?', $idperiodo);
        $select->where('esdet.idPeriodo = ?', $idperiodo);

        $row = $this->fetchAll($select);
        if ($row) {
            return $row->toArray();
        }

    }

    public function getcursocuentaperiodo($id, $idperiodo)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'cursosactual'));
        $select->where('p.idCuentaJefe = ?', $id);
        $select->where('p.idPeriodo = ?', $idperiodo);

        $row = $this->fetchAll($select);
        if ($row) {
            return $row->toArray();
        }

    }

    public function listarcursoid($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('p' => 'cursosactual'));
        $select->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento');
        $select->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento');
        $select->joinLeft(array('com' => 'comuna'), 'com.idComuna= es.comuna')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado');
        //->joinLeft(array('de' => 'decretos'), 'p.idDecreto= g.idDecreto',array('numeroDecreto','yearDecreto'));
        $select->where('p.idCursos = ?', $idcurso);
        $select->where('esdet.idPeriodo = ?', $idperiodo);

        return $this->fetchAll($select);

    }


    public function getcursosnivel()
    {

        $rowset = $this->fetchAll();
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idCursos] = $row->nombreCursos;
        }
        return $data;
    }


    public function agregaractual($idestablecimiento, $idperiodo, $idcodigo, $idnivel, $letra, $idcuentajefe, $combinado, $numero, $jornada, $codigosector, $codigoespecialidad, $codigoalternativa, $infraestructura)
    {
        $db = new Zend_Db_Table('cursosactual');
        $data = array('idPeriodo' => $idperiodo, 'idEstablecimiento' => $idestablecimiento, 'idCodigoTipo' => $idcodigo, 'idCodigoGrado' => $idnivel, 'letra' => $letra, 'idCuentaJefe' => $idcuentajefe, 'combinado' => $combinado, 'numeroCurso' => $numero, 'tipoJornada' => $jornada, 'codigoSector' => $codigosector, 'codigoEspecialidad' => $codigoespecialidad, 'codigoAlternativa' => $codigoalternativa, 'infraestructura' => $infraestructura);
        $db->insert($data);
    }


    public function cambiaractual($idcurso, $combinado, $numero, $jornada, $codigosector, $codigoespecialidad, $codigoalternativa, $infraestructura)
    {
        $db = new Zend_Db_Table('cursosactual');
        $data = array('combinado' => $combinado, 'numeroCurso' => $numero, 'tipoJornada' => $jornada, 'codigoSector' => $codigosector, 'codigoEspecialidad' => $codigoespecialidad, 'codigoAlternativa' => $codigoalternativa, 'infraestructura' => $infraestructura);
        $db->update($data, 'idCursos = ' . (int)$idcurso);
    }

    public function cambiardecreto($idcurso, $decreto)
    {
        $db = new Zend_Db_Table('cursosactual');
        $data = array('idDecreto' => $decreto);
        $db->update($data, 'idCursos = ' . (int)$idcurso);
    }


    public function listaractual($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento = es.idEstablecimiento', array('nombreEstablecimiento'))
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array('tipoModalidad'))
            ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= p.idCuentaJefe')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idPeriodo = ' . $idperiodo);
        $select->where('esc.idPeriodo = ' . $idperiodo);
        $select->order("es.nombreEstablecimiento");
        $select->order("p.idCodigoTipo");
        $select->order("g.idGrado");
        $select->order("p.Letra");


        return $this->fetchAll($select);

    }

    public function listartodasactual($idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento', array('monitoreo','tipoModalidad'))
            ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= p.idCuentaJefe')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado');


        $select->where('p.idPeriodo = ' . $idperiodo);
        $select->where('p.idEstablecimiento = ' . $idestablecimiento);
        $select->where('esdet.idPeriodo =? ', $idperiodo);
        $select->order("es.nombreEstablecimiento");
        $select->order("p.idCodigoTipo");
        $select->order("g.idGrado");
        $select->order("p.Letra");


        return $this->fetchAll($select);

    }

    public function listarcursoiddocenteactual($idusurario, $idestablecimiento, $idperiodo, $opcion)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array('tipoModalidad'))
            ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= p.idCuentaJefe')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado')
            ->joinLeft(array('per' => 'periodo'), 'p.idPeriodo= per.idPeriodo')
            ->where('p.idCuentaJefe  = ?', $idusurario)
            ->where('es.idEstablecimiento=?', $idestablecimiento)
            ->where('p.idPeriodo  = ?', $idperiodo)
            ->where('esc.idPeriodo  = ?', $idperiodo);

        if (!$opcion) {
            return $this->fetchAll($select);
        } else {
            return $this->fetchAll($select)->toArray();
        }


    }

    public function listarcursoiddocenteactuals($idusurario, $idperiodo, $opcion, $idcurso)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= p.idCuentaJefe')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado')
            ->joinLeft(array('per' => 'periodo'), 'p.idPeriodo= per.idPeriodo')
            ->where('p.idCuentaJefe  = ?', $idusurario)
            ->where('p.idCursos  = ?', $idcurso)
            ->where('p.idPeriodo  = ?', $idperiodo);

        if (!$opcion) {
            return $this->fetchAll($select);
        } else {
            return $this->fetchAll($select)->toArray();
        }


    }


    public function getactual($idcurso)
    {
        $db = new Zend_Db_Table('cursosactual');
        $row = $db->fetchAll('idCursos = ' . $idcurso);
        if (!$row) {
            throw new Exception("Could not find row $idcurso");
        }

        return $row->toArray();
    }

    public function getnombreactual($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= p.idCuentaJefe')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado')
            ->joinLeft(array('per' => 'periodo'), 'p.idPeriodo= per.idPeriodo')
            ->where('idCursos  = ?', $marca);

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getcursoactualtotal($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado')
            ->where('idCursos  = ?', $marca);

        return $this->fetchAll($select);

    }

    public function getcursojson($idusuario,$idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'),array('idCursos','letra'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento', array('rbd'))
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array())
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo',array())
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado',array('nombreGrado'));
                if(!empty($idusuario)){
                    $select->where('p.idCuentaJefe = ?', $idusuario);
                }
            $select->where('p.idEstablecimiento  = ?', $idestablecimiento)
            ->where('p.idPeriodo = ?', $idperiodo)
            ->where('esc.idPeriodo = ?', $idperiodo)
            ->order('ce.idCodigoTipo')
            ->order('g.idCodigoGrado')
            ->group('p.idCursos');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }


    public function getcursoalumno($idtipo, $idgrado, $letra, $idperiodo, $idest)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'cursosactual'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idCodigoTipo = ?', $idtipo);
        $select->where('g.idGrado = ?', $idgrado);
        $select->where('p.letra = ?', $letra);
        $select->where('p.idPeriodo = ?', $idperiodo);
        $select->where('p.idEstablecimiento = ?', $idest);
        $row = $this->fetchAll($select);
        if (!$row) {
            return false;
        } else {
            return $row->toArray();
        }

    }

    public function getcursodecreto($idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento', array('idEstablecimiento', 'rbd'))
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array('ingresonota'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado')
            ->where('p.idEstablecimiento  = ?', $idestablecimiento)
            ->where('p.idPeriodo = ?', $idperiodo)
            ->where('p.idDecreto= 0')
            ->order('ce.idCodigoTipo')
            ->order('g.idCodigoGrado')
            ->group('p.idCursos');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getcursodecretoid($iddecreto)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado')
            ->where('p.idDecreto = ?', $iddecreto)
            ->order('ce.idCodigoTipo')
            ->order('g.idCodigoGrado')
            ->group('p.idCursos');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listacursos($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursosactual'))
            ->where('p.idPeriodo = ?', $idperiodo);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }



    //Horario

    public function agregarbloque($bloque, $dia, $idcurso, $idasignatura, $idcuenta, $idperiodo)
    {
        $db = new Zend_Db_Table('horario');
        $data = array('bloque' => $bloque, 'dia' => $dia, 'idCursos' => $idcurso, 'idAsignatura' => $idasignatura, 'idCuenta' => $idcuenta, 'idPeriodo' => $idperiodo);
        $db->insert($data);
    }

    public function actualizarbloque($idhorario, $idasignatura, $idcuenta)
    {
        $db = new Zend_Db_Table('horario');
        $data = array('idAsignatura' => $idasignatura, 'idCuenta' => $idcuenta);
        $db->update($data, 'idHorario = ' . (int)$idhorario);
    }

    public function listarbloque($idcurso)
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

    public function listarbloqueid($idhorario)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'horario'))
            ->joinLeft(array('as' => 'asignaturas'), 'p.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'))
            ->joinLeft(array('asc' => 'asignaturascursos'), 'asc.idAsignatura = as.idAsignatura')
            ->where('p.idHorario= ?', $idhorario);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listarbloquetotaldocentes($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'horario'))
            ->where('p.idPeriodo= ?', $idperiodo);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function validarbloque($bloque, $dia, $idcuenta, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'horario'));
        $select->where('p.idCuenta = ?', $idcuenta);
        $select->where('p.idPeriodo = ?', $idperiodo);
        $select->where('p.bloque = ?', $bloque);
        $select->where('p.dia = ?', $dia);
        $row = $this->fetchAll($select)->toArray();
        if ($row) {
            return false;
        } else {
            return true;
        }

    }


    public function eliminarbloque($idhorario)
    {
        $db = new Zend_Db_Table('horario');
        $db->delete('idHorario =' . (int)$idhorario);
    }

    public function eliminarbloqueid($idbloque)
    {
        $db = new Zend_Db_Table('bloque');
        $db->delete('idBloque =' . (int)$idbloque);
    }


    //Nuevos Cursos Docentes

    public function listarcursodocente($idcuenta, $idperiodo, $idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->joinLeft(array('cu' => 'cursosactual'), 'cu.idCursos= h.idCursos')
            ->join(array('es' => 'establecimiento'), 'cu.idEstablecimiento = es.idEstablecimiento')
            ->join(array('esconf' => 'establecimientoConfiguracion'), 'esconf.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');

        $select->where('h.idCuenta = ?', $idcuenta);
        $select->where('h.idPeriodo = ?', $idperiodo);
        $select->where('esconf.idPeriodo = ?', $idperiodo);
        $select->where('es.idEstablecimiento = ?', $idestablecimiento);
        $select->order("h.idCuenta");
        $select->group('cu.idCursos');
        return $this->fetchAll($select);
    }

    public function listarcursodocentepre($idcuenta, $idperiodo, $idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('cu' => 'cursosactual'), '*')
            ->join(array('es' => 'establecimiento'), 'cu.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');

        $select->where('cu.idCuentaJefe = ?', $idcuenta);
        $select->where('cu.idPeriodo = ?', $idperiodo);
        $select->where('es.idEstablecimiento = ?', $idestablecimiento);
        $select->where('g.idCodigo=10');
        return $this->fetchAll($select);
    }

    public function listarcursodocentepreid($idcuenta, $idperiodo, $idestablecimiento, $idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('cu' => 'cursosactual'), '*')
            ->join(array('es' => 'establecimiento'), 'cu.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');

        $select->where('cu.idCursos = ?', $idcurso);
        $select->where('cu.idCuentaJefe = ?', $idcuenta);
        $select->where('cu.idPeriodo = ?', $idperiodo);
        $select->where('es.idEstablecimiento = ?', $idestablecimiento);
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

    public function gethorariocurso($idperiodo, $idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('h' => 'horario'), '*');
        $select->joinLeft(array('cu' => 'cursosactual'), 'cu.idCursos= h.idCursos')
            ->joinLeft(array('bl' => 'bloque'), 'bl.idBloque= h.bloque');


        $select->where('h.idPeriodo = ?', $idperiodo);
        $select->where('h.idCursos = ?', $idcurso);
        $select->order("h.bloque");
        $select->group('h.bloque');
        $row = $this->fetchAll($select)->toArray();

        return $row;


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
        $select->from(array('h' => 'horario'), '*');
           // ->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
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
        $select->from(array('h' => 'horario'), '*');
            //->joinLeft(array('b' => 'bloque'), 'b.idBloque = h.bloque');
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


    public function listarbloquesestablecimiento($idestablecimiento, $idperiodo, $tipo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'bloque'))
            ->where('p.idEstablecimiento= ?', $idestablecimiento)
            ->where('p.tipoBloque= ?', $tipo)
            ->where('p.idPeriodo= ?', $idperiodo);
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
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

    public function listarcursoscombinados($idperiodo, $idEstablecimiento = null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('ca' => 'cursosactual'))
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');

        if (!is_null($idEstablecimiento)) {
            $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('ca.idPeriodo =' . $idperiodo);
        $select->where('ca.combinado =0');

        return $this->fetchAll($select);
    }

    public function listarCursosJSON($periodo, $idEstablecimiento = null)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ca' => 'cursosactual'))
            ->join(array('cg' => 'codigogrados'), 'ca.idCodigoGrado = cg.idCodigoGrado');

        if (!is_null($idEstablecimiento)) {
            $select->where('ca.idEstablecimiento=' . $idEstablecimiento);
        }
        $select->where('ca.idPeriodo =' . $periodo);

        return $this->fetchAll($select);
    }

    public function listarAsignaturasCursosJSON($idCursos)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('ac' => 'asignaturascursos'))
            ->join(array('as' => 'asignaturas'), 'ac.idAsignatura = as.idAsignatura');

        $select->where('ac.idCursos =' . $idCursos);

        return $this->fetchAll($select);
    }


}
