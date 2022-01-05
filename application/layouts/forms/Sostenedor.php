<?php

class Application_Form_Sostenedor extends Zend_Form
{

    public function init()
    {
        $this->setName('sostenedor');
        $idhidden=new Zend_Form_Element_Hidden('idSostenedor');
        
        //campo <input text> para guardar rbd establecimiento
        $rutsostenedor = new Zend_Form_Element_Text('rutSostenedor');
        $rutsostenedor->setLabel('Rut Sostenedor:')->setRequired(true);
        //$rutsostenedor->addPrefixPath('Validator', 'Validator/', Zend_Form_Element::VALIDATE);
        //$rutsostenedor->addValidator('Rut','false');
        //creamos <input text> para escribir nombre Sostenedor
        
        $nombre = new Zend_Form_Element_Text('nombreSostenedor');
        $nombre->setLabel('Nombre del Sostenedor:')->setRequired(true);
         
        //creamos <input text> para escribir direccion sostenedor
        $direccion = new Zend_Form_Element_Text('direccion');
        $direccion->setLabel('Dirección:')->setRequired(true);
        
        //creamos <input text> para escribir telefono sostenedor
        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Teléfono:')->setRequired(true);
        
        //creamos select provincia sostenedor
      $regionSelect = new Zend_Form_Element_Select('region');
      $regionSelect->setLabel('Región: ');
      $regionSelect->setAttrib('id', 'item_region');
      
     $regionModel = new Application_Model_DbTable_Region();
     $rowset1 = $regionModel->listarregiones();
      foreach($rowset1 as $row){
         $regionSelect->addMultiOption($row->idRegion, $row->nombreRegion);
      }
        
        
        //creamos select provincia sostenedor
      $provinciaSelect = new Zend_Form_Element_Select('provincia');
      $provinciaSelect->setLabel('Provincia: ');
      $provinciaSelect->setAttrib('id', 'item_provincia')->setRegisterInArrayValidator(false);
 
      //creamos select comuna sostenedor
      $comunaSelect = new Zend_form_element_select('comuna');
      $comunaSelect->setLabel('Comuna: ');
      $comunaSelect->setAttrib('id', 'item_comuna')->setRegisterInArrayValidator(false);
 
  
        
        //creamos <input text> para escribir correo sostenedor
        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo:')->setRequired(true);
      
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('Rut_sostenedor', 'submitbutton');
        
        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'Sostenedor','action'=>'index'),null,TRUE).'\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($idhidden,$rutsostenedor, $nombre, $direccion,$telefono,$regionSelect,$provinciaSelect,$comunaSelect,$correo,$submit));
        $this->addElements(array($join));
    }

    


}

