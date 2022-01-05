<?php

class Application_Form_Cambiarestado extends Zend_Form
{

    public function init()
    {
        
         $this->setName('cambiarestado');  
      
      // form decorators
        $this->setDecorators(array(
        'FormElements',
        array('HtmlTag',array('tag' => 'table')),
        'Form'
        ));

        $id=new Zend_Form_Element_Hidden('idAlumnosActual');

       //creamos <input text> para escribir nombres
        $nombres = new Zend_Form_Element_Text('nombres');
        $nombres->setLabel('Nombres:');
     
      

        
        //creamos <input text> para escribir Apellido paterno
        $apaterno = new Zend_Form_Element_Text('apaterno');
        $apaterno->setLabel('Apellido Paterno:');
     
      
        //creamos <input text> para escribir Aplliedo Materno
        $amaterno = new Zend_Form_Element_Text('amaterno');
        $amaterno->setLabel('Apellido Materno:');


          //creamos select provincia sostenedor
      $estadoselect = new Zend_Form_Element_Select('idEstado');
      $estadoselect->setLabel('Estado Alumno: ')->setRequired(true);
      
     
      
      
     $estadomodel = new Application_Model_DbTable_Estado();
     $rowset = $estadomodel->listar();
      foreach($rowset as $row){
         $estadoselect->addMultiOption($row->idEstado, $row->nombreEstado);
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
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'Alumnos','action'=>'index'),null,TRUE).'\' ');
        $join->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
         //agrego los objetos creados al formulario
        $this->addElements(array($id,$nombres,$apaterno,$amaterno,$estadoselect,$submit,$join));
    }


}


