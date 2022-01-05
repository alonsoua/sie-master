<?php
defined('APPLICATION_UPLOADS_DIR')
    || define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/excel'));
class Application_Form_SubirAlumnos extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');
        // form decorators
        $this->setDecorators(array(
        'FormElements',
        array('HtmlTag',array('tag' => 'table')),
        'Form'
        ));
        
        
        
         //logo
        $archivo = new Zend_Form_Element_File('excel');
        $archivo->setRequired(true);
        $archivo->addValidator( 'Extension', false, 'xls,xlsx');
        $archivo->setLabel('Archivo (xlsx,xls):');
        
        $archivo->setDestination(APPLICATION_UPLOADS_DIR); 
       $archivo->setDecorators(array(
      'File',
      'Errors',
      array(array('data' => 'HtmlTag'), array('tag' => 'td')),
      array('Label', array('tag' => 'th')),
      array(array('row' => 'HtmlTag'), array('tag' => 'tr'))
));

       //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('RBD', 'submitbutton');
        $submit->setLabel('Enviar');
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
       
      
      

        //agregolos objetos creados al formulario
        $this->addElements(array($archivo,$submit,$join));
        
    
    
    }
}