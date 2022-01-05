<?php

class Application_Form_Calendario extends Zend_Form
{


    protected $_id;

    public function setParams($id)
    {
        $this->_id = $id;
    }

    protected $_idcurso;
    protected $_lista;

    public function __construct($options = null)
    {
        parent::__construct($options);
    }


    public function init()
    {


        $this->setName('Calendario');

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $est = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $est->establecimiento;

        $urlboton = $this->getView()->url(array('controller' => 'Periodo', 'action' => 'calendario'), null, true);

        $idcalendario = new Zend_Form_Element_Hidden('idCalendario');

        $establecimientoalumno = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimientoalumno->setLabel('Establecimiento *:');
        $establecimientoalumno->setAttrib('class', 'mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm');

        $tablaestablecimiento = new Application_Model_DbTable_Establecimiento();


        if ($rol == 3 || $rol == 6) {

            $rowestablecimientos = $tablaestablecimiento->get($establecimiento);
            foreach ($rowestablecimientos as $e) {

                $establecimientoalumno->addMultiOption($e['idEstablecimiento'], $e['nombreEstablecimiento']);
            }
        } else if ($rol == 1) {

            $rowestablecimientos = $tablaestablecimiento->listar();
            foreach ($rowestablecimientos as $e) {

                $establecimientoalumno->addMultiOption($e->idEstablecimiento, $e->nombreEstablecimiento);
            }
        }

        if ($this->_id > 0) {

            $establecimientoalumno->setValue($this->_id);
            $establecimientoalumno->setAttrib('disabled', 'disabled');

            $curso = new Zend_Form_Element_Select('idCursos');
            $curso->setLabel('Establecimiento *:')->setRequired(true);
            $curso->setAttrib('class', 'mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm');
            $modelocurso = new Application_Model_DbTable_Cursos();


            $row = $modelocurso->listarcursoestablecimiento($this->_id, $idperiodo);

            foreach ($row as $e) {
                $curso->addMultiOption($e['idCursos'], $e['nombreGrado'] . " " . $e['letra']);
            }

            $urlboton = $this->getView()->url(array('controller' => 'Periodo', 'action' => 'calendariocurso','id'=>$this->_id), null, true);

        }


        $codigo = new Zend_Form_Element_Text('codigoCalendario');
        $codigo->setLabel('Código Calendario: *')->setAttrib('maxlength', '30')->setRequired('true');
        $codigo->setAttrib('class', 'px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
        $codigo->setAttrib('placeholder', 'Codigo Calendario');


        $descripcion = new Zend_Form_Element_Text('descripcionCalendario');
        $descripcion->setLabel('Descripción Calendario: *')->setAttrib('maxlength', '60')->setRequired('true');
        $descripcion->setAttrib('class', 'px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
        $descripcion->setAttrib('placeholder', 'Descripción Calendario');


        $fechainicio = new Zend_Form_Element_Text('fechaInicioClase');
        $fechainicio->setLabel('Fecha Inicio Clases: *')->setAttrib('maxlength', '10')->setRequired('true');
        $fechainicio->setAttrib('class', 'pr-4 pl-10 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
        $fechainicio->setAttrib('autocomplete', 'off');


        $fechatermino = new Zend_Form_Element_Text('fechaTerminoClase');
        $fechatermino->setLabel('Fecha Termino Clases: *')->setAttrib('maxlength', '10')->setRequired('true');
        $fechatermino->setAttrib('class', 'pr-4 pl-10 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
        $fechatermino->setAttrib('autocomplete', 'off');

        $enviar = new Zend_Form_Element_Button('submit');
        $enviar->setAttrib('class', 'bg-blue-500 flex justify-center items-center w-full text-white px-4 py-3 rounded-md focus:outline-none');
        $enviar->removeDecorator('label');
        $enviar->setLabel('Guardar');
        $enviar->setAttrib('type', 'submit');

        $volver = new Zend_Form_Element_Button('volver');
        $volver->setAttrib('onclick', 'window.location =\'' . $urlboton . '\' ');
        $volver->removeDecorator('label');
        $volver->setAttrib('class', 'flex justify-center items-center w-full text-gray-900 px-4 py-3 rounded-md focus:outline-none');
        $volver->setLabel('Volver');


        if ($this->_id > 0) {
            $this->addElements(array($idcalendario, $establecimientoalumno, $curso, $codigo, $descripcion, $fechainicio, $fechatermino, $enviar, $volver));

        } else {
            $this->addElements(array($idcalendario, $establecimientoalumno, $codigo, $descripcion, $fechainicio, $fechatermino, $enviar, $volver));
        }


    }

}
