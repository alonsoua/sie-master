<?php

class Application_Form_AsistenciaSige extends Zend_Form
{

    public function init()
    {

        $periodo= new Zend_Session_Namespace('periodo');
        $idperiodo=$periodo->periodo;

        $usuario= new Zend_Session_Namespace('id');
        $user= $usuario->id;

        $nombreperiodo= new Zend_Session_Namespace('nombreperiodo');
        $nombreperiodos=$nombreperiodo->nombreperiodo;
        
        
        
        
        $this->setName('Notas');
        
         // form decorators
        $this->setDecorators(array(
        'FormElements',
        array('HtmlTag',array('tag' => 'table')),
        'Form'
        ));

        $cursos = new Zend_Form_Element_Select('idCursos');
        $cursos->setLabel('Curso: ')->setRequired(true);

        $modelocurso = new Application_Model_DbTable_Cursos();

        $rowset = $modelocurso->listaractual($idperiodo);
        $cursos->addMultiOption('null','Seleccione Curso');
        foreach ($rowset as $row) {
            $cursos->addMultiOption($row->idCursos, $row->nombreGrado.' '.$row->letra);
        }
        $cursos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

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

        $periodos = new Zend_Form_Element_Hidden('periodo');
        $periodos->setValue($nombreperiodos);
      
     
        
        //agrego los objetos creados al formulario
        $this->addElements(array($cursos,$fecha,$periodos));
        
    }

    


}

