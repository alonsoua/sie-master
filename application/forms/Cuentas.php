<?php

class Application_Form_Cuentas extends Zend_Form
{

    public function init()
    {

        //recuperamos el nombre del usuario que esta en sesion
        $establecimientos = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $establecimientos->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $idcuenta = new Zend_Form_Element_Hidden('idCuenta');

        //creamos <input text> para escribir nombre usuario
        $nombres = new Zend_Form_Element_Text('usuario');
        $nombres->setLabel('Rut Usuario:')->setRequired(true);

        $nombrereal = new Zend_Form_Element_Text('nombrescuenta');
        $nombrereal->setLabel('Nombres:')->setRequired(true);

        $paterno = new Zend_Form_Element_Text('paternocuenta');
        $paterno->setLabel('Apellido Paterno:')->setRequired(true);

        $materno = new Zend_Form_Element_Text('maternocuenta');
        $materno->setLabel('Apellido Materno:')->setRequired(true);

        $frmPassword1 = new Zend_Form_Element_Password('password');
        $frmPassword1->setLabel('Contraseña:')
            ->setRequired('true')
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_NotEmpty());

        $frmPassword2 = new Zend_Form_Element_Password('confirm_password');
        $frmPassword2->setLabel('Confirme Contraseña:')
            ->setRequired('true')
            ->addFilter(new Zend_Filter_StringTrim())
            ->addValidator(new Zend_Validate_Identical('password'));

        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo Electrónico:')->setRequired(true);

        //creamos select para seleccionar establecimiento
        $rbd = new Zend_Form_Element_Select('idEstablecimiento');
        $rbd->setLabel('Seleccione Establecimiento:')->setRequired(true);
        $table = new Application_Model_DbTable_Establecimiento();
        if ($rol == '3' || $rol == '4' || $rol == '6') {
            $rbd->addMultiOptions(array(
                "" => "Seleccione Establecimiento"
            ));
            foreach ($table->listarestablecimiento($establecimiento, $idperiodo) as $c) {


                $rbd->addMultiOption($c['idEstablecimiento'], $c['nombreEstablecimiento']);
            }
        }

        if ($rol == '1') {
            $rbd->addMultiOptions(array(
                "" => "Seleccione Establecimiento"
            ));
            foreach ($table->listar() as $c) {


                $rbd->addMultiOption($c->idEstablecimiento, $c->nombreEstablecimiento);
            }
        }
        //creamos select para seleccionar Curso
        $curso = new Zend_Form_Element_Select('curso');
        $curso->setLabel('Seleccione Curso:');
        $curso->setAttrib('id', 'curso_alumno');


        if ($rol == '3' || $rol == '4' || $rol == '6') {
            //creamos <select> para listar cargos
            $cargo = new Zend_Form_Element_Select('cargo');
            $cargo->setLabel('Cargo:')->setRequired(true);
            $cargo->addMultiOptions(array(
                "" => "Seleccione Cargo"
            ));

            $cargo->addMultiOption("3", "Director");
            $cargo->addMultiOption("6", "Jefe de UTP");
            $cargo->addMultiOption("2", "Docente");
        }
        if ($rol == '1') {
            //creamos <select> para listar cargos
            $cargo = new Zend_Form_Element_Select('cargo');
            $cargo->setLabel('Cargo:')->setRequired(true);
            $cargo->addMultiOptions(array(
                "" => "Seleccione Cargo"
            ));
            $cargo->addMultiOption("1", "Administrador Daem");
            $cargo->addMultiOption("3", "Director");
            $cargo->addMultiOption("6", "Jefe de UTP");
            $cargo->addMultiOption("2", "Docente");
            $cargo->addMultiOption("5", "Encargado Sige");
        }

        //creamos select para seleccionar establecimiento
        $periodo = new Zend_Form_Element_Select('periodo');
        $periodo->setLabel('Seleccione Periodo:')->setRequired(true);
        $table = new Application_Model_DbTable_Periodo();

        $periodo->addMultiOptions(array(
            "" => "Seleccione Periodo"
        ));
        foreach ($table->listar() as $c) {


            $periodo->addMultiOption($c->idPeriodo, $c->nombrePeriodo);
        }

        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('rut', 'submitbutton');
        $submit->setLabel('Guardar');


        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Personal', 'action' => 'index'), null, TRUE) . '\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($idcuenta, $nombres, $frmPassword1, $nombrereal, $paterno, $materno, $correo, $cargo, $rbd, $periodo));
    }


}

