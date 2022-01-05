<?php

class Application_Form_Notaspre extends Zend_Form
{

    public function init()
    {
        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));


        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        //recuperamos el iddetallecursocuenta que esta en sesion
        $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
        $idtevalucacion = $idtipoev->tipoevaluacion;


        //recuperamos el nivel del curso que esta en sesion
        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        //campo <input text> para guardar contenido
        $curso = new Zend_Form_Element_Hidden('idCursos');
        $curso->setValue($idcurso);

        //campo <input text> para guardar contenido
        $id = new Zend_Form_Element_Hidden('idEvaluacion');

        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();


        $alumnos = new Zend_Form_Element_Select('idAlumnos');
        $alumnos->setLabel('Seleccione Alumno: ');
        $alumnos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $rowsetalumnos = $modeloalumnos->listaralumnoscurso($idcurso, $idperiodo);

        foreach ($rowsetalumnos as $rowalumno) {
            $alumnos->addMultiOption($rowalumno->idAlumnos, $rowalumno->apaterno . ' ' . $rowalumno->amaterno . ' ' . $rowalumno->nombres);
        }


        if ($idtevalucacion == '1') {
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
            $tiponota->addMultiOptions(array('' => 'Seleccione Semestre', '1' => 'Primer Semestre', '2' => 'Segundo Semestre'));
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
            $tiponota->addMultiOptions(array('' => 'Seleccione Trimestre', '3' => 'Primer Trimestre', '4' => 'Segundo Trimestre', '5' => 'Tercer Trimestre'));
        }

        //fecha de la prueba
        $fecha = new Zend_Form_Element_Text('fecha');
        $fecha->setAttrib('readonly', 'true');

        $fecha->setLabel('Fecha Prueba:')->setRequired(true);
        $fecha->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $diast = new Zend_Form_Element_Text('diasTrabajado');
        $diast->setLabel('Dias Trabajados:')->setAttrib('maxlength', '3');;
        $diast->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $diasin = new Zend_Form_Element_Text('diasInasistencia');
        $diasin->setLabel('Dias Inasistencia:')->setAttrib('maxlength', '3');;
        $diasin->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $observaciones = new Zend_Form_Element_Textarea('observaciones');
        $observaciones->setLabel('Observación:');
        $observaciones->setAttrib('rows', '24');
        $observaciones->setAttrib('cols', '80');
        $observaciones->setAttrib('style', 'width: 298px; height: 80px; margin: 0px;');
        $observaciones->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        //fecha de la prueba
        $contenidos = new Zend_Form_Element_Text('conte');

        $contenidos->setLabel('Contenido:')->setRequired(true);
        $contenidos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $this->addElements(array($id, $curso, $alumnos, $tiponota, $diast, $diasin, $observaciones));


    }

}
