<?php

class Application_Form_Cambiarperiodo extends Zend_Form
{

    public function init()
    {
        
         $this->setName('cambiarperiodo');  
      
      // form decorators
        $this->setDecorators(array(
        'FormElements',
        array('HtmlTag',array('tag' => 'table')),
        'Form'
        ));
        
        
        //creamos select para seleccionar establecimiento
        $periodo = new Zend_Form_Element_Select('periodo');
        $periodo->setLabel('Seleccione Periodo:')->setRequired(true);
        //cargo en un select los Peridos
        $table = new Application_Model_DbTable_Periodo();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista
       
        $periodo->addMultiOptions(array(
        "" => "Seleccione Periodo"   
    ));
        foreach ($table->listar() as $c)
        {
            
            
            $periodo->addMultiOption($c->idPeriodo,$c->nombrePeriodo);
        }
        
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('rut', 'submitbutton');
        $submit->setLabel('Guardar');
        $submit->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        
        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'index','action'=>'index'),null,TRUE).'\' ');
        $join->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
         //agrego los objetos creados al formulario
        $this->addElements(array($periodo,$submit,$join));
    }


}


