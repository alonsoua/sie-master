<?php

class Application_Form_Asistencia extends Zend_Form
{

    public function init()
    {
        
        //recuperamos el iddetallecursocuenta que esta en sesion
        $id_detalle_cursos= new Zend_Session_Namespace('id_detalle_curso');
        $id_detalle_curso= $id_detalle_cursos->id_detalle_curso;
        
         //recuperamos el nivel del curso que esta en sesion
        $id_nivel_cursos= new Zend_Session_Namespace('nivel_curso');
        $id_nivel_curso= $id_nivel_cursos->nivel_curso;
        
         //recuperamos el nivel del curso que esta en sesion
        $id_cursos= new Zend_Session_Namespace('idtablacurso');
        $id_curso= $id_cursos->idtablacurso;
        
        $periodo= new Zend_Session_Namespace('periodo');
        $idperiodo=$periodo->periodo;
        
        $nombreperiodo= new Zend_Session_Namespace('nombreperiodo');
        $nombreperiodos=$nombreperiodo->nombreperiodo;
        
        $usuario= new Zend_Session_Namespace('id');
     
        $user= $usuario->id;
        
        
        
        
        $this->setName('Notas');
        
         // form decorators
        $this->setDecorators(array(
        'FormElements',
        array('HtmlTag',array('tag' => 'table')),
        'Form'
        ));

      
      
      
        
        //campo <input hidden> para guardar Curso
        $curso = new Zend_Form_Element_Hidden('Cursos_idCursos');
        $curso->setValue($id_curso);
        
        //campo <input hidden> para guardar Curso
        $cursodetalle = new Zend_Form_Element_Hidden('Cursodetalle');
        $cursodetalle->setValue($id_detalle_curso);
         //campo <input hidden> para guardar Curso
        $periodos = new Zend_Form_Element_Hidden('periodo');
        $periodos->setValue($nombreperiodos);
        
        $usuarios = new Zend_Form_Element_Hidden('usuario');
        $usuarios->setValue($user);
        
        
        
       //fecha de la prueba
        $fecha = new Zend_Form_Element_Text('fecha');
        $fecha->setAttrib('readonly', 'true');
        
        $fecha->setLabel('Fecha Asistencia:')->setRequired(true);
         $fecha->setDecorators(array(
        'ViewHelper',
        array('Errors'),
        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
        array('Label', array('tag' => 'td')),
        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
      
     
        
        //agrego los objetos creados al formulario
        $this->addElements(array($fecha,$curso,$cursodetalle,$periodos,$usuarios));
        
    }

    


}

