$(function() {

    // bloqueamos la tecla enter para que no recargue la página
    $('#form_ajax').bind('keypress', function(e) {
        if (e.keyCode == 13) return false;
    });

    $('#guardarcomunicacion').click(function() {

       var selectedOptions = $("#Rut_alumnocomunicacion option");
        var values = $.map(selectedOptions ,function(option) {
            return option.value;
        });
        
        var observacion = $('textarea#comunicacion').val();
        
         if(values=='' || values=='Null'){
       $('#Rut_alumnocomunicacion').focus();
                    var $re=$('#Rut_alumnocomunicacion'),
                    x=4000;
                    $re.css("background", "#FFBABA");
                    setTimeout(function(){
                    $re.css("background", "white");
                    }, x);
                    return false; 
        
    }
         if(observacion=='' || observacion=='Null'){
       $('#comunicacion').focus();
                    var $re=$('#comunicacion'),
                    x=4000;
                    $re.css("background", "#FFBABA");
                    setTimeout(function(){
                    $re.css("background", "white");
                    }, x);
                    return false; 
        
    }
    
        
        
        
            //creamos el json con los datos
        var edited = "{";       
        for(var i=0;i<values.length;i++) {
    
        edited += '"'+i+'":{"rut":"'+values[i]+'","comunicacion":"'+observacion+'"},';
    
         }
        edited = edited.slice(0, -1);
        edited += "}";
        //fin creacion json
 //$.parseJSON(edited);      
        
        
       //console.log(edited);
//guardamos los datos por ajax
        $.ajax(
        {
            cache:false,
            async: true,
            dataType: 'json',
            type: 'POST',
            
            url: 'guardarcitacion/',
            data: edited ,
            beforeSend: function(data){
                    $('#div_cliente').html('<label>Cargando...</label>');
            },
            success: function( data ){
             window.location.replace(data.redirect);
                    
            },
            
            complete: function(requestData, exito){
            }
        });
        
       
        return false;
    });
});
