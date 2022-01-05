<?php

class Application_Model_DbTable_Establecimiento extends Zend_Db_Table_Abstract
{

    protected $_name = 'establecimiento';
    protected $_primary = 'idEstablecimiento';


    public function get($id)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('e' => 'establecimiento'), '*');
        $select->where('e.idEstablecimiento = ' . $id);

        $stmt = $select->query();
        $result = $stmt->fetchAll();

        return $result;
    }

    public function getnamerbd($rbd)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('e' => 'establecimiento'));
        $select->where('e.rbd = ?', $rbd);
        $rows = $this->fetchAll($select);
        if (!$rows) {
            return false;
        } else {
            return $rows->toArray();
        }
    }

    public function agregar($RBD, $Nombre, $dependencia, $comuna, $direccion, $telefono, $correo, $extension, $sostenedor, $tipo, $subvencion, $matricula, $concentracion, $calificacion, $matriculapie)
    {
        $data = array('rbd' => $RBD, 'nombreEstablecimiento' => $Nombre, 'dependencia' => $dependencia, 'comuna' => (int)$comuna, 'direccion' => $direccion, 'telefono' => $telefono, 'correo' => $correo, 'extension' => $extension, 'idSostenedor' => $sostenedor, 'tipoEvaluacion' => $tipo, 'subvencion' => $subvencion, 'matricula' => $matricula, 'concentracion' => $concentracion, 'calificacion' => $calificacion, 'matriculapie' => $matriculapie);
        //$this->insert inserta nuevo Establecimiento
        $this->insert($data);
    }

    public function cambiar($id, $RBD, $Nombre, $dependencia, $comuna, $direccion, $telefono, $correo, $extension, $sostenedor, $tipo, $subvencion, $matricula, $concentracion, $calificacion, $matriculapie)
    {
        if ($extension == '0') {
            $data = array('rbd' => $RBD, 'nombreEstablecimiento' => $Nombre, 'dependencia' => $dependencia, 'comuna' => $comuna, 'direccion' => $direccion, 'telefono' => $telefono, 'correo' => $correo, 'idSostenedor' => $sostenedor, 'tipoEvaluacion' => $tipo, 'subvencion' => $subvencion, 'matricula' => $matricula, 'concentracion' => $concentracion, 'calificacion' => $calificacion, 'matriculapie' => $matriculapie,
            );
        } else {
            $data = array('rbd' => $RBD, 'nombreEstablecimiento' => $Nombre, 'dependencia' => $dependencia, 'comuna' => $comuna, 'direccion' => $direccion, 'telefono' => $telefono, 'correo' => $correo, 'extension' => $extension, 'idSostenedor' => $sostenedor, 'tipoEvaluacion' => $tipo, 'subvencion' => $subvencion, 'matricula' => $matricula, 'concentracion' => $concentracion, 'calificacion' => $calificacion, 'matriculapie' => $matriculapie,
            );
        }

        //$this->update cambia datos de Establecimiento con RBD= $RBD
        $this->update($data, 'idEstablecimiento = ' . (int)$id);
    }

    public function borrar($id)
    {

        $this->delete('idEstablecimiento =' . (int)$id);
    }

    public function listar()
    {
        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('e' => 'establecimiento'))
        ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= e.idDirector',array('nombrescuenta','paternocuenta','maternocuenta'));
        return $this->fetchAll($select);

    }

    public function listarestablecimientorol($id, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('e' => 'establecimiento'))
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'e.idEstablecimiento= esdet.idEstablecimiento', array('numeroDecreto', 'yeardecreto', 'aproxAsignatura', 'aproxPeriodo', 'aproxAnual', 'aproxFinal', 'monitoreo', 'idPeriodo'))
        ->joinLeft(array('cu' => 'cuentasUsuario'), 'cu.idCuenta= e.idDirector',array('nombrescuenta','paternocuenta','maternocuenta'));
        $select->where('e.idEstablecimiento=?', $id);
        $select->where('esdet.idPeriodo=?', $idperiodo);


        return $this->fetchAll($select);
    }

    public function validar($RBD)
    {
        $RBD = (int)$RBD;
        //$this->fetchRow devuelve fila donde RBD = $RBD
        $row = $this->fetchRow('rbd = ' . $RBD);
        if (!$row) {
            return true;
        } else {

            return false;
        }

    }

    public function listarcursoid($marca)
    {
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cursos'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento')
            ->join(array('esdet' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esdet.idEstablecimiento')
            ->join(array('com' => 'comuna'), 'com.idComuna= es.comuna')
            ->where('idCursos = ?', $marca);

        return $this->fetchAll($select);

    }

    public function listarestablecimiento($id, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('e' => 'establecimiento'))
            ->joinLeft(array('esdet' => 'establecimientoConfiguracion'), 'e.idEstablecimiento= esdet.idEstablecimiento', array('numeroDecreto', 'yeardecreto', 'aproxAsignatura', 'aproxPeriodo', 'aproxAnual', 'aproxFinal', 'monitoreo', 'idPeriodo','activarapp','tipoModalidad'));

        $select->where('e.idEstablecimiento=' . $id);
        $select->where('esdet.idPeriodo=' . $idperiodo);


        return $this->fetchAll($select)->toArray();
    }

    public function getestablecimiento($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'establecimiento'), '*');
        $select->where('c.idEstablecimiento= ?', $marca);
        $select->order("c.nombreEstablecimiento");
        $rowset = $this->fetchAll($select);
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idEstablecimiento] = $row->nombreEstablecimiento;
        }
        return $data;
    }

    public function getestablecimientos()
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'establecimiento'), '*');

        $select->order("c.nombreEstablecimiento");
        $rowset = $this->fetchAll($select);
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idEstablecimiento] = $row->nombreEstablecimiento;
        }
        return $data;
    }

    public function getconfig($id, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'establecimientoConfiguracion'), '*');
        $select->join(array('es' => 'establecimiento'), 'c.idEstablecimiento= es.idEstablecimiento', array('idEstablecimiento', 'tipoEvaluacion'));
        $select->where('c.idEstablecimiento= ?', $id);
        $select->where('c.idPeriodo= ?', $idperiodo);
        $rowset = $this->fetchAll($select);
        if (!$rowset) {
            return false;
        } else {
            return $rowset->toArray();
        }
    }

    public function agregarconfig($tipomodalidad,$numerodecreto, $yeardecreto, $aproxasignatura, $aproxperiodo, $aproxanual, $aproxfinal, $examen, $aproexamen, $monitoreo, $idperiodo, $idestablecimiento,$profesor,$apoderado,$director,$mostrarpromedio,$decimas,$activarapp)
    {
        $db = new Zend_Db_Table('establecimientoConfiguracion');

        $data = array('tipoModalidad'=>$tipomodalidad,'numeroDecreto' => $numerodecreto, 'yeardecreto' => $yeardecreto, 'aproxAsignatura' => $aproxasignatura, 'aproxPeriodo' => $aproxperiodo, 'aproxAnual' => $aproxanual, 'aproxFinal' => $aproxfinal, 'examen' => $examen, 'aproxExamen' => $aproexamen, 'monitoreo' => $monitoreo, 'idPeriodo' => $idperiodo, 'idEstablecimiento' => $idestablecimiento,'profesor'=>$profesor,'apoderado'=>$apoderado,'director'=>$director,'mostrarPromedio'=>$mostrarpromedio,'decimas'=>$decimas,'activarapp'=>$activarapp);
        $db->insert($data);
    }

    public function cambiarconfig($id,$tipomodalidad, $numerodecreto, $yeardecreto, $aproxasignatura, $aproxperiodo, $aproxanual, $aproxfinal, $examen, $aproexamen, $monitoreo,$profesor,$apoderado,$director,$mostrarpromedio,$decimas,$activarapp)
    {
        $db = new Zend_Db_Table('establecimientoConfiguracion');
        $data = array('tipoModalidad'=>$tipomodalidad,'numeroDecreto' => $numerodecreto, 'yeardecreto' => $yeardecreto, 'aproxAsignatura' => $aproxasignatura, 'aproxPeriodo' => $aproxperiodo, 'aproxAnual' => $aproxanual, 'aproxFinal' => $aproxfinal, 'examen' => $examen, 'aproxExamen' => $aproexamen, 'monitoreo' => $monitoreo,'profesor'=>$profesor,'apoderado'=>$apoderado,'director'=>$director,'mostrarPromedio'=>$mostrarpromedio,'decimas'=>$decimas,'activarapp'=>$activarapp);
        $db->update($data, 'idConfiguracion = ' . (int)$id);
    }

    public function listardecreto($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'decretos'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento')
            ->where('idPeriodo = ?', $idperiodo);

        return $this->fetchAll($select);

    }

    public function listardecretoestablecimiento($idperiodo, $idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'decretos'))
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento')
            ->where('p.idPeriodo = ?', $idperiodo)
            ->where('p.idEstablecimiento= ?', $idestablecimiento);

        return $this->fetchAll($select);

    }

    public function agregardecreto($decreto, $year, $idestablecimiento, $idperiodo)
    {
        $db = new Zend_Db_Table('decretos');
        $data = array('numeroDecreto' => $decreto, 'yearDecreto' => $year, 'idEstablecimiento' => $idestablecimiento, 'idPeriodo' => $idperiodo);
        $db->insert($data);
    }

    public function cambiardecreto($id, $decreto, $year)
    {
        $db = new Zend_Db_Table('decretos');
        $data = array('numeroDecreto' => $decreto, 'yearDecreto' => $year);
        $db->update($data, 'idDecreto = ' . (int)$id);
    }

    public function getdecreto($id, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'decretos'), '*');
        $select->where('c.idDecreto= ?', $id);
        $select->where('c.idPeriodo= ?', $idperiodo);
        $rowset = $this->fetchAll($select);
        if (!$rowset) {
            return false;
        } else {
            return $rowset->toArray();
        }
    }

    public function listaconfiguracion($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'establecimientoConfiguracion'), '*');
        $select->where('c.idPeriodo= ?', $idperiodo);
        $rowset = $this->fetchAll($select);
        return $rowset->toArray();

    }

    public function actualizardirector($iddirector, $idest)
    {

        $data = array('idDirector' => $iddirector);
        $this->update($data, 'idEstablecimiento = ' . (int)$idest);
    }
}
