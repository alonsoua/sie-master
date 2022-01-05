<?php

class Application_Form_Configuraciontaller extends Zend_Form
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

        $porcentaje = new Zend_Form_Element_Text('porcentaje');
        $porcentaje->setLabel('Porcentaje %: ')->addValidator('digits')->setAttrib('maxlength', '2');
        $porcentaje->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $forma = new Zend_Form_Element_Select('forma');
        $forma->setLabel('Taller se Transforma en: ');
        $forma->addMultiOption("1", "Ultima Nota");
        $forma->addMultiOption("2", "Porcentaje");
        $forma->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $enviar = new Zend_Form_Element_Submit('submit');
        $enviar->setAttrib('rut', 'submitbutton', 'class', '');
        $enviar->removeDecorator('label');
        $enviar->setLabel('Guardar');
        $enviar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $asignatura = new Zend_Form_Element_Select('idAsignaturaTaller');
        $asignatura->setRequired(true);
        $asignatura->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $asignatura->setLabel('Asignatura a la que va el taller: ');

        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
        $tipos           = array('1', '3','4');

        $rowsetasignatura = $asignaturamodel->listarniveltipos($this->getAttrib('idcurso'), $idperiodo, $tipos);


        foreach ($rowsetasignatura as $row) {
            $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
        }

        $segmento= new Zend_Form_Element_Select('segmento');
        $segmento->setRequired(true);
        $segmento->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $segmento->setLabel('Periodo: ');
        $segmento->addMultiOption('1', 'Anual');
        $segmento->addMultiOption('2', 'Semestral');
        $segmento->addMultiOption('3', 'Trimestral');


        $opsegmento= new Zend_Form_Element_Select('opsegmento');
        $opsegmento->setRegisterInArrayValidator(false);
        $opsegmento->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $opsegmento->setLabel('Opción Periodo: ');

        $tipo= new Zend_Form_Element_Select('tipo');
        $tipo->setRequired(true);
        $tipo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $tipo->setLabel('Opción: ');
        $tipo->addMultiOption('1', 'Todo el Curso');
        $tipo->addMultiOption('2', 'Alumnos');

        $alumnos=new Zend_Form_Element_Multiselect('idAlumnos');
        $alumnos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $alumnos->setLabel('Lista de Alumnos: ');

        $alumnosmodelo= new Application_Model_DbTable_Alumnosactual();

        $rowsetalumnos= $alumnosmodelo->listaralumnoscursoactualnot($this->getAttrib('idcurso'),$idperiodo,$this->getAttrib('lista'));
        $r=1;
        foreach ($rowsetalumnos as $row) {
            $alumnos->addMultiOption($row['idAlumnos'], $r.'-'.$row['apaterno'].' '.$row['amaterno'].' '.$row['nombres']);
        $r++;
        }

        $this->addElements(array( $forma, $porcentaje, $asignatura,$segmento,$opsegmento,$tipo,$alumnos,$enviar));

    }

}
