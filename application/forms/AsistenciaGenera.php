<?php


/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 25-07-17
 * Time: 9:12 PM
 */


class Application_Form_AsistenciaGenera extends Zend_Form
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

        $idcurso = new Zend_Form_Element_Select('idCursos');
        $idcurso->setLabel('Seleccione Establecimiento:')->setRequired(true);
        $modelocursos = new Application_Model_DbTable_Cursos();
        if ($rol == 2) {
            $rowcursos = $modelocursos->listarcursoiddocenteactual($user, $establecimiento, $idperiodo, 0);
        } elseif ($rol == 3 || $rol == 6) {
            $rowcursos = $modelocursos->listartodasactual($establecimiento, $idperiodo);

        } elseif ($rol == 1) {
            $rowcursos = $modelocursos->listaractual($idperiodo);
        }

        foreach ($rowcursos as $e) {

            $idcurso->addMultiOption($e->idCursos, $e->nombreGrado . " " . $e->letra);
        }
        $idcurso->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $meses = new Zend_Form_Element_Select('meses');
        $meses->setLabel('Seleccione Mes')->setRequired(true);


        setlocale(LC_TIME, 'es_ES.UTF-8');
        for ($i = 1; $i <= 12; $i++) {
            // Create date object to store the DateTime format
            $dateObj = DateTime::createFromFormat('!m', $i);
            $numeromes = strftime("%m", $dateObj->getTimestamp());
            $nombremes = strftime("%B", $dateObj->getTimestamp());
            $meses->addMultiOption($numeromes, $nombremes);
            //$month_options .= '<option value="' . esc_attr( $month_num ) . '">' . $month_name . '</option>';
        }
        //return '<select name="' . $field_name . '">' . $month_options . '</select>';

        $meses->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //boton para enviar formulario pdf
        $pdf = new Zend_Form_Element_Submit('pdf');
        $pdf->setAttrib('class', 'button red large');
        $pdf->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $excel = new Zend_Form_Element_Submit('excel');
        $excel->setAttrib('class', 'button green large');
        $excel->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        //agrego los objetos creados al formulario
        $this->addElements(array($idcurso, $meses, $pdf, $excel));

    }
}
