<?php

class Application_Form_Comunicacionesenviar extends Zend_Form
{

    public function init()
    {

         $periodo= new Zend_Session_Namespace('periodo');
       $idperiodo=$periodo->periodo;
       
         $this->setName('formcomunicacionesenviar');
      // form decorators
        $this->setDecorators(array(
        'FormElements',
        array('HtmlTag',array('tag' => 'table')),
        'Form'
        ));

// element decorators
        $id_cursos= new Zend_Session_Namespace('id_curso');
        $id_curso= $id_cursos->id_curso;
        
        $idcomunicacion=new Zend_Form_Element_Hidden('idComunicacion');
        
         //creamos select donde se carga la lista de alumnos
      $alumnosSelect = new Zend_Form_Element_Select('Rut_alumnocomunicacion');
      $alumnosSelect->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
      $alumnosSelect->setLabel('Seleccione Alumno(s): ');
     
   
      $alumnosSelect->setAttrib('id', 'itemalumnos');
      $alumnosSelect->setAttrib('class', 'multiselect');
      $alumnosSelect->setAttrib('multiple', 'TRUE')->setRequired()->setRegisterInArrayValidator(false);
     
      $alumnosModel = new Application_Model_DbTable_Alumnosactual();
      $rowsetalumno = $alumnosModel->listaralumnoscurso($id_curso,$idperiodo);
      foreach($rowsetalumno as $row1){
          
          
      $alumnosSelect->addMultiOption($row1->idAlumnos,$row1->nombres.' '.$row1->apaterno.' '.$row1->amaterno);
      }
        
        //creamos <input text hidden> para curso
        $curso = new Zend_Form_Element_Hidden('curso');
        $curso->setValue($id_curso);
         
      
        $comunicacion = new Zend_Form_Element_Textarea('contenido');
        $comunicacion->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        $comunicacion->setLabel('Comunicacion:');
        $comunicacion->setAttrib('cols', '40')
                    ->setAttrib('rows', '5')
                    ->setAttrib('disabled', 'disabled');
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
        
        $submit->setAttrib('Rut', 'submitbutton');
        $submit->setLabel('Generar');
        $submit->setAttrib('id', 'guardarcomunicacion');
        
        
        $fecha=new Zend_Form_Element_Text('fechaenvio');
        $fecha->setLabel('Fecha y Hora:')->setRequired(true);
        $fecha->addValidator(new Zend_Validate_Date(array('format' => 'dd-mm-yyyy')));
        
       
        $fecha->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        
        $asunto=new Zend_Form_Element_Text('asunto');
        $asunto->setLabel('Asunto (Se visualizará en el asunto del correo):')->setRequired(true);
        
        
       
        $asunto->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        
        
        
        
        
        $this->addElements(array($idcomunicacion,$comunicacion,$alumnosSelect,$curso,$fecha,$asunto,$submit));
    }


}

