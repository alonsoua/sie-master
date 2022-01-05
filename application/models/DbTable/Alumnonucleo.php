<?php

class Application_Model_DbTable_Alumnonucleo extends Zend_Db_Table_Abstract
{

    protected $_name = 'alumnosNucleo';
    protected $_primary = 'idAlumnosNucleo';


    public function get($rut)
    {
        $RUT = (int)$rut;
        $row = $this->fetchRow('idAlumnos = ' . $RUT);
        if (!$row) {
            throw new Exception("Could not find row $RUT");
        }

        return $row->toArray();
    }

    public function validar($id)
    {

        $row = $this->fetchRow('idAlumnos = ' . $id);
        if ($row) {
            return true;
        } else {
            return false;
        }

    }


    public function agregar($nombremadre, $paternomadre,$maternomadre, $telefonomadre, $nivelmadre, $nombrepadre, $paternopadre,$maternopadre, $telefonopadre, $nivelpadre, $rutpadre, $rutmadre, $ocupacionpadre, $ocupacionmadre, $idalumnos)
    {
        $data = array('nombremadre' => $nombremadre, 'paternomadre' => $paternomadre,'maternomadre'=>$maternomadre, 'telefonomadre' => $telefonomadre, 'nivelmadre' => $nivelmadre, 'nombrepadre' => $nombrepadre, 'paternopadre' => $paternopadre,'maternopadre'=>$maternopadre, 'telefonopadre' => $telefonopadre, 'nivelpadre' => $nivelpadre, 'rutPadre' => $rutpadre, 'rutMadre' => $rutmadre, 'ocupacionPadre' => $ocupacionpadre, 'ocupacionMadre' => $ocupacionmadre, 'idAlumnos' => $idalumnos);
        $this->insert($data);

    }


    public function cambiar($nombremadre, $paternomadre,$maternomadre, $telefonomadre, $nivelmadre, $nombrepadre, $paternopadre,$maternopadre, $telefonopadre, $nivelpadre, $rutpadre, $rutmadre, $ocupacionpadre, $ocupacionmadre, $vive, $nacimiento, $estudios, $personalidad, $idalumnos)
    {
        $data = array('nombremadre' => $nombremadre, 'paternomadre' => $paternomadre,'maternomadre'=>$maternomadre, 'telefonomadre' => $telefonomadre, 'nivelmadre' => $nivelmadre, 'nombrepadre' => $nombrepadre, 'paternopadre' => $paternopadre,'maternopadre'=>$maternopadre,'telefonopadre' => $telefonopadre,
            'nivelpadre' => $nivelpadre, 'rutPadre' => $rutpadre, 'rutMadre' => $rutmadre, 'ocupacionPadre' => $ocupacionpadre, 'ocupacionMadre' => $ocupacionmadre, 'vive' => $vive, 'nacimiento' => $nacimiento, 'estudio' => $estudios, 'personalidad' => $personalidad);
        $this->update($data, 'idAlumnos = ' . (int)$idalumnos);
    }


    public function borrar($id)
    {

        $this->delete('idAlumnos =' . (int)$id);
    }

}



