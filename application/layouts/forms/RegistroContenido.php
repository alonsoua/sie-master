<?php

class Application_Form_RegistroContenido extends Zend_Form
{

    public function init()
    {

        $this->setName('Contenido');


        $idcontenido= new Zend_Form_Element_Hidden('idControlContenidoDetalle');
        $token= new Zend_Form_Element_Hidden('token');



        $fecha = new Zend_Form_Element_Text('fechaContenido');
        $fecha->setAttrib('readonly', 'true');

        $fecha->setLabel('Fecha Contenido:')->setRequired(true);
        $fecha->setAttrib('readonly', 'true');
        $fecha->setAttrib('class', 'pr-4 pl-10 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
        $fecha->setAttrib('autocomplete', 'off');

        $contenido = new Zend_Form_Element_Text('contenidos');
        $contenido->setLabel('Contenido:')->setRequired(true)->setAttrib('maxlength','200')->setAttrib('style','width:100%');
        $contenido->setAttrib('class', 'px-4 py-2 border focus:ring-gray-500 focus:border-gray-900 w-full sm:text-sm border-gray-300 rounded-md focus:outline-none text-gray-600');
        $contenido->setAttrib('placeholder', 'Contenido');



        $bloque = new Zend_Form_Element_Multiselect('bloque');
        $bloque->setLabel('Periodo:')->setAttrib('data-placeholder','Seleccione Periodo')->setRegisterInArrayValidator(false);
        $bloque->setAttrib('class', 'mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm');


        $idasignatura= new Zend_Form_Element_Select('idAsignatura');
        $idasignatura->setLabel('Asignatura:')->setRequired(true)->setRegisterInArrayValidator(false);
        $idasignatura->setAttrib('class', 'mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm');



        $enviar = new Zend_Form_Element_Submit('enviar');
        $enviar->setAttrib('class', 'button small blue');
        $enviar->removeDecorator('label');
        $enviar->setLabel('Guardar');

        //agrego los objetos creados al formulario
        $this->addElements(array($token,$idcontenido,$fecha,$contenido,$idasignatura,$bloque,$enviar));

    }


}

