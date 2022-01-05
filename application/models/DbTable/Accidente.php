<?php

class Application_Model_DbTable_Accidente extends Zend_Db_Table_Abstract
{

    protected $_name = 'accidente';
    protected $_primary = 'idAccidente';


    public function get($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('idAccidente = ' . $id);
        if (!$row) {
            return false;
        } else {
            return $row->toArray();
        }


    }

    public function getaccidente($idaccidente, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'accidente'))
            ->join(array('ala' => 'AlumnosActual'), 'ala.idAlumnos = p.idAlumnos')
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos',array('nombres','apaterno','amaterno','fechanacimiento','sexo'))
            ->join(array('e' => 'cursosactual'), 'ala.idCursosActual = e.idCursos')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idAccidente=' . $idaccidente);
        $select->where('p.idPeriodo=' . $idperiodo);


        return $this->fetchAll($select)->toArray();


    }

    public function gettestigos($idaccidente)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'accidentetestigo'));
        $select->where('p.idAccidente=' . $idaccidente);

        return $this->fetchAll($select)->toArray();

    }


    public function agregar($fecha,$fecharegistro, $tipo, $numero, $descripcion, $idalumno, $idperiodo, $idestablecimiento, $idcuenta)
    {
        $data = array('fechaAccidente' => $fecha,'fechaRegistro'=>$fecharegistro, 'tipoAccidente' => $tipo, 'numeroAccidente' => $numero, 'descripcionAccidente' => $descripcion, 'idAlumnos' => $idalumno, 'idPeriodo' => $idperiodo, 'idEstablecimiento' => $idestablecimiento, 'idCuenta' => $idcuenta);
        $this->insert($data);


    }


    public function cambiar($id, $fecha, $tipo, $numero, $descripcion, $idalumno, $idperiodo, $idestablecimiento, $idcuenta)
    {
        $data = array('fechaAccidente' => $fecha, 'tipoAccidente' => $tipo, 'numeroAccidente' => $numero, 'descripcionAccidente' => $descripcion, 'idAlumnos' => $idalumno, 'idPeriodo' => $idperiodo, 'idEstablecimiento' => $idestablecimiento, 'idCuenta' => $idcuenta);
        $this->update($data, 'idAccidente = ' . (int)$id);
    }

    public function actualizararchivo($idaccidente, $archivo)
    {
        $data = array('archivo' => $archivo);
        $this->update($data, 'idAccidente = ' . (int)$idaccidente);
    }

    public function agregartestigo($nombretestigo, $ruttestigo, $idaccidente)
    {
        $db = new Zend_Db_Table('accidentetestigo');
        $data = array('nombreTestigo' => $nombretestigo, 'rutTestigo' => $ruttestigo, 'idAccidente' => $idaccidente);
        $db->insert($data);


    }


    public function borrar($id)
    {
        $this->delete('idAccidente =' . (int)$id);
    }


    public function listarestablecimiento($idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'accidente'))
            ->join(array('ala' => 'AlumnosActual'), 'ala.idAlumnos = p.idAlumnos')
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos',array('nombres','apaterno','amaterno'))
            ->join(array('e' => 'cursosactual'), 'ala.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idEstablecimiento=' . $idestablecimiento);
        $select->where('p.idPeriodo=' . $idperiodo);
        $select->order("es.idEstablecimiento");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);


    }

    public function listar($idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'accidente'))
            ->join(array('ala' => 'AlumnosActual'), 'ala.idAlumnos = p.idAlumnos')
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos',array('nombres','apaterno','amaterno'))
            ->join(array('e' => 'cursosactual'), 'ala.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idPeriodo=' . $idperiodo);
        $select->order("es.idEstablecimiento");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select);


    }

    public function listardocente($idcuenta,$idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'accidente'))
            ->join(array('ala' => 'AlumnosActual'), 'ala.idAlumnos = p.idAlumnos')
            ->join(array('al' => 'alumnos'), 'al.idAlumnos = p.idAlumnos',array('nombres','apaterno','amaterno'))
            ->join(array('e' => 'cursosactual'), 'ala.idCursosActual = e.idCursos')
            ->join(array('es' => 'establecimiento'), 'e.idEstablecimiento = es.idEstablecimiento')
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'e.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'e.idCodigoGrado= g.idCodigoGrado');
        $select->where('p.idCuenta=' . $idcuenta);
        $select->where('p.idEstablecimiento=' . $idestablecimiento);
        $select->where('p.idPeriodo=' . $idperiodo);
        $select->order("es.idEstablecimiento");
        $select->order("al.apaterno");
        $select->order("al.amaterno");

        return $this->fetchAll($select)->toArray();


    }

    public function ultimo($idestanlecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('a' => 'accidente'), array('max' => new Zend_Db_Expr('MAX(numeroAccidente)')));
        $select->where('a.idEstablecimiento=' . $idestanlecimiento);
        $select->where('a.idPeriodo=' . $idperiodo);

        $row = $this->fetchAll($select);
        return $row->toArray();

    }
}
