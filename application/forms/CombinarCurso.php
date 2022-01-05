<?php

class Application_Form_CombinarCurso extends Zend_Form
{

    public function init()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $this->setName('CombinarCurso');

        $establecimiento = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimiento->setRequired(true);
        $establecimiento->setAttrib('class','mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm');
        $modeloestablecimiento = new Application_Model_DbTable_Establecimiento();
        $establecimiento->addMultiOption("Null", "Seleccione Establecimiento");

        if ($rol == '4' || $rol == '3' || $rol == '6') {
            foreach ($modeloestablecimiento->listarestablecimiento($establecimiento) as $item) {
                $establecimiento->addMultiOption($item->idEstablecimiento, $item->nombreEstablecimiento);
            }
        }

        if ($rol == '1') {
            foreach ($modeloestablecimiento->listar() as $item) {
                $establecimiento->addMultiOption($item->idEstablecimiento, $item->nombreEstablecimiento);
            }
        }

        $curso = new Zend_Form_Element_Multiselect('idCursos');
        $curso->setRegisterInArrayValidator(false)->setRequired(true);
        $curso->setAttrib('class','mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm');
        $curso->setAttrib('data-placeholder','Seleccione Curso');

        $this->addElements(array($establecimiento, $curso));

    }

}
