<?php

class Application_Form_Director extends Zend_Form
{

    public function init()
    {


        $this->setName('Director');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        $idest = new Zend_Form_Element_Hidden('idEstablecimiento');


        //creamos select donde se carga la lista de niveles
        $id = new Zend_Form_Element_Select('idDirector');
        $id->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $id->setLabel('Seleccione Director: ')->setRequired(true)->setRegisterInArrayValidator(false);


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
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Establecimiento', 'action' => 'index'), null, TRUE) . '\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($idest, $id, $submit));
        $this->addElements(array($join));
    }


}

