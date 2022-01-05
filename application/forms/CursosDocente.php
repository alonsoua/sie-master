<?php

class Application_Form_CursosDocente extends Zend_Form
{

    public function init()
    {


        $this->setName('CursosDocente');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        $curso = new Zend_Form_Element_Hidden('idCursos');


        //creamos select donde se carga la lista de niveles
        $nivel = new Zend_Form_Element_Select('idCuentaJefe');
        $nivel->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $nivel->setLabel('Seleccione Docente: ')->setRequired(true)->setRegisterInArrayValidator(false);


        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('idcurso', 'submitbutton');
        $submit->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Cursos', 'action' => 'index'), null, TRUE) . '\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($curso, $nivel, $submit));
        $this->addElements(array($join));
    }


}

