<?php

class Application_Form_Periodo extends Zend_Form
{

    public function init()
    {
       $this->setName('periodo');

        //campo <input hidden> para guardar idPeriodo
        $idPeriodo = new Zend_Form_Element_Hidden('idPeriodo');
        $idPeriodo->addValidator('digits');
       

        //creamos <input text> para escribir fecha inicio periodo
        
        $fecha = new Zend_Form_Element_Text('nombrePeriodo');
        $fecha->setAttrib('id', 'nombreperiodo');
        $fecha->setAttrib('readonly', 'true');
        $fecha->setLabel('Año Periodo:')->setRequired(true);
        $fecha->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
              
      
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('idPeriodo', 'submitbutton');
        
        
        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'Periodo','action'=>'index'),null, TRUE).'\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($idPeriodo,$fecha,$submit));
        $this->addElements(array($join));
    }


}

