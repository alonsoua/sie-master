<?php


/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 25-07-17
 * Time: 9:12 PM
 */


class Application_Form_AsistenciaGeneraAlumno extends Zend_Form
{

    public function init()
    {


        $est = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $est->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $this->setName('Cursos');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
        $this->setAttrib('target','_blank');

        $idcurso = new Zend_Form_Element_Select('idCursos');
        $idcurso->setLabel('Seleccione Curso:')->setRequired(true);
        $modelocursos = new Application_Model_DbTable_Cursos();
        if($rol==2){
            $rowcursos = $modelocursos->listarcursoiddocenteactual($user,$establecimiento,$idperiodo,0);
        }
        elseif($rol==3 || $rol==6){
            $rowcursos = $modelocursos->listartodasactual($establecimiento,$idperiodo);

        }elseif ($rol==1){
            $rowcursos = $modelocursos->listaractual($idperiodo);
        }

        foreach ($rowcursos as $e) {

            $idcurso->addMultiOption($e->idCursos, $e->nombreGrado." ".$e->letra);
        }
        $idcurso->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $idalumnos = new Zend_Form_Element_Select('idAlumnos');
        $idalumnos->setLabel('Seleccione Alumno:')->setRequired(true);

        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        if($rol==2){
            if(!empty($rowcursos[0]['idCursos'])){
                $rowalumnos = $modeloalumnos->listaralumnoscurso($rowcursos[0]['idCursos'],$idperiodo);
                if(!empty($rowalumnos)){
                    foreach ($rowalumnos as $e) {

                        $idalumnos->addMultiOption($e->idAlumnos, $e->apaterno." ".$e->amaterno." ".$e->nombres);
                    }
                }
            }


        }

        $idalumnos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $fechainicio= new Zend_Form_Element_Text('fechai');
        $fechainicio->setLabel('Fecha de Inicio:')->setRequired(true);
        $fechainicio->setAttrib('readonly','readonly');
        $fechainicio->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $fechatermino= new Zend_Form_Element_Text('fechat');
        $fechatermino->setLabel('Fecha de Termino:')->setRequired(true);
        $fechatermino->setAttrib('readonly','readonly');
        $fechatermino->setDecorators(array(
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
        $observacion->setLabel('Observacion:');
        $observacion->setAttrib('cols', '40')
            ->setAttrib('rows', '5');
        $observacion->setAttrib('id', 'observacion');



        //boton para enviar formulario pdf
        $pdf = new Zend_Form_Element_Submit('pdf');
        $pdf->setAttrib('class','button blue large');
        $pdf->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));





        //agrego los objetos creados al formulario
        $this->addElements(array($idcurso,$idalumnos, $fechainicio,$fechatermino,$pdf));

    }
}
