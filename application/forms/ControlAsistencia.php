<?php

class Application_Form_ControlAsistencia extends Zend_Form
{

    public function init()
    {

        //recuperamos el nivel del curso que esta en sesion
        $id_cursos = new Zend_Session_Namespace('idtablacurso');
        $id_curso = $id_cursos->idtablacurso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $nombreperiodo = new Zend_Session_Namespace('nombreperiodo');
        $nombreperiodos = $nombreperiodo->nombreperiodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;


        $this->setName('Notas');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));


        //campo <input hidden> para guardar Curso
        $curso = new Zend_Form_Element_Hidden('idCursos');
        $curso->setValue($id_curso);

        //campo <input hidden> para guardar Curso
        $periodos = new Zend_Form_Element_Hidden('idPeriodo');
        $periodos->setValue($nombreperiodos);

        $usuarios = new Zend_Form_Element_Hidden('idCuenta');
        $usuarios->setValue($user);


        //fecha de la prueba
        $fecha = new Zend_Form_Element_Text('fechaAsistencia');
        $fecha->setAttrib('readonly', 'true');

        $fecha->setLabel('Fecha Asistencia:')->setRequired(true);
        $fecha->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $bloque = new Zend_Form_Element_Multiselect('bloque');
        $bloque->setLabel('Periodo:')->setAttrib('data-placeholder','Seleccione Periodo')->setRegisterInArrayValidator(false);
        $bloque->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $idasignatura= new Zend_Form_Element_Select('idAsignatura');
        $idasignatura->setLabel('Asignatura:');
        $idasignatura->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $tipo= new Zend_Form_Element_Radio('tipo');
        $tipo->setLabel('Presencial/Online:');
        $tipo->setMultiOptions(array('1'=>'Presencial', '2'=>'Online'))
            ->setSeparator(' ')
            ->setAttrib('id','tipoClase')
            ->setValue("1");
        $tipo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $this->addElements(array($fecha,$idasignatura,$bloque,$tipo,$curso, $periodos, $usuarios));

    }


}

