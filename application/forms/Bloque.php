<?php

class Application_Form_Bloque extends Zend_Form
{

    protected $_idcurso;
    protected $_lista;

    public function __construct($options = null)
    {
        parent::__construct($options);
    }


    public function init()
    {


        $this->setName('Bloque');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        $est = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $est->establecimiento;

        $idcalendario = new Zend_Form_Element_Hidden('idBloque');

//        //creamos select para seleccionar Establecimiento
        $establecimientoalumno = new Zend_Form_Element_Hidden('idEstablecimiento');
//        $establecimientoalumno->setLabel('Establecimiento *:')->setRequired(true);
//        $establecimientoalumno->setDecorators(array(
//            'ViewHelper',
//            'Errors',
//            'Label'
//
//        ));
//        $tablaestablecimiento = new Application_Model_DbTable_Establecimiento();
//
//
//        if ( $rol == 3 || $rol == 6) {
//
//            $rowestablecimientos = $tablaestablecimiento->get($establecimiento);
//            foreach ($rowestablecimientos as $e) {
//
//                $establecimientoalumno->addMultiOption($e['idEstablecimiento'], $e['nombreEstablecimiento']);
//            }
//        } else if($rol==1) {
//
//            $rowestablecimientos = $tablaestablecimiento->listar();
//            foreach ($rowestablecimientos as $e) {
//
//                $establecimientoalumno->addMultiOption($e->idEstablecimiento, $e->nombreEstablecimiento);
//            }
//        }
//        $establecimientoalumno->setDecorators(array(
//            'ViewHelper',
//            array('Errors'),
//            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
//            array('Label', array('tag' => 'td')),
//            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
//        ));


        $tiempoinicio = new Zend_Form_Element_Text('tiempoInicio');
        $tiempoinicio->setLabel('Hora Inicio: *')->setAttrib('maxlength', '5')->setRequired('true');
        $tiempoinicio->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $tiempotermino = new Zend_Form_Element_Text('tiempoTermino');
        $tiempotermino->setLabel('Hora Termino: *')->setAttrib('maxlength', '5')->setRequired('true');
        $tiempotermino->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $nombre = new Zend_Form_Element_Text('nombreBloque');
        $nombre->setLabel('Descripción Bloque: *')->setAttrib('maxlength', '60')->setRequired('true');
        $nombre->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $tipo = new Zend_Form_Element_Select('tipoBloque');
        $tipo->setLabel('Tipo de Jornada *:')->setRequired(true);
        $tipo->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $tipo->addMultiOption('1', 'Mañana');
        $tipo->addMultiOption('2', 'Tarde');
        $tipo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        //boton para enviar formulario
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


        //agrego los objetos creados al formulario
        $this->addElements(array($idcalendario, $establecimientoalumno, $nombre, $tiempoinicio, $tiempotermino,$tipo, $enviar));

    }

}
