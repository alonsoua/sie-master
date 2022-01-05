<?php

class Application_Form_Decretos extends Zend_Form
{

    protected $_est;

    public function setParams($est)
    {
        $this->_est = $est;
    }


    public function init()
    {


        $this->setName('Configuraciondecretos');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $iddecreto = new Zend_Form_Element_Hidden('idDecreto');


        $numero = new Zend_Form_Element_Text('numeroDecreto');
        $numero->setLabel('Decreto Plan de Estudio Nº:')->setRequired(true);


        $fecha = new Zend_Form_Element_Text('yearDecreto');
        $fecha->setAttrib('id', 'yearperiodo');
        $fecha->setAttrib('readonly', 'true');
        $fecha->setLabel('Año Decreto Plan de Estudio :')->setRequired(true);


        $numerop = new Zend_Form_Element_Text('numeroPlan');
        $numerop->setLabel('Promoción Escolar Decreto Exento Nº:')->setRequired(true);


        $fechap = new Zend_Form_Element_Text('yearPlan');
        $fechap->setAttrib('id', 'yearplan');
        $fechap->setAttrib('readonly', 'true');
        $fechap->setLabel('Año  Promoción Escolar Decreto Exento Nº :')->setRequired(true);


        //boton para enviar formulario
        $enviar = new Zend_Form_Element_Submit('submit');
        $enviar->setAttrib('rut', 'submitbutton', 'class', '');
        $enviar->removeDecorator('label');
        $enviar->setLabel('Guardar');


        $curso = new Zend_Form_Element_Multiselect('idCursos');

        $curso->setLabel('Lista de Cursos: ')->setRequired(true);

        $cursomodelo = new Application_Model_DbTable_Cursos();

        $rowsetcursos = $cursomodelo->getcursodecreto($this->_est, $idperiodo);

        foreach ($rowsetcursos as $row) {
            $curso->addMultiOption($row['idCursos'], $row['nombreGrado'] . ' ' . $row['letra']);

        }

        //agrego los objetos creados al formulario
        $this->addElements(array($iddecreto, $numero, $fecha, $numerop, $fechap, $curso, $enviar));

    }

}
