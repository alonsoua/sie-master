<?php

class Application_Form_Cuentasrol extends Zend_Form {

    public function init() {

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));


        //recuperamos el nombre del usuario que esta en sesion
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        //recuperamos el nombre del usuario que esta en sesion
        $usuario = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $usuario->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        //creamos <input text> para escribir nombre usuario
        $id = new Zend_Form_Element_Hidden('idCuenta');


        //creamos <input text> para escribir nombre usuario
        //creamos <input text> para escribir nombre usuario
        $nombres = new Zend_Form_Element_Text('usuario');
        $nombres->setLabel('Nombre Usuario:')->setRequired(true);
        $nombres->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $nombrereal = new Zend_Form_Element_Text('nombrescuenta');
        $nombrereal->setLabel('Nombres:')->setRequired(true);
        $nombrereal->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $paterno = new Zend_Form_Element_Text('paternocuenta');
        $paterno->setLabel('Apellido Paterno:')->setRequired(true);
        $paterno->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $materno = new Zend_Form_Element_Text('maternocuenta');
        $materno->setLabel('Apellido Materno:')->setRequired(true);
        $materno->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos select para seleccionar establecimiento
        $rbd = new Zend_Form_Element_Select('rbd');
        $rbd->setLabel('Seleccione Establecimiento:')->setRequired(true);
        $rbd->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $table = new Application_Model_DbTable_Establecimiento();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista
        if ($rol == '3' || $rol == '4' || $rol == '6') {
            $datosestablecimiento=$table->listarestablecimiento($establecimiento,$idperiodo);
            foreach ($datosestablecimiento as $c) {


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
        //creamos select para seleccionar establecimiento
        $periodo = new Zend_Form_Element_Select('periodo');
        $periodo->setLabel('Seleccione Periodo:')->setRequired(true);
        $periodo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        //cargo en un select los Peridos
        $table = new Application_Model_DbTable_Periodo();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista

        $periodo->addMultiOptions(array(
            "" => "Seleccione Periodo"
        ));
        foreach ($table->listar() as $c) {
            $periodo->addMultiOption($c->idPeriodo, $c->nombrePeriodo);
        }


        $cargos = new Zend_Form_Element_Select('cargo');
        $cargos->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $cargos->setLabel('Cargo *:')->setRequired(true);
        $cargos->addMultiOptions(array(
            "" => "Seleccione Cargo"
        ));

        if ($rol == '3' || $rol == '4' || $rol == '6') {

            $cargos->addMultiOption("3", "Director");
            $cargos->addMultiOption("6", "Jefe UTP");
            $cargos->addMultiOption("2", "Docente");
        }
        if ($rol == '1') {

            $cargos->addMultiOption("1", "Administrador Daem");
            $cargos->addMultiOption("3", "Director");
            $cargos->addMultiOption("6", "Jefe UTP");
            $cargos->addMultiOption("2", "Docente");
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
        $this->addElements(array($id, $nombres, $nombrereal, $paterno, $materno, $cargos, $rbd, $periodo));
    }


}

