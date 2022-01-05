<?php

class Application_Model_DbTable_Alumnomedico extends Zend_Db_Table_Abstract
{

    protected $_name = 'alumnosClinico';
    protected $_primary = 'idAlumnosClinico';


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


    public function agregar($prevision, $letra, $actividad, $patologia, $profesional, $telefono, $hora, $documento, $persona, $telefonoe, $traslado, $personaretira, $telefonoretira, $rutretira, $tratamiento, $otrotratamiento, $entratamiento, $alergia, $idalumno)
    {
        $data = array('prevision' => $prevision, 'letra' => $letra, 'actividad' => $actividad, 'patologia' => $patologia, 'profesional' => $profesional, 'telefonoProfesional' => $telefono, 'horario' => $hora,
            'documento' => $documento, 'personaEmergencia' => $persona, 'telefonoEmergencia' => $telefonoe, 'trasladoEmergencia' => $traslado, 'idAlumnos' => $idalumno, 'personaRetira' => $personaretira, 'telefonoRetira' => $telefonoretira, 'rutRetira' => $rutretira, 'tratamiento' => $tratamiento, 'otroTratamiento' => $otrotratamiento, 'enTratamiento' => $entratamiento, 'alergia' => $alergia
        );

        $this->insert($data);

    }


    public function cambiar($prevision, $letra, $actividad, $patologia, $profesional, $telefono, $hora, $documento, $persona, $telefonoe, $traslado,$personaretira, $telefonoretira, $rutretira, $tratamiento, $otrotratamiento, $entratamiento, $alergia, $idalumno)
    {
        $data = array('prevision' => $prevision, 'letra' => $letra, 'actividad' => $actividad, 'patologia' => $patologia, 'profesional' => $profesional, 'telefonoProfesional' => $telefono, 'horario' => $hora,
            'documento' => $documento, 'personaEmergencia' => $persona, 'telefonoEmergencia' => $telefonoe, 'trasladoEmergencia' => $traslado,'personaRetira' => $personaretira, 'telefonoRetira' => $telefonoretira, 'rutRetira' => $rutretira, 'tratamiento' => $tratamiento, 'otroTratamiento' => $otrotratamiento, 'enTratamiento' => $entratamiento, 'alergia' => $alergia);
        $this->update($data, 'idAlumnos = ' . (int)$idalumno);
    }


    public function borrar($id)
    {
        $this->delete('idAlumnos =' . (int)$id);
    }

}

