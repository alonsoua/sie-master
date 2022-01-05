<?php

class Application_Form_Configuraciondep extends Zend_Form
{

    protected $_idcurso;
    protected $_nombre;

    public function setParams($idcurso)
    {
        $this->_idcurso = $idcurso;

    }
    public function setNombre($nombre)
    {

        $this->_nombre = $nombre;
    }
    public function init()
    {

        $this->setName('Configuraciondep');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));

        $periodo   = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        //campo <input hidden> para guardar Curso
        $id = new Zend_Form_Element_Hidden('idConfiguracionDependencia');

        //boton para enviar formulario
        $enviar = new Zend_Form_Element_Submit('submit');
        $enviar->setAttrib('rut', 'submitbutton', 'class', '');
        $enviar->removeDecorator('label');
        $enviar->setLabel('Guardar');
        $enviar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $asignatura = new Zend_Form_Element_Multiselect('asignaturas');
        $asignatura->setRequired(true);
        $asignatura->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $asignatura->setLabel('Asignaturas que componen a ' . $this->_nombre . ' : ');

        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
        $tipos           = array('1');

        $rowsetasignatura = $asignaturamodel->listarniveltipos($this->_idcurso, $idperiodo, $tipos);

        foreach ($rowsetasignatura as $row) {
            $asignatura->addMultiOption($row->idAsignaturaCurso, $row->nombreAsignatura);
        }

        //agrego los objetos creados al formulario
        $this->addElements(array($id, $asignatura, $enviar));

    }

}
