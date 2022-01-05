<?php

class Application_Form_Configuracionelectivo extends Zend_Form
{

    protected $_idcurso;
    protected $_lista;

    public function __construct($options = null)
    {
        parent::__construct($options);
    }

    public function init()
    {


        $this->setName('Configuraciontaller');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));

        $periodo   = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelocurso = new Application_Model_DbTable_Cursos();
        $datoscurso = $modelocurso->listarcursoid($this->getAttrib('idcurso'),$idperiodo);

        $opciones=array(
            '1'=>'Primer Semestre',
            '2'=>'Segundo Semestre');

        if($datoscurso[0]['tipoModalidad']==2){
            $opciones=array(
                '3'=>'Primer Trimestre',
                '4'=>'Segundo Trimestre',
                '5'=>'Tercer Trimestre',
            );
        }



        $enviar = new Zend_Form_Element_Submit('submit');
        $enviar->setAttrib('rut', 'submitbutton', 'class', '');
        $enviar->setLabel('Guardar');


        $opsegmento= new Zend_Form_Element_Select('opsegmento');
        $opsegmento->setRegisterInArrayValidator(false);
        $opsegmento->setRequired(true);
        $opsegmento->addMultiOptions($opciones);

        $alumnos=new Zend_Form_Element_Multiselect('idAlumnos');
        $alumnosmodelo= new Application_Model_DbTable_Alumnosactual();

        $rowsetalumnos= $alumnosmodelo->listaralumnoscursoactual($this->getAttrib('idcurso'),$idperiodo);
        $r=1;
        foreach ($rowsetalumnos as $row) {
            $alumnos->addMultiOption($row['idAlumnos'], $r.'-'.$row['apaterno'].' '.$row['amaterno'].' '.$row['nombres']);
        $r++;
        }

        $this->addElements(array($opsegmento,$alumnos,$enviar));

    }

}
