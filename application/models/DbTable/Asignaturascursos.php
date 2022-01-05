<?php

class Application_Model_DbTable_Asignaturascursos extends Zend_Db_Table_Abstract
{

    protected $_name = 'asignaturascursos';
    protected $_primary = 'idAsignaturaCurso';

    public function get($idasignatura, $idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('nu' => 'nucleos'), 'nu.idNucleo = a.idNucleo');

        $select->where('a.idCursos=' . $idcurso);
        $select->where('a.idAsignatura=' . $idasignatura);
        $select->where('a.idPeriodo=' . $idperiodo);
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function getnombre($idasignatura)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idAsignaturaCurso=' . $idasignatura);
        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function ultimo($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'), array('max' => new Zend_Db_Expr('MAX(orden)')));
        $select->where('a.idCursos=' . $idcurso);
        $select->where('a.idPeriodo=' . $idperiodo);

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function ultimoorden($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'), array('max' => new Zend_Db_Expr('MAX(orden)')));
        $select->where('a.idCursos=' . $idcurso);
        $select->where('a.idPeriodo=' . $idperiodo);
        $row = $this->fetchAll($select);
        return $row->toArray();

    }


    public function agregar($idasignatura, $tipoasignatura, $orden, $promedio, $nucleo, $idperiodo, $idcurso)
    {
        $data = array('idAsignatura' => $idasignatura, 'tipoAsignatura' => $tipoasignatura, 'orden' => $orden, 'promedio' => $promedio, 'estado' => 1, 'idNucleo' => $nucleo, 'idPeriodo' => $idperiodo, 'idCursos' => $idcurso, 'prioritaria' => 0);
        $this->insert($data);
    }


    public function cambiar($idasignatura, $tipoasignatura, $incide, $nucleo)
    {
        $data = array('idAsignaturaCurso' => $idasignatura, 'tipoAsignatura' => $tipoasignatura, 'promedio' => $incide, 'idNucleo' => $nucleo);
        $this->update($data, 'idAsignaturaCurso= ' . (int)$idasignatura);
    }

    public function eliminar($id)
    {
        $this->delete('idAsignaturaCurso= ' . (int)$id);
    }

    public function cambiarestadoorden($idasignatura, $orden, $prioritaria, $horas, $electivo)
    {
        $data = array('orden' => $orden, 'prioritaria' => $prioritaria, 'horas' => $horas, 'electivo' => $electivo);
        $this->update($data, 'idAsignaturaCurso = ' . (int)$idasignatura);
    }

    public function listarniveltipos($idcurso, $idperiodo, $tipos)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->where('a.estado=1');
        $select->where('a.tipoAsignatura IN(?)', $tipos);
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarniveltipospre($idcurso, $idperiodo, $tipos)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('n' => 'nucleos'), 'n.idNucleo = a.idNucleo');
        $select->joinLeft(array('am' => 'ambitos'), 'am.idAmbito = n.idAmbito');
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->where('a.estado=1');
        $select->where('a.tipoAsignatura IN(?)', $tipos);
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarporcurso($idcurso, $incide, $tipos, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idCursos=' . $idcurso);
        $select->where('a.estado=1');
        $select->where('a.promedio=' . $incide);
        $select->where('a.estado=1');
        $select->where('a.tipoAsignatura IN(?)', $tipos);
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.orden');
        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarporcursoprioritaria($idcurso, $incide, $tipos, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idCursos=' . $idcurso);
        $select->where('a.estado=1');
        $select->where('a.promedio=' . $incide);
        $select->where('a.estado=1');
        $select->where('a.prioritaria=0');
        $select->where('a.tipoAsignatura IN(?)', $tipos);
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.orden');
        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarporasignatura($idasignatura, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idAsignatura=' . $idasignatura);
        $select->where('a.estado=1');

        $select->where('a.estado=1');

        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.orden');
        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarnivelidcurso($idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('n' => 'nucleos'), 'n.idNucleo = a.idNucleo');
        $select->joinLeft(array('am' => 'ambitos'), 'am.idAmbito = n.idAmbito');
        $select->where('a.idCursos=' . $idcurso);
        $select->where('a.estado=1');
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function validar($idasignatura, $idperiodo, $idcurso)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->where('o.idAsignatura = ?', $idasignatura);
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos = ?', $idcurso);

        $stmt = $select->query();
        $row = $stmt->fetchAll();

        if (!$row) {
            return true;
        } else {

            return false;
        }

    }

    public function validarconfig($id, $idtaller)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuraciontaller'), '*');
        $select->where('o.idAsignaturaCurso = ?', $id);
        $select->where('o.idAsignaturaTaller = ?', $idtaller);
        $stmt = $select->query();
        $row = $stmt->fetchAll();

        if (!$row) {
            return true;
        } else {

            return false;
        }

    }

    public function validarconfiganual($idcurso, $idtaller, $segmento)
    {
        //Tiempo Taller 1= Anual 2= Semestral 3=Trimestral

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuraciontaller'), '*');
        $select->where('o.idAsignaturaCurso = ?', $idtaller);
        $select->where('o.idAsignaturaTaller = ?', $idcurso);
        $select->where('o.tiempoTaller IN(?)', $segmento);
        //$stmt = $select->query();
        $row = $this->fetchAll($select)->toArray();
        return $row;

    }

    public function validarconfigporperiodo($idcurso, $idtaller, $segmento, $segopcion)
    {
        //Tiempo Taller 1= Anual 2= Semestral 3=Trimestral

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuraciontaller'), '*');
        $select->where('o.idAsignaturaCurso = ?', $idtaller);
        $select->where('o.idAsignaturaTaller = ?', $idcurso);
        $select->where('o.tiempoTaller =?', $segmento);
        $select->where('o.tiempoOpcion =?', $segopcion);
        //$stmt = $select->query();
        $row = $this->fetchAll($select)->toArray();
        return $row;

    }

    public function getalumnostaller($idtaller)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuraciontaller'), '*');
        $select->join(array('det' => 'configuraciontallerdetalle'), 'det.idConfiguracionTaller=o.idConfiguracionTaller');
        $select->where('o.idAsignaturaCurso = ?', $idtaller);
        $row = $this->fetchAll($select)->toArray();

        return $row;

    }

    public function agregarconfiguracion($forma, $porcentaje, $idasignaturataller, $idasignaturacurso, $segmento, $opcionsegmento, $tipo)
    {
        $db = new Zend_Db_Table('configuraciontaller');
        $data = array('forma' => $forma, 'porcentaje' => $porcentaje, 'idAsignaturaTaller' => $idasignaturataller, 'idAsignaturaCurso' => $idasignaturacurso, 'tiempoTaller' => $segmento, 'tiempoOpcion' => $opcionsegmento, 'tipoAjuste' => $tipo);
        $db->insert($data);
    }

    public function agregarconfiguraciondetalle($idasignatura, $idalumno, $idconfigtaller)
    {
        $db = new Zend_Db_Table('configuraciontallerdetalle');
        $data = array('idAsignaturaDestino' => $idasignatura, 'idAlumnos' => $idalumno, 'idConfiguracionTaller' => $idconfigtaller);
        $db->insert($data);
    }


    public function cambiarconfiguracion($idconfig, $forma, $porcentaje, $idasignaturataller)
    {
        $db = new Zend_Db_Table('configuraciontaller');
        $data = array('forma' => $forma, 'porcentaje' => $porcentaje, 'idAsignaturaTaller' => $idasignaturataller);
        $db->update($data, 'idConfiguracionTaller= ' . (int)$idconfig);
    }

    public function getconfiguracion($idasignatura)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuraciontaller'));
        $select->where('a.idAsignaturaCurso=' . $idasignatura);

        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function agregardep($asignaturas, $idasignaturacurso, $estado)
    {
        $db = new Zend_Db_Table('configuraciondependencia');
        $data = array('asignaturas' => $asignaturas, 'estadonp' => $estado, 'idAsignaturaCurso' => $idasignaturacurso);
        $db->insert($data);
    }


    public function cambiardep($idconfig, $asignaturas, $estado)
    {
        $db = new Zend_Db_Table('configuraciondependencia');
        $data = array('asignaturas' => $asignaturas, 'estadonp' => $estado);
        $db->update($data, 'idConfiguracionDependencia= ' . (int)$idconfig);
    }

    public function getdep($idasignatura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuraciondependencia'));
        $select->where('a.idAsignaturaCurso=' . $idasignatura);

        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function validardep($id)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuraciondependencia'), '*');
        $select->where('o.idAsignaturaCurso = ?', $id);

        $stmt = $select->query();
        $row = $stmt->fetchAll();

        if (!$row) {
            return true;
        } else {

            return false;
        }

    }

    public function cambiartipoasignatura($id, $estado, $inciden)
    {
        $data = array('tipoAsignatura' => $estado, 'promedio' => $inciden);
        $this->update($data, 'idAsignaturaCurso= ' . (int)$id);
    }

    public function cambiarestadoasignatura($id)
    {
        $data = array('estado' => 0);
        $this->update($data, 'idAsignaturaCurso= ' . (int)$id);
    }

    public function getasignaturajson($idcurso, $idperiodo, $tipos)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos = ?', $idcurso);
        $select->where('o.tipoAsignatura IN(?)', $tipos);
        $select->where('o.estado=1');
        $select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getasignaturas($listaasignaturas)
    {
        // $select = $this->select();
        // $select->setIntegrityCheck(false);
        // $select->from(array('o' => 'asignaturascursos'), '*');
        // $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        // $select->joinleft(array('nu' => 'nucleos'), 'nu.idNucleo = o.idNucleo');
        // $select->where('o.idAsignaturaCurso IN(?)', $listaasignaturas);
        // $select->where('o.estado=1');
        // $select->group('o.idNucleo');
        // $rowset = $this->fetchAll($select);
        // return $rowset;

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('o.idAsignaturaCurso IN(?)', $listaasignaturas);
        $select->where('o.estado=1');
        $rowset = $this->fetchAll($select);
        return $rowset;
    }

    public function getasignaturasid($idasignatura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('o.idAsignatura =?', $idasignatura);
        $select->where('o.estado=1');
        $rowset = $this->fetchAll($select);
        return $rowset;
    }


    public function getasignaturaspornucleo($listaasignaturas)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), array('idNucleo', 'estado', 'promedio', 'tipoAsignatura'));
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura'));
        $select->join(array('nu' => 'nucleos'), 'nu.idNucleo = o.idNucleo', array('*'));
        $select->where('o.idAsignaturaCurso IN(?)', $listaasignaturas);
        $select->where('o.estado=1');
        $select->group('o.idNucleo');
        $select->order('o.idNucleo');
        $rowset = $this->fetchAll($select);
        return $rowset;
    }


    public function getasignaturasporambito($listaasignaturas)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), array(''));
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array(''));
        $select->join(array('nu' => 'nucleos'), 'nu.idNucleo = o.idNucleo', array(''));
        $select->join(array('am' => 'ambitos'), 'am.idAmbito = nu.idAmbito', array('*'));
        $select->where('o.idAsignaturaCurso IN(?)', $listaasignaturas);
        $select->where('o.estado=1');
        $select->group('am.idAmbito');
        $select->order('am.idAmbito');
        $rowset = $this->fetchAll($select);
        return $rowset;
    }

    public function getasignaturastipos($listaasignaturas, $tipos)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('o.idAsignaturaCurso IN(?)', $listaasignaturas);
        $select->where('o.tipoAsignatura IN(?)', $tipos);
        $select->where('o.estado=1');
        $rowset = $this->fetchAll($select);
        return $rowset;
    }

    public function gettaller($idcurso, $idperiodo, $tiempo, $ajuste, $segmento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('ct' => 'configuraciontaller'), 'ct.idAsignaturaCurso = o.idAsignaturaCurso');
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos = ?', $idcurso);
        $select->where('o.tipoAsignatura=2');
        $select->where('ct.tiempoTaller IN(?)', $tiempo);
        $select->where('ct.tiempoOpcion IN(?)', $segmento);
        $select->where('ct.tipoAjuste=?', $ajuste);
        $select->where('o.estado=1');
        //$select->order('ct.tiempoOpcion');
        $select->order('o.orden');


        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }


    public function gettaller2($idasignatura, $idperiodo, $tiempo, $ajuste, $segmento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('ct' => 'configuraciontaller'), 'ct.idAsignaturaCurso = o.idAsignaturaCurso');
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('ct.idAsignaturaTaller=' . $idasignatura);
        $select->where('o.tipoAsignatura=2');
        $select->where('ct.tiempoTaller IN(?)', $tiempo);
        $select->where('ct.tiempoOpcion IN(?)', $segmento);
        $select->where('ct.tipoAjuste=?', $ajuste);
        $select->where('o.estado=1');
        //$select->order('ct.tiempoOpcion');
        $select->order('o.orden');


        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function gettalleranual($idasignatura, $idperiodo, $tiempo, $ajuste)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('ct' => 'configuraciontaller'), 'ct.idAsignaturaCurso = o.idAsignaturaCurso');
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('ct.idAsignaturaTaller=' . $idasignatura);
        $select->where('o.tipoAsignatura=2');
        $select->where('ct.tiempoTaller IN(?)', $tiempo);
        $select->where('ct.tipoAjuste=?', $ajuste);
        $select->where('o.estado=1');
        $select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function gettalleranual2($idcurso, $idperiodo, $tiempo, $ajuste)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('ct' => 'configuraciontaller'), 'ct.idAsignaturaCurso = o.idAsignaturaCurso');
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos = ?', $idcurso);
        $select->where('o.tipoAsignatura=2');
        $select->where('ct.tiempoTaller IN(?)', $tiempo);
        $select->where('ct.tipoAjuste=?', $ajuste);
        $select->where('o.estado=1');
        $select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function gettallersin($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('ct' => 'configuraciontaller'), 'ct.idAsignaturaCurso = o.idAsignaturaCurso');
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos = ?', $idcurso);
        $select->where('o.tipoAsignatura=2');
        $select->where('o.estado=1');
        $select->where('ct.idConfiguracionTaller IS NULL');
        $select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getcombinada($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('ct' => 'configuraciondependencia'), 'ct.idAsignaturaCurso = o.idAsignaturaCurso');
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos = ?', $idcurso);
        $select->where('o.tipoAsignatura=3');
        $select->where('o.estado=1');
        $select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getcombinadaasignatura($idcurso, $idperiodo, $idasignatura)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('ct' => 'configuraciondependencia'), 'ct.idAsignaturaCurso = o.idAsignaturaCurso');
        $select->where('o.idAsignatura= ?', $idasignatura);
        $select->where('o.idPeriodo= ?', $idperiodo);
        $select->where('o.idCursos = ?', $idcurso);
        $select->where('o.tipoAsignatura=3');
        $select->where('o.estado=1');
        $select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listarambito()
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'ambitos'));
        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarnucleo()
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'nucleos'));
        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarnucleoambito($idambito)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'nucleos'));
        $select->where('a.idAmbito=?', $idambito);
        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarasignaturapornucleo($idnucleo, $idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array("a" => "asignaturascursos"));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where("a.idNucleo=?", $idnucleo);
        $select->where("a.idCursos=?", $idcurso);
        $select->where("a.idPeriodo=?", $idperiodo);
        $select->where("a.estado=1");
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarnucleoporambito($idambito)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'nucleos'));
        $select->where('a.idAmbito=?', $idambito);

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarporcursopre($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('n' => 'nucleos'), 'a.idNucleo = n.idNucleo');
        $select->join(array('am' => 'ambitos'), 'n.idAmbito = am.idAmbito');
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.estado=1');
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.idNucleo');
        $select->order('a.orden');
        //$select->group('a.idNucleo');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarporcursopregreda($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('n' => 'nucleos'), 'a.idNucleo = n.idNucleo');
        $select->join(array('am' => 'ambitos'), 'n.idAmbito = am.idAmbito');
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.estado=1');
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.idNucleo');
        $select->order('a.orden');
        $select->group('a.idNucleo');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarporcursopremonitoreolag($idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('n' => 'nucleos'), 'a.idNucleo = n.idNucleo');
        $select->join(array('am' => 'ambitos'), 'n.idAmbito = am.idAmbito');
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.estado=1');
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.idNucleo');
        $select->order('a.orden');


        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function validarconcepto($id, $concepto)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuracionconceptos'), '*');
        $select->where('o.idAsignaturaCurso = ?', $id);
        $select->where('o.concepto = ?', $concepto);
        $stmt = $select->query();
        $row = $stmt->fetchAll();

        if (!$row) {
            return true;
        } else {

            return false;
        }

    }

    public function agregarconcepto($concepto, $nota, $desde, $hasta, $idasignaturacurso)
    {
        $db = new Zend_Db_Table('configuracionconceptos');
        $data = array('concepto' => $concepto, 'notaconcepto' => $nota, 'desde' => $desde, 'hasta' => $hasta, 'idAsignaturaCurso' => $idasignaturacurso);
        $db->insert($data);
    }


    public function cambiarconcepto($id, $concepto, $nota)
    {
        $db = new Zend_Db_Table('configuracionconceptos');
        $data = array('concepto' => $concepto, 'notaconcepto' => $nota);
        $db->update($data, 'idConcepto= ' . (int)$id);
    }

    public function getconcepto($idasignatura)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuracionconceptos'));
        $select->where('a.idAsignaturaCurso=' . $idasignatura);

        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function getasignaturaconcepto($idasignatura, $idcurso, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('c' => 'configuracionconceptos'), 'a.idAsignaturaCurso = c.idAsignaturaCurso');

        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.idAsignatura=?', $idasignatura);
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function gettallerasignatura($idasignatura)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuraciontaller'));
        $select->join(array('as' => 'asignaturascursos'), 'a.idAsignaturaCurso = as.idAsignaturaCurso', array('idAsignatura'));
        $select->join(array('asg' => 'asignaturas'), 'as.idAsignatura = asg.idAsignatura', array('idAsignatura', 'nombreAsignatura'));

        $select->where('a.idAsignaturaTaller=' . $idasignatura);
        $select->order('a.tiempoOpcion');
        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function gettallerasignaturaalumnos($idasignatura)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuraciontaller'));
        $select->join(array('as' => 'asignaturascursos'), 'a.idAsignaturaCurso = as.idAsignaturaCurso', array('idAsignatura'));
        $select->join(array('asg' => 'asignaturas'), 'as.idAsignatura = asg.idAsignatura', array('idAsignatura', 'nombreAsignatura'));

        $select->where('a.idAsignaturaTaller=' . $idasignatura);
        $select->where('a.tipoAjuste=2');
        $select->order('a.tiempoOpcion');
        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function gettallerconfiguracion($idasignatura)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuraciontaller'));
        $select->join(array('o' => 'asignaturascursos'), 'o.idAsignatura= a.idAsignaturaTaller');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idAsignaturaCurso=?', $idasignatura);
        //$select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }


    public function eliminartaller($id)
    {
        $db = new Zend_Db_Table('configuraciontaller');
        $db->delete('idConfiguracionTaller= ' . (int)$id);
    }

    public function eliminartallerdetalle($id)
    {
        $db = new Zend_Db_Table('configuraciontallerdetalle');
        $db->delete('idConfiguracionTaller= ' . (int)$id);
    }

    public function gettallerdetalle($idconfig, $idalumnos)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuraciontallerdetalle'), '*');
        $select->where('o.idConfiguracionTaller= ?', $idconfig);
        $select->where('o.idAlumnos = ?', $idalumnos);


        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function gettallerdetalles($idconfig, $idalumnos)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuraciontallerdetalle'), '*');
        $select->join(array('c' => 'configuraciontaller'), 'o.idConfiguracionTaller=c.idConfiguracionTaller');
        $select->where('o.idConfiguracionTaller= ?', $idconfig);
        $select->where('o.idAlumnos = ?', $idalumnos);


        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function gettallersegmento($idtaller, $segmento, $idalumno)
    {
        /*Codigos
        tiempoTaller 1=Anual 2=Semestral 3= Trimestral
        tiempoOpcion si tiempoTaller es 1 ....1=Primer Semestre 2=Segundo Semestre
        tiempoOpcion si tiempoTaller es 2 ....1=Primer Trimestre2=Segundo Trimestre 3= Tercer Trimestre
        tipoAjuste 1=Curso 2=lista alumnos

        */

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('ct' => 'configuraciontaller'), '*');
        $select->joinLeft(array('det' => 'configuraciontallerdetalle'), 'ct.idConfiguracionTaller= det.idConfiguracionTaller');
        $select->join(array('o' => 'asignaturascursos'), 'o.idAsignaturaCurso = ct.idAsignaturaCurso');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('ct.idConfiguracionTaller= ?', $idtaller);
        $select->where('ct.tiempoOpcion IN(?)', $segmento);
        $select->where('ct.tiempoTaller =2');
        $select->where('ct.tipoAjuste = 2');
        $select->where('det.idAlumnos = ?', $idalumno);
        //$select->where('o.tipoAsignatura=2');
        $select->where('o.estado=1');
        $select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getdestino($idasignatura)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->where('o.idAsignatura= ?', $idasignatura);
        return $this->fetchAll($select)->toArray();
    }

    public function getconceptoconfiguracion($idasignatura)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuracionconceptos'));
        $select->join(array('o' => 'asignaturascursos'), 'o.idAsignaturaCurso= a.idAsignaturaCurso');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idAsignaturaCurso=?', $idasignatura);
        //$select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getconceptoid($idconcepto)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuracionconceptos'));
        $select->where('a.idConcepto=' . $idconcepto);

        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function cambiarconceptoid($idconcepto, $concepto, $rango, $desde, $hasta)
    {
        $db = new Zend_Db_Table('configuracionconceptos');
        $data = array('concepto' => $concepto, 'notaconcepto' => $rango, 'desde' => $desde, 'hasta' => $hasta);
        $db->update($data, 'idConcepto= ' . (int)$idconcepto);
    }

    public function eliminarconcepto($id)
    {
        $db = new Zend_Db_Table('configuracionconceptos');
        $db->delete('idConcepto= ' . (int)$id);
    }

    //Prebasica

    public function getconceptoconfiguracionprebasica($idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuracionconceptosprebasica'));
        $select->join(array('o' => 'cursosactual'), 'o.idCursos= a.idCursos');
        $select->where('a.idCursos=?', $idcurso);
        //$select->order('o.orden');

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getconceptoidparvularia($idconcepto)
    {


        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuracionconceptosprebasica'));
        $select->where('a.idConcepto=' . $idconcepto);

        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function validarconceptoparvularia($idcurso, $concepto)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'configuracionconceptosprebasica'), '*');
        $select->where('o.idCursos = ?', $idcurso);
        $select->where('o.concepto = ?', $concepto);
        $stmt = $select->query();
        $row = $stmt->fetchAll();

        if (!$row) {
            return true;
        } else {

            return false;
        }

    }

    public function agregarconceptoparvularia($concepto, $nota, $descripcion, $idcurso)
    {
        $db = new Zend_Db_Table('configuracionconceptosprebasica');
        $data = array('concepto' => $concepto, 'notaConcepto' => $nota, 'descripcionConcepto' => $descripcion, 'idCursos' => $idcurso);
        $db->insert($data);
    }


    public function cambiarconceptoparvularia($id, $concepto, $nota, $descripcion)
    {
        $db = new Zend_Db_Table('configuracionconceptosprebasica');
        $data = array('concepto' => $concepto, 'notaConcepto' => $nota, 'descripcionConcepto' => $descripcion);
        $db->update($data, 'idConcepto= ' . (int)$id);
    }

    public function eliminarconceptoparvularia($id)
    {
        $db = new Zend_Db_Table('configuracionconceptosprebasica');
        $db->delete('idConcepto= ' . (int)$id);
    }

    public function getconceptosparvularia($idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuracionconceptosprebasica'));
        $select->where('a.idCursos=?', $idcurso);
        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function listarasignaturacurso($idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('nu' => 'nucleos'), 'nu.idNucleo = a.idNucleo');
        $select->where('a.idCursos=' . $idcurso);
        $row = $this->fetchAll($select);
        return $row->toArray();
    }

    public function getasignaturasarray($listaasignaturas)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), '*');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('o.idAsignaturaCurso IN(?)', $listaasignaturas);
        $select->where('o.estado=1');
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }


    public function listaragrupar($idcurso, $idperiodo, $tipos)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->joinLeft(array('n' => 'nucleos'), 'n.idNucleo = a.idNucleo');
        $select->joinLeft(array('am' => 'ambitos'), 'am.idAmbito = n.idAmbito');
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->where('a.estado=1');
        $select->where('a.tipoAsignatura IN(?)', $tipos);
        $select->group('am.idAmbito');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function getnucleos($idambito)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('o' => 'asignaturascursos'), array('idNucleo', 'estado', 'promedio', 'tipoAsignatura'));
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura'));
        $select->join(array('nu' => 'nucleos'), 'nu.idNucleo = o.idNucleo', array('*'));
        $select->where('nu.idAmbito=?', $idambito);
        $select->where('o.estado=1');
        $select->group('o.idNucleo');
        $select->order('o.idNucleo');
        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listarporasignaturapre($idcurso, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('n' => 'nucleos'), 'a.idNucleo = n.idNucleo');
        $select->join(array('am' => 'ambitos'), 'n.idAmbito = am.idAmbito');
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.estado=1');
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.idNucleo');
        $select->order('a.orden');
        $select->group('a.idNucleo');

        $row = $this->fetchAll($select);
        return $row->toArray();


    }

    public function listaroapre($idnucleo, $idcurso, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->join(array('n' => 'nucleos'), 'a.idNucleo = n.idNucleo');
        $select->join(array('am' => 'ambitos'), 'n.idAmbito = am.idAmbito');
        $select->where('a.idCursos=?', $idcurso);
        $select->where('a.idNucleo=?', $idnucleo);
        $select->where('a.estado=1');
        $select->where('a.idPeriodo=?', $idperiodo);
        $select->order('a.idNucleo');
        $select->order('a.orden');


        return $this->fetchAll($select);
        //return $row->toArray();


    }

    public function getoa($idoa)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'asignaturascursos'));
        $select->join(array('as' => 'asignaturas'), 'a.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idAsignatura=?', $idoa);
        $row = $this->fetchAll($select);
        return $row->toArray();


    }

    public function actualizarhoraasignada($hora, $idasignatura)
    {
        $db = new Zend_Db_Table('asignaturascursos');
        $data = array('horasAsignadas' => $hora);
        $db->update($data, 'idAsignatura = ' . (int)$idasignatura);
    }


    public function getelectivoconfiguracion($idasignatura)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'configuracionelectivo'));
        $select->join(array('o' => 'asignaturascursos'), 'o.idAsignatura= a.idAsignatura');
        $select->join(array('as' => 'asignaturas'), 'o.idAsignatura = as.idAsignatura', array('idAsignatura', 'nombreAsignatura'));
        $select->where('a.idAsignatura=?', $idasignatura);

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function agregarconfiguracionelectivo($idasignatura, $tiempo)
    {
        $db = new Zend_Db_Table('configuracionelectivo');
        $data = array('idAsignatura' => $idasignatura, 'tiempo' => $tiempo);
        $db->insert($data);
    }

    public function agregaralumnoselectivo($idalumno, $iddetalle)
    {
        $db = new Zend_Db_Table('configuracionelectivoalumnos');
        $data = array('idAlumnos' => $idalumno, 'idConfiguracionElectivo' => $iddetalle);
        $db->insert($data);
    }

    public function getalumnoselectivo($idasignatura)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'configuracionelectivo'), '*');
        $select->join(array('det' => 'configuracionelectivoalumnos'), 'det.idConfiguracionElectivo=c.idConfiguracionElectivo');
        $select->where('c.idAsignatura = ?', $idasignatura);
        $row = $this->fetchAll($select)->toArray();

        return $row;

    }

    public function validarelectivo($idasignatura,$tiempo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('c' => 'configuracionelectivo'), '*');
        $select->join(array('det' => 'configuracionelectivoalumnos'), 'det.idConfiguracionElectivo=c.idConfiguracionElectivo');
        $select->where('c.idAsignatura = ?', $idasignatura);
        $select->where('c.tiempo = ?', $tiempo);
        $row = $this->fetchAll($select)->toArray();

        if($row){
            return true;
        }

        return false;

    }


}
