<?php

defined('APPLICATION_UPLOADS_DIR')
|| define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/guias'));


class Application_Form_Pruebaspre extends Zend_Form
{

    protected $_niveles;

    public function setParams($niveles)
    {
        $this->_niveles = $niveles;
    }

    public function init()
    {
        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        //recuperamos el iddetallecursocuenta que esta en sesion
        $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
        $idtevalucacion = $idtipoev->tipoevaluacion;


        //recuperamos el nivel del curso que esta en sesion
        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $curso = new Zend_Form_Element_Hidden('idCursos');
        $curso->setValue($id_curso);

        $id = new Zend_Form_Element_Hidden('idEvaluacion');


        $oa = new Zend_Form_Element_Select('oa');
        $oa->setLabel('Seleccione OA:')->setRequired(true);
        $oa->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $modeloasignatura= new Application_Model_DbTable_Asignaturascursos();
        $rowset      = $modeloasignatura->listaroapre($this->_niveles,$id_curso,$idperiodo);
        foreach ($rowset as $row) {
            $oa->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
        }

        $modelocurso = new Application_Model_DbTable_Cursos();

        $detalleest = $modelocurso->listarcursoid($id_curso, $idperiodo);


        $asignatura = new Zend_Form_Element_Hidden('idAsignatura');
        $asignatura->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos select para seleccionar tipo de nota
        $tiponota = new Zend_Form_Element_Select('tipoEvaluacionPrueba');
        $tiponota->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $tiponota->setLabel('Periodo Evaluación:')->setRequired(true);


        //fecha de la prueba
        $fecha = new Zend_Form_Element_Text('fecha');
        $fecha->setAttrib('readonly', 'true');

        $fecha->setLabel('Fecha Prueba:');
        $fecha->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));




        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setLabel('Crear');
        $submit->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $coef = new Zend_Form_Element_Select('coef');
        $coef->setLabel('Coeficiente:')->setRequired(true);
        $coef->addMultiOptions(array('1' => 'Coeficiente 1', '2' => 'Coeficiente 2'));
        $coef->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $publicar = new Zend_Form_Element_Checkbox('publicar', 'publicar',
            array('checkedValue' => 1, 'uncheckedValue' => 2));
        $publicar->setLabel('Publicar en APP:')->setValue(2);
        $publicar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        //Campos Nuevo Examen
        if ($detalleest[0]['examen'] == 1) {
            $categoria = new Zend_Form_Element_Radio('tipoNota');
            $categoria->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $categoria->setLabel('Tipo De Nota:')->setRequired(true);
            $categoria->addMultiOptions(array('1' => 'Nota', '2' => 'Exámen'))->setSeparator('')->setValue("1");


            $criterio = new Zend_Form_Element_Text('criterio');
            $criterio->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $criterio->setLabel('Promedio Menor a:')->setAttrib('maxlength', '2');


            $porcentaje = new Zend_Form_Element_Text('porcentajeExamen');
            $porcentaje->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $porcentaje->setLabel('% del Exámen:')->setAttrib('maxlength', '2');
            if ($idtevalucacion == '1') {


                $tiponota->addMultiOptions(array('1' => 'I Semestre', '2' => 'II Semestre', '6' => 'Final'));
            }

            if ($idtevalucacion == '2') {
                //creamos select para seleccionar tipo de nota
                $tiponota = new Zend_Form_Element_Select('tipoEvaluacionPrueba');
                $tiponota->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));
                $tiponota->setLabel('Periodo Evaluación:')->setRequired(true);
                $tiponota->addMultiOptions(array('3' => 'Primer Trimestre', '4' => 'Segundo Trimestre', '5' => 'Tercer Trimestre', '6' => 'Final'));
            }

            $this->addElements(array($id, $curso, $asignatura, $categoria, $oa, $tiponota, $fecha, $coef, $publicar, $criterio, $porcentaje, $submit));

        } else {
            if ($idtevalucacion == '1') {


                $tiponota->addMultiOptions(array('1' => 'Primer Semestre', '2' => 'Segundo Semestre'));
            }

            if ($idtevalucacion == '2') {
                //creamos select para seleccionar tipo de nota
                $tiponota = new Zend_Form_Element_Select('tipoEvaluacionPrueba');
                $tiponota->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));
                $tiponota->setLabel('Periodo Evaluación:')->setRequired(true);
                $tiponota->addMultiOptions(array('3' => 'Primer Trimestre', '4' => 'Segundo Trimestre', '5' => 'Tercer Trimestre'));
            }
            $this->addElements(array($id, $curso, $asignatura, $oa, $tiponota, $fecha, $coef, $publicar, $submit));

        }


    }
}





