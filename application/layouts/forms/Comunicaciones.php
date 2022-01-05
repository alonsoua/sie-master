<?php

class Application_Form_Comunicaciones extends Zend_Form
{

    public function init()
    {
       $this->setName('formcomunicaciones');
      // form decorators
        $this->setDecorators(array(
        'FormElements',
        array('HtmlTag',array('tag' => 'table')),
        'Form'
        ));
        
        
         //recuperamos el iddetallecursocuenta que esta en sesion
        $id_detalle_cursos= new Zend_Session_Namespace('id_detalle_curso');
        $id_detalle_curso= $id_detalle_cursos->id_detalle_curso;
        
        
        
        
         //recuperamos el nivel del curso que esta en sesion
        $id_cursos= new Zend_Session_Namespace('id_curso');
        $id_curso= $id_cursos->id_curso;


        
        //creamos <input text hidden> para curso
        $curso = new Zend_Form_Element_Hidden('idCursos');
        $curso->setValue($id_curso);
     
      
        
        
        //creamos <input text hidden> para curso
        $profesor = new Zend_Form_Element_Hidden('idCuenta');
        
        $tipocitacion=new Zend_Form_Element_Select('idTipo');
        
         $tipocitacion->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        $tipocitacion->setLabel('Tipo de Citación');
        $tipocitacion->addMultiOptions(array('1'=>'Reunión Apoderados' ,
                '2'=>'Citación Apoderado',
                '3'=>'Conducta',
                '4'=>'Información'
                ));
      
    
        
        
        
        
        
      
        $comunicacion = new Zend_Form_Element_Textarea('contenido');
        $comunicacion->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        $comunicacion->setLabel('Contenido:')->setRequired(true);
        $comunicacion->setAttrib('cols', '40')
                    ->setAttrib('rows', '5');
        $comunicacion->setAttrib('id', 'comunicacion');
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        
        $submit->setLabel('Guardar');
        
        
       
        
        
        $this->addElements(array($curso,$profesor,$tipocitacion,$comunicacion,$submit));
    }


}

