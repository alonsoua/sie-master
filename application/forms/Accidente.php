<?php


/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 25-07-17
 * Time: 9:12 PM
 */


class Application_Form_Accidente extends Zend_Form
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
        $idcurso->setLabel('Seleccione Curso: *');
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
        $idalumnos->setLabel('Seleccione Alumno: *');

        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
            if(!empty($rowcursos[0]['idCursos'])){
                $rowalumnos = $modeloalumnos->listaralumnoscurso($rowcursos[0]['idCursos'],$idperiodo);
                if(!empty($rowalumnos)){
                    foreach ($rowalumnos as $e) {

                        $idalumnos->addMultiOption($e->idAlumnos, $e->apaterno." ".$e->amaterno." ".$e->nombres);
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


        $calle= new Zend_Form_Element_Text('calle');
        $calle->setLabel('Calle: *');
        $calle->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $numerocasa= new Zend_Form_Element_Text('numeroCasa');
        $numerocasa->setLabel('Número: *');
        $numerocasa->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $numerocasa->setAttrib('class','numero');

        $villa= new Zend_Form_Element_Text('villa');
        $villa->setLabel('Villa: *');
        $villa->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $ciudad= new Zend_Form_Element_Text('ciudadActual');
        $ciudad->setLabel('Ciudad: *');
        $ciudad->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir fecha inscripcionç
        $date = new DateTime;
        $fechaactual = $date->format('d-m-Y');
        $horarioactual=$date->format('H:i');


        $fechaaccidente= new Zend_Form_Element_Text('fechaAccidente');
        $fechaaccidente->setLabel('Fecha de Accidente: *');
        $fechaaccidente->setAttrib('readonly','readonly');
        $fechaaccidente->setValue($fechaactual);
        $fechaaccidente->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $horarioaccidente= new Zend_Form_Element_Text('horarioAccidente');
        $horarioaccidente->setLabel('Horario de Accidente: *');
        $horarioaccidente->setAttrib('maxlength','5');
        $horarioaccidente->setValue($horarioactual);
        $horarioaccidente->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $horarioaccidente->setAttrib('class','numero');

        $ubicacionaccidente = new Zend_Form_Element_Select('ubicacionAccidente');
        $ubicacionaccidente->setLabel('Donde ocurrió el accidente: *');
        $ubicacionaccidente->addMultiOption("1", "De Trayecto");
        $ubicacionaccidente->addMultiOption("2", "En la Escuela");
        $ubicacionaccidente->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $testigo= new Zend_Form_Element_Text('nombreTestigo');
        $testigo->setLabel('Nombre Testigo 1:');
        $testigo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $ruttestigo= new Zend_Form_Element_Text('rutTestigo');
        $ruttestigo->setLabel('Rut testigo 1:');
        $ruttestigo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $testigodos= new Zend_Form_Element_Text('nombreTestigodos');
        $testigodos->setLabel('Nombre Testigo 2:');
        $testigodos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $ruttestigodos= new Zend_Form_Element_Text('rutTestigodos');
        $ruttestigodos->setLabel('Rut testigo 2:');
        $ruttestigodos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));




        $observacion = new Zend_Form_Element_Textarea('descripcionAccidente');
        $observacion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $observacion->setLabel('CIRCUNSTANCIAS DEL ACCIDENTE (DESCRIBA COMO OCURRIO-CAUSAL): *');
        $observacion->setAttrib('cols', '40')->setAttrib('rows', '8');


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
        $this->addElements(array($idcurso,$idalumnos,$calle,$numerocasa,$villa,$ciudad,$fechaaccidente,$horarioaccidente,$ubicacionaccidente,$testigo,$ruttestigo,$testigodos,$ruttestigodos,$observacion,$pdf));

    }
}
