<?php

class Application_Form_configuracionMensajes extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));



        $id = new Zend_Form_Element_Hidden('id');
        $idest = new Zend_Form_Element_Hidden('idEstablecimiento');

        //Crear Mensajes

        $daemdirector = new Zend_Form_Element_Checkbox('nuevoDirector');
        $daemdirector->setLabel('Habilitar Creación Mensajes Director ')->setValue('1');


        $daemutp = new Zend_Form_Element_Checkbox('nuevoUtp');
        $daemutp->setLabel('Habilitar Creación Mensajes UTP: ')->setValue('1');
        $daemutp->setOptions(array('label_class' => array('class' => 'control-label')));

        $daemdocente = new Zend_Form_Element_Checkbox('nuevoDocente');
        $daemdocente->setLabel('Habilitar Creación Mensajes Docente ')->setValue('1');
        $daemdocente->setOptions(array('label_class' => array('class' => 'control-label')));



       // Permitir Respuestas

        //Adminisrador Daem
        $daemdirectorrespuesta = new Zend_Form_Element_Checkbox('daemDirector');
        $daemdirectorrespuesta->setLabel('Permitir Respuesta Director')->setValue('1');
        $daemdirectorrespuesta->setOptions(array('label_class' => array('class' => 'control-label')));

        $daemutprespuesta = new Zend_Form_Element_Checkbox('daemUtp');
        $daemutprespuesta->setLabel('Permitir Respuesta Jefe UTP ')->setValue('1');
        $daemutprespuesta->setOptions(array('label_class' => array('class' => 'control-label')));

        $daemdocenterespuesta = new Zend_Form_Element_Checkbox('daemDocente');
        $daemdocenterespuesta->setLabel('Permitir Respuesta Docente ')->setValue('1');
        $daemdocenterespuesta->setOptions(array('label_class' => array('class' => 'control-label')));

        //Director

        $directorutprespuesta = new Zend_Form_Element_Checkbox('directorUtp');
        $directorutprespuesta->setLabel('Permitir Respuesta Jefe UTP ')->setValue('1');
        $directorutprespuesta->setOptions(array('label_class' => array('class' => 'control-label')));

        $directordocenterespuesta = new Zend_Form_Element_Checkbox('directorDocente');
        $directordocenterespuesta->setLabel('Permitir Respuesta Docente ')->setValue('1');
        $directordocenterespuesta->setOptions(array('label_class' => array('class' => 'control-label')));

        // UTP


        $utpdocenterespuesta = new Zend_Form_Element_Checkbox('utpDocente');
        $utpdocenterespuesta->setLabel('Permitir Respuesta Director ')->setValue('1');
        $utpdocenterespuesta->setOptions(array('label_class' => array('class' => 'control-label')));




        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('RBD', 'submitbutton');
        $submit->setLabel('Guardar');


            $this->addElements(array($id, $idest,$daemdirector,$daemutp,$daemdocente,$daemdirectorrespuesta,$daemutprespuesta,$daemdocenterespuesta,$directorutprespuesta,$directordocenterespuesta,$utpdocenterespuesta, $submit));





    }
}
