<?php

class Application_Form_Mensajes extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;



        $idest = new Zend_Form_Element_Hidden('idEstablecimiento');


        if ($rol == 1) {


            //creamos select para seleccionar Establecimiento
            $establecimiento = new Zend_Form_Element_Multiselect('idEstablecimiento');
            $establecimiento->setLabel('Establecimiento  :');
            $establecimiento->setDecorators(array(
                'ViewHelper',
                'Errors',
                'Label'

            ));
            $establecimiento->setAttrib("class", "form-control");


            $tablaestablecimiento = new Application_Model_DbTable_Establecimiento();
            $rowestablecimientos = $tablaestablecimiento->listar();
            $establecimiento->addMultiOption("all", "Todos los Establecimientos");
            foreach ($rowestablecimientos as $e) {

                $establecimiento->addMultiOption($e->idEstablecimiento, $e->nombreEstablecimiento);
            }
        }


        if ($rol == 3 || $rol == 6) {


            $idest->setValue($idestablecimiento);

        }

        //creamos select para seleccionar Establecimiento
        $cuentas = new Zend_Form_Element_Multiselect('idCuenta');
        $cuentas->setLabel('Destinatarios  :')->setRequired(true);
        $cuentas->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));
        $cuentas->setAttrib("class", "form-control");
        $cuentas->setAttrib('id', 'idCuenta')->setRegisterInArrayValidator(false);
        $cryptor = new \Chirp\Cryptor();
        $modelocuenta = new Application_Model_DbTable_Mensaje();

        if ($rol == 1) {

            $rowcuenta = $modelocuenta->listarusuariotodo($idperiodo,$user,array(1,2,3,4,5,6));
            $cuentas->addMultiOption("alle", "Todos los Usuarios");

            foreach ($rowcuenta as $e) {

                $cuentas->addMultiOption($cryptor->encrypt($e['id']), $e['nombrescuenta'] . ' ' . $e['paternocuenta'] . ' ' . $e['maternocuenta'].' | '.$e['nombreRol'].' |'.$e['nombreEstablecimiento']);
            }
        }
        if ($rol == 3) {
            $rowcuenta = $modelocuenta->listarusuarioestablecimientos($idperiodo,$idestablecimiento,$user,array(1,2,3,4,5,6));
            $cuentas->addMultiOption("alle", "Todos los Usuarios");

            foreach ($rowcuenta as $e) {

                $cuentas->addMultiOption($cryptor->encrypt($e['id']), $e['nombrescuenta'] . ' ' . $e['paternocuenta'] . ' ' . $e['maternocuenta'].' | '.$e['nombreRol'].' |'.$e['nombreEstablecimiento']);
            }

        }

        if ($rol == 2) {
            $rowcuenta = $modelocuenta->listarusuarioestablecimientos($idperiodo,$idestablecimiento,$user,array(3,6));
            //$cuentas->addMultiOption("alle", "Todos los Usuarios");

            foreach ($rowcuenta as $e) {

                $cuentas->addMultiOption($cryptor->encrypt($e['id']), $e['nombrescuenta'] . ' ' . $e['paternocuenta'] . ' ' . $e['maternocuenta'].' | '.$e['nombreRol'].' |'.$e['nombreEstablecimiento']);
            }

        }


        //creamos select para seleccionar Establecimiento
        $roles = new Zend_Form_Element_Multiselect('idRol');
        $roles->setLabel('Roles  :');
        $roles->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));
        $roles->setAttrib("class", "form-control");
        $roles->setAttrib('id', 'idRol')->setRegisterInArrayValidator(false);


        $modelocuenta = new Application_Model_DbTable_Cuentas();
        $rowcuenta = $modelocuenta->listartodo($idperiodo, 1);
        $roles->addMultiOption("allr", "Todos los Roles");
        $cryptor = new \Chirp\Cryptor();
        if ($rol == 1) {
            $roles->addMultiOption($cryptor->encrypt(3), 'Director');
            $roles->addMultiOption($cryptor->encrypt(6), 'Jefe UTP');
            $roles->addMultiOption($cryptor->encrypt(2), 'Docente');
        }
        if ($rol == 3) {
            $roles->addMultiOption($cryptor->encrypt(6), 'Jefe UTP');
            $roles->addMultiOption($cryptor->encrypt(2), 'Docente');
        }
        if ($rol == 6) {
            $roles->addMultiOption($cryptor->encrypt(2), 'Docente');
        }


        $asunto = new Zend_Form_Element_Text('asunto');
        $asunto->setLabel('Asunto :')->setRequired(true);
        $asunto->setAttrib("class", "form-control");
        $asunto->setAttrib("placeholder", "Asunto:");
        $asunto->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $mensaje = new Zend_Form_Element_Textarea('mensaje');
        $mensaje->setLabel('Mensaje :')->setRequired(true);
        $mensaje->setAttrib('id', 'compose-textarea');
        $mensaje->setAttrib('style', 'style="height: 300px"');
        $mensaje->setAttrib("class", "form-control");
        $mensaje->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('RBD', 'submitbutton');
        $submit->setLabel('Enviar');

        if ($rol == 1) {
            $this->addElements(array($establecimiento, $roles, $cuentas, $asunto, $mensaje, $submit));
        }
        if ($rol == 3 || $rol == 6) {
            $this->addElements(array($idest, $roles, $cuentas, $asunto, $mensaje, $submit));
        }

        if ($rol == 2 ) {
            $this->addElements(array($cuentas,$asunto, $mensaje, $submit));
        }


    }
}
