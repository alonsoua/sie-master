<?php

class Application_Model_DbTable_Asignaturas extends Zend_Db_Table_Abstract
{

    protected $_name    = 'asignaturas';
    protected $_primary = 'idAsignatura';
    public function get($idasignaturas)
    {

        $idAsignaturas = (int) $idasignaturas;
        //$this->fetchRow devuelve fila donde idPeriodo = $idPeriodo
        $row = $this->fetchRow('idAsignatura = ' . $idAsignaturas);
        if (!$row) {
            throw new Exception("No se Encuentra el dato $idAsignaturas");
        }

        return $row->toArray();
    }

    public function ultimo($nivel)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'), array('max' => new Zend_Db_Expr('MAX(orden)')))
            ->where('a.idNiveles=' . $nivel);

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function ultimoid()
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'), array('max' => new Zend_Db_Expr('MAX(idAsignatura)')));
        $row = $this->fetchAll($select);
        return $row->toArray();

    }


    public function agregar($idasignatura, $nombre_asignatura, $orden, $promedio, $nivel)
    {
        $data = array('idAsignatura' => $idasignatura, 'nombreAsignatura' => $nombre_asignatura, 'orden' => $orden, 'promedio' => $promedio, 'estado' => 1, 'idNiveles' => $nivel, 'activo' => 1);
        //$this->insert inserta nuevo Establecimiento
        $this->insert($data);
    }


    public function cambiar($idasignatura, $nombre_asignatura)
    {
        $data = array('idAsignatura' => $idasignatura, 'nombreAsignatura' => $nombre_asignatura);
        //$this->update cambia datos de Establecimiento con RBD= $RBD
        $this->update($data, 'idAsignatura= ' . (int) $idasignatura);
    }

    public function cambiarestadoorden($idasignatura, $orden, $promedio, $estado)
    {
        $data = array('orden' => $orden, 'promedio' => $promedio, 'estado' => $estado);
        //$this->update cambia datos de Establecimiento con RBD= $RBD
        $this->update($data, 'idAsignatura = ' . (int) $idasignatura);
    }

    public function listar($nivel)
    {
        //devuelve todos los registros de la tabla asignatura
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'))
            ->join(array('n' => 'niveles'), 'a.idNiveles = n.idNiveles')
            ->where('a.idNiveles=' . $nivel);

        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarporcurso($nivel, $estado,$activo)
    {
        //devuelve todos los registros de la tabla

        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'))

            ->where('a.idCursos=' . $nivel)
            ->where('a.estado=1')
            ->where('a.promedio=' . $estado)
            ->where('a.activo=' . $activo);
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarnivelselect($nivel)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'))

            ->where('a.idNiveles=' . $nivel)
            ->where('a.estado=1');
        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarnucleo()
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'nucleos'));

        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarambito()
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'ambitos'));

        $row = $this->fetchAll($select);
        return $row;

    }



    public function listarnucleoporambito($idambito)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'nucleos'))
            ->where('a.idAmbito=' . $idambito);

        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarasignaturapornucleo($idnucleo, $idniveles)
    {

        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array("a" => "asignaturas"))
            ->where("a.idNucleo IN(" . $idnucleo . ")")
            ->where("a.idNiveles=?", $idniveles)
            ->where("a.estado=1")
            ->order('a.orden');

        $row = $this->fetchAll($select);
        return $row;

    }

    public function listarporcursopre($nivel, $estado)
    {
        //devuelve todos los registros de la tabla

        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'))
            ->join(array('n' => 'nucleos'), 'a.idNucleo = n.idNucleo')
            ->join(array('am' => 'ambitos'), 'n.idAmbito = am.idAmbito')
            ->where('a.idNiveles=' . $nivel)
            ->where('a.estado=1')
            ->where('a.promedio=' . $estado);
        $select->order('a.idNucleo');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarnivelidcurso($idcurso)
    {
        //devuelve todos los registros de la tabla asignatura
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'))
            ->join(array('n' => 'niveles'), 'a.idNiveles = n.idNiveles')
            ->join(array('c' => 'cursos'), 'c.idNiveles = n.idNiveles')
            ->where('c.idCursos=' . $idcurso)
            ->where('a.activo=' . 0);

        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

    public function listarnivelidcursopre($idcurso)
    {
        //devuelve todos los registros de la tabla asignatura
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)

            ->from(array('a' => 'asignaturas'))
            ->join(array('n' => 'niveles'), 'a.idNiveles = n.idNiveles')
            ->join(array('c' => 'cursos'), 'c.idNiveles = n.idNiveles')
            ->where('c.idCursos=' . $idcurso)
            ->where('a.activo=' . 0);

        $select->order('a.orden');

        $row = $this->fetchAll($select);
        return $row->toArray();

    }

}
