<?php

class Application_Form_Horario extends Zend_Form
{

    protected $_id;

    public function setParams($id)
    {
        $this->_id = $id;
    }

    public function init()
    {

        $this->setName('Horario');
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $id = new Zend_Form_Element_Hidden('idHorario');

        //boton para enviar formulario
        $enviar = new Zend_Form_Element_Submit('submit');
        $enviar->setAttrib('class', 'button small blue');
        $enviar->removeDecorator('label');
        $enviar->setLabel('Guardar');

        $eliminar = new Zend_Form_Element_Submit('eliminar');
        $eliminar->setAttrib('class', 'button small red');
        $eliminar->removeDecorator('label');
        $eliminar->setLabel('Eliminar');


        $bloque = new Zend_Form_Element_Text('bloque');
        $bloque->setAttrib('maxlength', '2');
        $bloque->setAttrib('size', '4');
        $bloque->setAttrib('name', 'bloque[]');


        $dia = new Zend_Form_Element_Text('dia');
        $dia->setLabel(' Valor:');
        $dia->setAttrib('maxlength', '2');
        $dia->setAttrib('size', '4');
        $dia->setAttrib('name', 'dia[]');

        $asignatura = new Zend_Form_Element_Select('idAsignatura');
        $asignatura->setRequired(true);
        $asignatura->setLabel('Seleccione Asignatura: ');
        $asignatura->addMultiOptions(array(
            "" => "Seleccione Asignatura"
        ));

        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
        $tipos = array(1, 2, 3, 4, 5, 6);
        $rowsetasignatura = $asignaturamodel->listarniveltipos($this->_id, $idperiodo, $tipos);

        foreach ($rowsetasignatura as $row) {
            if($row->horas>$row->horasAsignadas){
                $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura.' ('.($row->horas-$row->horasAsignadas).')');
            }

        }
        $asignatura->setDecorators(array('ViewHelper'));

        $curso = new Application_Model_DbTable_Cursos();
        $cursos = $curso->getnombreactual($this->_id);

        $modelocuenta = new Application_Model_DbTable_Cuentas();
        $cuentas = $modelocuenta->listarjefeperiodo($cursos[0]['idEstablecimiento'],$idperiodo);

        $cuenta= new Zend_Form_Element_Select('idCuenta');
        $cuenta->setRequired(true);
        $cuenta->setLabel('Seleccione Docente: ');
        $cuenta->addMultiOptions(array(
            "" => "Seleccione Docente"
        ));

        //foreach ($cuentas as $row) {
            $cuenta->addMultiOptions($cuentas);
        //}
        $cuenta->setDecorators(array('ViewHelper'));



        //agrego los objetos creados al formulario
        $this->addElements(array($id, $asignatura,$cuenta, $enviar,$eliminar));

    }

}
