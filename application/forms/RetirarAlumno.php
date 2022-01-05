<?php

class Application_Form_RetirarAlumno extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');

        $fecharet = new Zend_Form_Element_Text('fechaRetiro');
        $fecharet->setAttrib('readonly', 'true');
        $fecharet->setLabel('Fecha Retiro(*):')->setRequired(true);
        $fecharet->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $id = new Zend_Form_Element_Hidden('idAlumnosActual');
        $id->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));



        $motivo = new Zend_Form_Element_Text('motivo');
        $motivo->setLabel('Motivo (Opcional):');
        $motivo->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->removeDecorator('label');
        $submit->setLabel('Guardar');
        $submit->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));





        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Alumnos', 'action' => 'index'), null, true) . '\' ');
        $join->removeDecorator('label');
        $join->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        //agrego los objetos creados al formulario
        $this->addElements(array(
            $id,
            $fecharet,
            $motivo,
            $submit,
            $join));


    }
}
