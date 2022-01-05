$(function(){
	
	
	//evento al hacer clic en el boton agregar
	$('#btnAgregar').on('click',function(){
		//obtenemos el nombre digitado por el usuario, y el limite establecido
		//con la funcion parseInt() convertimos de texto a numero
		var $txtNombre=$('#txtNombre');
		
		//verificamos que el campo nombre no este vacio
		if($.trim($txtNombre.val())!=''){
			//variable para contener la lista html
			var $ulLista;
			//si la lista html no existe entonces la agregamos al dom
			if(!$('#divLista').find('ul').length) $('#divLista').append('<ul/>');
			
			//obtenemos una instancia de la lista
			$ulLista=$('#divLista').find('ul');
			
			//verificamos el limite de elementos
			
				//creamos el item que va a contener el nombre y el boton eliminar
				var $liNuevoNombre=$('<li/>').html('<i title="Eliminar Recurso" style="cursor:pointer;" id="clsEliminarElemento" class="icon-trash icon-1x"></i>    '  +$.trim($txtNombre.val()));
				
				//verificamos la posicion en la que debemos agregar el nuevo elemento (inicio o final de la lista)
				if($('#chkAgregarAlInicio').is(':checked')){
					//agregamos el elemento al inicio (con prepend)
					$ulLista.prepend($liNuevoNombre);
				}else{
					//agregamos el elemento al final de la lista (con append)
					$ulLista.append($liNuevoNombre);
				}
			
		//el campo nombre esta vacio
		}else{
			alert('Por favor, digite el nombre que desea agregar a la lista.')
		}
		//limpiamos el campo nombre y lo enfocamos
		$txtNombre.val('').focus();
	});
	
	//evento al hacer clic en el boton eliminar de cada item de la lista
	//se debe usar "live", ya que son elementos generados donamicamente
	$('#clsEliminarElemento').live('click',function(){
		//buscamos la lista
		var $ulLista=$('#divLista').find('ul');
		//buscamos el padre del boton (el tag li en el que se encuentra)
		var $liPadre=$($(this).parents().get(0));
		
		//eliminamos el elemento
		$liPadre.remove();
		//si la listaesta vacia entonces la eliminamos del dom
		if($ulLista.find('li').length==0) $ulLista.remove();
	});
	
	
	
	//eliminamos la lista del dom
	$('#btnEliminarTodo').on('click',function(){
		$('#divLista ul').remove();
	});
	
	//al presionar <ENTER> sobre el campo txtNombre llamamos al boton (usabilidad otra vez)
	$('#txtNombre').on('keypress',function(eEvento){
		if(eEvento.which==13) $('#btnAgregar').trigger('click');
	});	
});

$(function(){
	
	
	//evento al hacer clic en el boton agregar
	$('#btnAgregarevaluacion').on('click',function(){
		//obtenemos el nombre digitado por el usuario, y el limite establecido
		//con la funcion parseInt() convertimos de texto a numero
		var $txtNombre=$('#txtNombreevaluacion');
		
		//verificamos que el campo nombre no este vacio
		if($.trim($txtNombre.val())!=''){
			//variable para contener la lista html
			var $ulLista;
			//si la lista html no existe entonces la agregamos al dom
			if(!$('#divListaev').find('ul').length) $('#divListaev').append('<ul/>');
			
			//obtenemos una instancia de la lista
			$ulLista=$('#divListaev').find('ul');
			
			//verificamos el limite de elementos
			
				//creamos el item que va a contener el nombre y el boton eliminar
				var $liNuevoNombre=$('<li/>').html('<i title="Eliminar Recurso" style="cursor:pointer;" id="clsEliminarElementoev" class="icon-trash icon-1x"></i>    '  +$.trim($txtNombre.val()));
				
				//verificamos la posicion en la que debemos agregar el nuevo elemento (inicio o final de la lista)
				if($('#chkAgregarAlInicioev').is(':checked')){
					//agregamos el elemento al inicio (con prepend)
					$ulLista.prepend($liNuevoNombre);
				}else{
					//agregamos el elemento al final de la lista (con append)
					$ulLista.append($liNuevoNombre);
				}
			
		//el campo nombre esta vacio
		}else{
			alert('Por favor, digite el nombre que desea agregar a la lista.')
		}
		//limpiamos el campo nombre y lo enfocamos
		$txtNombre.val('').focus();
	});
	
	//evento al hacer clic en el boton eliminar de cada item de la lista
	//se debe usar "live", ya que son elementos generados donamicamente
	$('#clsEliminarElementoev').live('click',function(){
		//buscamos la lista
		var $ulLista=$('#divListaev').find('ul');
		//buscamos el padre del boton (el tag li en el que se encuentra)
		var $liPadre=$($(this).parents().get(0));
		
		//eliminamos el elemento
		$liPadre.remove();
		//si la listaesta vacia entonces la eliminamos del dom
		if($ulLista.find('li').length==0) $ulLista.remove();
	});
	
	
	
	//eliminamos la lista del dom
	$('#btnEliminarTodoev').on('click',function(){
		$('#divListaev ul').remove();
	});
	
	//al presionar <ENTER> sobre el campo txtNombre llamamos al boton (usabilidad otra vez)
	$('#txtNombreevaluacion').on('keypress',function(eEvento){
		if(eEvento.which==13) $('#btnAgregarevaluacion').trigger('click');
	});	
});