$(function() {

    
//si presiona boton guardar 
    $('#guardarobservaciones').click(function() {

       //recibimos valores de los campos del formulario
        var rutalumno= $('#rutalumno').val();
        var idcurso = $('#cursoalumno').val();
       
        var asignatura = $('#itemasignatura').val();
        var observacion = $('textarea#observacion').val();
        
       //validamos los datos del formulario 
       if(observacion==''|| observacion=='NULL'){
            alert('El campo observacion esta vacio');
            $('textarea#observacion').focus();
        if(rutalumno==''|| rutalumno=='NULL'){
            alert('Debe seleccionar un Alumno');
            $('#itemalumnos').focus();}}
        
        else{
        var dataString = {"rut": rutalumno,
        "idcurso": idcurso,
       
        "asignatura": asignatura,
        "observacion": observacion};
    //alert(dataString.observacion);

        $.ajax(
        {
            cache: false,
            async: true,
            dataType: 'html',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            url: '../../../observaciones/guardarobservacion/',
            data: dataString,
            beforeSend: function(data){
                    $('#div_cliente').html('<label>Cargando...</label>');
            },
            success: function(requestData){
            $("#formobs")[0].reset();
            cargalista(rutalumno);
                    
            },
            error: function(requestData, strError, strTipoError){
                    alert('Error ' + strTipoError +': ' + strError);
            },
            complete: function(requestData, exito){
            }
        });
        }
        
        return false;
    });
});
function cargalista(rut){
    $(function() {
      
   $.ajax({
             cache: false,
             type: "GET",
             url: "../../../observaciones/index/id/" + rut,
             async: true,
             dataType: "html",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success : function(data){
              
                $("#cargaobservaciones").html(data); 
 
             }
   }); 
  });  
  
    
}