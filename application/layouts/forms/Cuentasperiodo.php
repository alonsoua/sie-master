<?php

class Application_Form_Cuentasperiodo extends Zend_Form
{

    public function init()
    {
        
        //recuperamos el nombre del usuario que esta en sesion
      $usuario= new Zend_Session_Namespace('establecimiento');
      //
      $establecimiento= $usuario->establecimiento;
      
       //creamos <input text> para escribir nombre usuario
        $id = new Zend_Form_Element_Hidden('idCuenta');
        
        
         //creamos <input text> para escribir nombre usuario
          //creamos <input text> para escribir nombre usuario
        $nombres = new Zend_Form_Element_Text('usuario');
        $nombres->setLabel('Rut Usuario:')->setRequired(true);
        
        $nombrereal = new Zend_Form_Element_Text('nombrescuenta');
        $nombrereal->setLabel('Nombres:')->setRequired(true);
        
        $paterno = new Zend_Form_Element_Text('paternocuenta');
        $paterno->setLabel('Apellido Paterno:')->setRequired(true);
        
        $materno = new Zend_Form_Element_Text('maternocuenta');
        $materno->setLabel('Apellido Materno:')->setRequired(true);

        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo Electrónico:')->setRequired(true); 
        
        //creamos select para seleccionar establecimiento
        $rbd = new Zend_Form_Element_Select('rbd');
        $rbd->setLabel('Seleccione Establecimiento:')->setRequired(true);
      //creamos select para seleccionar establecimiento
        $periodo = new Zend_Form_Element_Select('periodo');
        $periodo->setLabel('Seleccione Periodo:')->setRequired(true);
        //cargo en un select los Peridos
        $table = new Application_Model_DbTable_Periodo();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista
       
        $periodo->addMultiOptions(array(
        "" => "Seleccione Periodo"   
    ));
        foreach ($table->listar() as $c)
        {
            $periodo->addMultiOption($c->idPeriodo,$c->nombrePeriodo);
        }
        
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('rut', 'submitbutton');
        $submit->setLabel('Guardar');
        
        
        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'Personal','action'=>'index'),null,TRUE).'\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($id,$nombres,$nombrereal,$paterno,$materno,$correo,$rbd,$periodo));
    }


}

