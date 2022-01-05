<?php

class Application_Form_Cambiarcontrasena extends Zend_Form
{

    public function init()
    {

        $this->setName('cambiarpass');

        $id = new Zend_Form_Element_Hidden('idCuenta');


        $frmPassword1 = new Zend_Form_Element_Password('password');
        $frmPassword1->setLabel('Contraseña Nueva:')
            ->setRequired('true')
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_NotEmpty());
        $frmPassword1->setAttrib('class','shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline');

        $frmPassword2 = new Zend_Form_Element_Password('confirm_password');
        $frmPassword2->setLabel('Confirme Contraseña Nueva:')
            ->setRequired('true')
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_Identical('password'));

        $frmPassword2->setAttrib('class','shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline');

        $verifica = new Zend_Form_Element_Password('verifica');
        $verifica->setLabel('Introduzca contraseña antigua para confirmar:')
            ->setRequired('true')
            ->addFilter(new Zend_Filter_StringTrim());
        $verifica->setAttrib('class','shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline');

        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('rut', 'submitbutton');
        $submit->setLabel('Guardar');
        $submit->setAttrib('class','bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline');



        $this->addElements(array($frmPassword1, $frmPassword2, $verifica, $submit, $id));
    }


}

