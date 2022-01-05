<?php

class Application_Form_NuevaEntrevista extends Zend_Form
{

    public function init()
    {


        //recuperamos el nivel del curso que esta en sesion
        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        $this->setName('formobs');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        $idobservacion = new Zend_Form_Element_Hidden('idEntrevista');
        $ida = new Zend_Form_Element_Hidden('ida');


        //creamos select para seleccionar Establecimiento
        $alumnos = new Zend_Form_Element_Text('Alumnos');
        $alumnos->setLabel('Alumno  :')->setRequired(true);
        $alumnos->setAttrib('style', 'width:39.5%');
        $alumnos->setAttrib('readonly', 'readonly');
        $alumnos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $cryptor = new \Chirp\Cryptor();


        $asignatura = new Zend_Form_Element_Select('idAsignatura');
        $asignatura->setRequired(true);
        $asignatura->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $asignatura->setLabel('Seleccione Asignatura: ');
        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
        $modelocurso = new Application_Model_DbTable_Cursos();
        $tipos = array(1, 2, 3, 4, 5, 6);
        $rowsetasignatura = $asignaturamodel->listarniveltipos($id_curso, $idperiodo, $tipos);


        if ($rol == 1 || $rol == 3 || $rol == 6) {
            //$opciones['Oficina'] = array("D" => "Dirección", "I" => "Inspectoria", "R" => "Recreo");


            foreach ($rowsetasignatura as $row) {
                $opciones['Asignaturas'][$row->idAsignatura] = $row->nombreAsignatura;

            }

        } elseif ($rol == 2) {
            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $rowa = $modelocurso->getasignaturashorario($id_curso, $idperiodo, $user);

            //$opciones['Oficina'] = array("R" => "Recreo");
            foreach ($rowa as $row) {
                $opciones['Asignaturas'][$row['idAsignatura']] = $row['nombreAsignatura'];

            }


        }


        $asignatura->setMultiOptions($opciones);


        $fecha = new Zend_Form_Element_Text('fechaEntrevista');
        $fecha->setLabel('Fecha Entrevista:');
        $fecha->setRequired(true);
        $fecha->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $observacion = new Zend_Form_Element_Textarea('entrevista');
        $observacion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $observacion->setLabel('Entrevista:')->setRequired(true);
        $observacion->setAttrib('cols', '40')
            ->setAttrib('rows', '5');
        $observacion->setAttrib('id', 'observacion');


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
        $submit->setLabel('Guardar');
        $submit->setAttrib('id', 'guardarentrevista');

        $token = new Zend_Form_Element_Text('token');
        //$token->setLabel('Firma :')->setRequired(true)->setValidators('Digits');
        $token->setLabel('Firma :')->addValidator('Digits');
        $token->setAttrib('style', 'width:39.5%');
        $token->setAttrib('maxlength', '6');
        $token->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $this->addElements(array($ida, $idobservacion, $alumnos, $asignatura, $fecha, $observacion, $token, $submit));
    }


}

