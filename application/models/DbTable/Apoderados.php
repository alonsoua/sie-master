<?php

class Application_Model_DbTable_Apoderados extends Zend_Db_Table_Abstract
{

    protected $_name = 'apoderados';
    protected $_primary = 'idApoderado';


    public function get($rut)
    {
        $RUT = (int)$rut;
        //$this->fetchRow devuelve fila donde Rut = $RUT
        $row = $this->fetchRow('idApoderado = ' . $RUT);
        if (!$row) {
            return false;
        }else{
            return $row->toArray();
        }


    }




    public function getrut($rut)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('ap' => 'apoderados'))
            ->where('ap.rutApoderado = ?', $rut);
        $row = $this->fetchRow($select);
        if ($row) {
            return $row->toArray();
        } else {
            return false;
        }

    }




    public function agregar($rut, $nombres, $apaterno, $amaterno, $direccion, $telefono, $comuna, $correo)
    {
        $data = array('rutApoderado' => $rut, 'nombreApoderado' => $nombres, 'paternoApoderado' => $apaterno, 'maternoApoderado' => $amaterno, 'direccionApoderado' => $direccion, 'telefonoApoderado' => $telefono, 'comunaApoderado' => $comuna, 'correoApoderado' => $correo
        );
        $this->insert($data);


    }


    public function cambiar($id, $rut, $nombres, $apaterno, $amaterno, $direccion, $telefono, $comuna, $correo)
    {
        $data = array('rutApoderado' => $rut, 'nombreApoderado' => $nombres, 'paternoApoderado' => $apaterno, 'maternoApoderado' => $amaterno, 'direccionApoderado' => $direccion, 'telefonoApoderado' => $telefono, 'comunaApoderado' => $comuna, 'correoApoderado' => $correo
        );
        $this->update($data, 'idApoderado = ' . (int)$id);
    }


    public function cambiarporrut($rut, $nombres, $apaterno, $amaterno, $direccion, $telefono, $comuna, $correo)
    {
        $data = array('nombreApoderado' => $nombres, 'paternoApoderado' => $apaterno, 'maternoApoderado' => $amaterno, 'direccionApoderado' => $direccion, 'telefonoApoderado' => $telefono, 'comunaApoderado' => $comuna, 'correoApoderado' => $correo
        );
        //$this->update($data, array('id = ?'=>$id));
        $this->update($data, array('rutApoderado = ?'=>$rut));

    }


    public function borrar($id)
    {
        //$this->delete borrar alumno donde Rut=$rut
        $this->delete('idApoderado =' . (int)$id);
    }

    public function validar($rut)
    {
        $Rut = (int)$rut;
        //$this->fetchRow devuelve fila donde RBD = $RBD
        $row = $this->fetchRow('rutApoderado = ' . $Rut);
        if (!$row) {
            return TRUE;
        } else {

            return FALSE;
        }


    }

    public function validarapoderado($rut)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('ap' => 'apoderados'))
            ->where('ap.rutApoderado = ?', $rut);
        $row = $this->fetchRow($select);
        if ($row) {
            return $row;
        } else {
            return false;
        }

    }

    public function listar()
    {
        //devuelve todos los registros de la tabla

        return $this->fetchAll();
    }
}
