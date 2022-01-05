<?php

class Application_Model_DbTable_Alumnoescolar extends Zend_Db_Table_Abstract
{

    protected $_name = 'alumnosEscolar';
    protected $_primary = 'idAlumnosEscolar';


    public function get($id)
    {
        $row = $this->fetchRow('idAlumnosActual = ' . $id);
        if (!$row) {
            throw new Exception("No se Encuentra el  $id");
        }

        return $row->toArray();
    }

    public function validar($id)
    {

        $row = $this->fetchRow('idAlumnosActual = ' . $id);
        if ($row) {
            return true;
        } else {
            return false;
        }

    }


    public function agregar($prioritario, $beneficio, $foto,  $pie, $religion, $junaeb, $autorizacion, $aprendizaje, $transporte, $idalumnoactual)
    {
        $data = array('prioritario' => $prioritario, 'beneficio' => $beneficio, 'foto' => $foto,  'pie' => $pie,
            'religion' => $religion, 'junaeb' => $junaeb, 'autorizacion' => $autorizacion, 'aprendizaje' => $aprendizaje, 'transporte' => $transporte, 'idAlumnosActual' => $idalumnoactual);

        $this->insert($data);

    }


    public function cambiar($idalumnoactual, $prioritario, $beneficio, $foto, $pie, $religion, $junaeb, $autorizacion, $aprendizaje, $transporte)
    {
        $data = array('prioritario' => $prioritario, 'beneficio' => $beneficio, 'foto' => $foto,  'pie' => $pie,
            'religion' => $religion, 'junaeb' => $junaeb, 'autorizacion' => $autorizacion, 'aprendizaje' => $aprendizaje, 'transporte' => $transporte);
        $this->update($data, 'idAlumnosActual = ' . (int)$idalumnoactual);
    }


    public function borrar($id)
    {
        $this->delete('idAlumnosEscolar=' . (int)$id);
    }

}

