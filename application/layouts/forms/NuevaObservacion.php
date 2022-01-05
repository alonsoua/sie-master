<?php

class Application_Form_NuevaObservacion extends Zend_Form
{

    public function init()
    {


        //recuperamos el nivel del curso que esta en sesion
        $nivelcurso = new Zend_Session_Namespace('nivel_curso');
        $nivel_curso = $nivelcurso->nivel_curso;

        $ingreson = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;

        //recuperamos el iddetallecursocuenta que esta en sesion
        $id_detalle_cursos = new Zend_Session_Namespace('id_detalle_curso');
        $id_detalle_curso = $id_detalle_cursos->id_detalle_curso;

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

        $idobservacion = new Zend_Form_Element_Hidden('idObservaciones');
        $ida = new Zend_Form_Element_Hidden('ida');


        //creamos select para seleccionar Establecimiento
        $alumnos = new Zend_Form_Element_Multiselect('idAlumnos');
        $alumnos->setLabel('Alumnos  :')->setRequired(true);
        $alumnos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $alumnos->setAttrib("class", "form-control");
        $alumnos->setAttrib('id', 'idAlumnos')->setRegisterInArrayValidator(false);
        $cryptor = new \Chirp\Cryptor();
        $modeloalumno = new Application_Model_DbTable_Alumnosactual();

        if ($rol == 1 || $rol == 2 || $rol == 3 || $rol == 6) {

            $rowalumno = $modeloalumno->listaralumnoscursoactual($id_curso, $idperiodo);
            $alumnos->addMultiOption("allc", "Todo el Curso");
            $r = 1;


            foreach ($rowalumno as $e) {

                $alumnos->addMultiOption($cryptor->encrypt($e['idAlumnos']), $r . ' | ' . $e['nombres'] . ' ' . $e['apaterno'] . ' ' . $e['amaterno']);
                $r++;
            }
        }


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
            $opciones['Oficina'] = array("D" => "Dirección", "I" => "Inspectoria", "R" => "Recreo");


            foreach ($rowsetasignatura as $row) {
                $opciones['Asignaturas'][$row->idAsignatura] = $row->nombreAsignatura;

            }

        } elseif ($rol == 2) {
            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $rowa = $modelocurso->getasignaturashorario($id_curso, $idperiodo, $user);

            $opciones['Oficina'] = array("R" => "Recreo");
            foreach ($rowa as $row) {
                $opciones['Asignaturas'][$row['idAsignatura']] = $row['nombreAsignatura'];

            }


        }


        $asignatura->setMultiOptions($opciones);


        $radios = new Zend_Form_Element_Radio("idTipo");
        $radios->setLabel('Tipo de Observación:')
            ->addMultiOptions(array(
                '1' => 'Positiva',
                '2' => 'Negativa'
            ))
            ->setSeparator(' ')->setValue(1);

        $radios->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $fecha = new Zend_Form_Element_Text('fechaObservacion');
        $fecha->setLabel('Fecha Observación');
        $fecha->setRequired(true);
        $fecha->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $observacion = new Zend_Form_Element_Textarea('observacion');
        $observacion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $observacion->setLabel('Observacion:')->setRequired(true);
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
        $submit->setAttrib('id', 'guardarobservaciones');


        $this->addElements(array($ida, $idobservacion, $alumnos, $asignatura, $fecha, $observacion, $submit));
    }


}

