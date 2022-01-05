<?php

class Application_Form_Evento extends Zend_Form
{

    protected $_idcurso;
    protected $_lista;

    public function __construct($options = null)
    {
        parent::__construct($options);
    }


    public function init()
    {


        $this->setName('Evento');

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        if ($rol == 1 || $rol == 3 || $rol == 6) {

            $idevento = new Zend_Form_Element_Hidden('idEvento');
            $nombre = new Zend_Form_Element_Text('nombreEvento');
            $nombre->setLabel('Nombre Evento: *')->setAttrib('maxlength', '30')->setRequired('true');
            $nombre->setAttrib('class', 'px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
            $nombre->setAttrib('placeholder', 'Nombre Evento');


            $fecha = new Zend_Form_Element_Text('fechaEvento');
            $fecha->setLabel('Fecha Evento: *')->setAttrib('maxlength', '10')->setRequired('true');
            $fecha->setAttrib('readonly', 'true');
            $fecha->setAttrib('class', 'pr-4 pl-10 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
            $fecha->setAttrib('autocomplete', 'off');


            $tipo = new Zend_Form_Element_Select('idTipoEvento');
            $tipo->setLabel('Tipo Evento *:')->setRequired(true);
            $tipo->setAttrib('class', 'mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm');


            $modeloperiodo = new Application_Model_DbTable_Periodo();

            $row = $modeloperiodo->getTipoEvento();
            foreach ($row as $e) {

                $tipo->addMultiOption($e['idTipoEvento'], $e['descripcionEvento']);
            }


            //boton para enviar formulario
            $enviar = new Zend_Form_Element_Button('submit');
            $enviar->setAttrib('class', 'bg-blue-500 flex justify-center items-center w-full text-white px-4 py-3 rounded-md focus:outline-none');
            $enviar->removeDecorator('label');
            $enviar->setLabel('Guardar');
            $enviar->setAttrib('type', 'submit');

            //boton volver
            $volver = new Zend_Form_Element_Button('volver');
            $volver->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Periodo', 'action' => 'evento')). '\' ');
            $volver->removeDecorator('label');
            $volver->setAttrib('class', 'flex justify-center items-center w-full text-gray-900 px-4 py-3 rounded-md focus:outline-none');
            $volver->setLabel('Volver');


            //agrego los objetos creados al formulario
            $this->addElements(array($idevento, $nombre, $fecha, $tipo, $enviar, $volver));


        }


    }

}
