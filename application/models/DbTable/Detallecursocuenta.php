<?php

class Application_Model_DbTable_Detallecursocuenta extends Zend_Db_Table_Abstract
{

    protected $_name = 'cuentascurso';


    public function agregar($idcurso, $asignaturas, $idrol)
    {
        $data = array('idCursos' => $idcurso, 'asignaturasLista' => $asignaturas, 'idCuentaRol' => $idrol);
        $this->insert($data);
    }




    public function listar($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentascurso'), '*');
        $select->join(array('cu' => 'cursos'),
            'cu.idCursos = c.idCursos');
        $select->join(array('es' => 'establecimiento'),
            'cu.idEstablecimiento = es.idEstablecimiento');
        $select->join(array('niv' => 'cuentasnivel'),
            'c.idCuentaNivel = niv.idCuentaNivel');
        $select->where('c.idCuentaNivel = ?', $marca);
        $select->order("cu.idCursos");
        return $this->fetchAll($select);
    }

    public function borrar($id)
    {

        $this->delete('idCuentaRol =' . (int)$id);

    }

    public function listarcursousuario($id)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentascurso'), '*');
        $select->join(array('p' => 'cursosactual'),
            'p.idCursos = c.idCursos')
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento')
            ->joinLeft(array('esc' => 'establecimientoConfiguracion'), 'es.idEstablecimiento= esc.idEstablecimiento', array('tipoModalidad','idConfiguracion'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado');
        $select->where('c.idCuentaRol = ?', $id);
        $select->order("p.idCursos");

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listarcursoasignatura($iddetallecurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentascurso'), '*');
        $select->join(array('p' => 'cursosactual'),
            'p.idCursos = c.idCursos')
            ->join(array('es' => 'establecimiento'), 'p.idEstablecimiento= es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'p.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'p.idCodigoGrado= g.idCodigoGrado');
        $select->where('c.idCuentaCurso = ?', $iddetallecurso);
        $select->order("p.idCursos");

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function listarcursousuariojson($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentascurso'), '*');
        $select->join(array('p' => 'cursosactual'),
            'p.idCursos = c.idCursos');
        $select->where('c.idCuentaRol = ?', $marca);
        $select->order("p.idCursos");

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }

    public function getcursolibro($idrol,$idcurso)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentascurso'), '*');
        $select->join(array('p' => 'cursosactual'),
            'p.idCursos = c.idCursos');
        $select->where('c.idCuentaRol = ?', $idrol);
        $select->where('c.idCursos = ?', $idcurso);
        $select->order("p.idCursos");

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;
    }


}

?>
