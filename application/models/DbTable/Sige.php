<?php

/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 19-07-17
 * Time: 3:30 PM
 */
class Application_Model_DbTable_Sige extends Zend_Db_Table_Abstract
{
    protected $_name = 'establecimientoSige';
    protected $_primary = 'idSige';


    public function get($id)
    {
        $row = $this->fetchAll('idSige = ' . (int)$id);
        if (!$row) {
            return false;
        } else {
            return $row->toArray();
        }

    }

    public function get2($idest, $per)
    {
        $row = $this->fetchAll('idEstablecimiento = ' . (int)$idest . ' AND idPeriodo = ' . (int)$per);
        if (!$row) {
            return false;
        } else {
            return $row->toArray();
        }

    }


    public function agregar($cliente, $convenio, $token, $idperiodo, $idestablecimiento)
    {
        $data = array('idSige' => 'Null', 'ClienteId' => $cliente, 'ConvenioId' => $convenio, 'ConvenioToken' => $token, 'idPeriodo' => $idperiodo, 'idEstablecimiento' => $idestablecimiento);

        $this->insert($data);

    }

    public function cambiar($id, $cliente, $convenio, $token, $semilla)
    {
        $data = array('ClienteId' => $cliente, 'ConvenioId' => $convenio, 'ConvenioToken' => $token, 'ValorSemilla' => $semilla);

        $this->update($data, 'idSige= ' . (int)$id);

    }

    public function actualizarsemilla($id, $semilla, $fecha)
    {
        $data = array('ValorSemilla' => $semilla, 'fechasemilla' => $fecha);

        $this->update($data, 'idSige= ' . (int)$id);

    }

    public function listar($idperiodo)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('s' => 'establecimientoSige'))
            ->join(array('es' => 'establecimiento'), 's.idEstablecimiento = es.idEstablecimiento')
            ->where('s.idPeriodo=' . (int)$idperiodo);
        $select->order("es.nombreEstablecimiento");


        return $this->fetchAll($select);

    }

    public function agregartipo($idperiodo, $idestablecimiento, $idcodigo, $estado, $autorizacion, $fecha, $centro, $juridica, $numero, $iniciom, $terminom, $iniciot, $terminot, $iniciomt, $terminomt, $iniciov, $terminov)
    {
        $db = new Zend_Db_Table('tipoensenanza');
        $data = array('idPeriodo' => $idperiodo, 'idEstablecimiento' => $idestablecimiento, 'idCodigoTipo' => $idcodigo, 'estadoTipo' => $estado, 'autorizacion' => $autorizacion, 'fechaAutorizacion' => $fecha, 'centro' => $centro, 'juridica' => $juridica, 'numeroGrupo' => $numero, 'inicioManana' => $iniciom, 'terminoManana' => $terminom, 'inicioTarde' => $iniciot, 'terminoTarde' => $terminot, 'inicioMananaTarde' => $iniciomt, 'terminoMananaTarde' => $terminomt, 'inicioVespertino' => $iniciov, 'terminoVespertino' => $terminov);
        $db->insert($data);
    }

    public function listartipo($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('t' => 'tipoensenanza'))
            ->join(array('es' => 'establecimiento'), 't.idEstablecimiento = es.idEstablecimiento')
            ->join(array('p' => 'periodo'), 't.idPeriodo = p.idPeriodo')
            ->where('t.idPeriodo=' . (int)$idperiodo);
        return $this->fetchAll($select);
    }

    public function getespecialidad($idcodigo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('t' => 'codigoespecialidad'))
            ->where('t.idCodigoSector=' . (int)$idcodigo);
        return $this->fetchAll($select);
    }


    public function agregarhistorialcurso($semilla, $respuesta, $fecha, $idcurso)
    {
        $db = new Zend_Db_Table('historialcurso');
        $data = array('semilla' => $semilla, 'respuesta' => $respuesta, 'fechahistorial' => $fecha, 'idCursos' => $idcurso);
        $db->insert($data);
    }

    public function listarhistorialcurso($idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('h' => 'historialcurso'))
            ->where('h.idCursos=' . (int)$idcurso);
        return $this->fetchAll($select);
    }

    public function agregarhistorialtipo($semilla, $respuesta, $fecha, $idcurso)
    {
        $db = new Zend_Db_Table('historialtipo');
        $data = array('semilla' => $semilla, 'respuesta' => $respuesta, 'fechahistorial' => $fecha, 'idTipo' => $idcurso);
        $db->insert($data);
    }

    public function listarhistorialtipo($idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('h' => 'historialtipo'))
            ->where('h.idTipo=' . (int)$idcurso);
        return $this->fetchAll($select);
    }

    public function agregarhistorialalumno($semilla, $respuesta, $fecha, $idcurso)
    {
        $db = new Zend_Db_Table('historialalumno');
        $data = array('semilla' => $semilla, 'respuesta' => $respuesta, 'fechahistorial' => $fecha, 'idAlumno' => $idcurso);
        $db->insert($data);
    }

    public function listarhistorialalumno($idalumno)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('h' => 'historialalumno'))
            ->where('h.idAlumno=' . (int)$idalumno);
        return $this->fetchAll($select);
    }

    public function guardarcodigo($idasistencia, $fecha, $codigo, $cuenta)
    {
        $db = new Zend_Db_Table('codigoasistencia');
        $data = array('idAsistenciac' => $idasistencia, 'fechaRespuesta' => $fecha, 'codigoasistencia' => $codigo, 'idCuenta' => $cuenta);
        $db->insert($data);
    }

    public function actualizarcodigo($idcodigo, $estado)
    {
        $db = new Zend_Db_Table('codigoasistencia');
        $data = array('estadoasistencia' => $estado);
        $db->update($data, 'idCodigoAsistencia = ' . (int)$idcodigo);
    }

    public function agregarhistorialasistencia($semilla, $respuesta, $fecha, $idasistencia)
    {
        $db = new Zend_Db_Table('historialasistencia');
        $data = array('semilla' => $semilla, 'respuesta' => $respuesta, 'fechahistorial' => $fecha, 'idAsistencia' => $idasistencia);
        $db->insert($data);
    }

    public function listarhistorialasistencia($idasistencia)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('h' => 'historialasistencia'))
            ->where('h.idAsistencia=' . (int)$idasistencia);
        return $this->fetchAll($select);
    }

    public function agregarhistorialasistenciareporte($semilla, $respuesta, $fecha, $idasistencia)
    {
        $db = new Zend_Db_Table('historialasistenciareporte');
        $data = array('semilla' => $semilla, 'respuesta' => $respuesta, 'fechahistorial' => $fecha, 'idAsistencia' => $idasistencia);
        $db->insert($data);
    }

    public function listarhistorialasistenciareporte($idasistencia)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('h' => 'historialasistenciareporte'))
            ->where('h.idAsistencia=' . (int)$idasistencia);
        return $this->fetchAll($select);
    }


}