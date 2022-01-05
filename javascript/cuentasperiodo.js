$(function(){
        
        
        //si cambia el select de Establecimiento
       $('#periodo').live('change',function(){
           
       var id = $('#rbd').val();
      
            //$('#asignaturap').html('');
             //$('#unidad').html('');
             //$('#tablaplanificaciones').html('');
       
          var ajax = $.ajax({
             type: "GET",
             url: "../../getcurso/id/" + id,
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
              
             var envia=new Array();
               var options = '<ul class="span4" style="margin:20px 0 20px 0;"><li><input type="checkbox" id="all" name="check-all" /> Seleccionar Todos </li>';
                   for (var i = 0; i < data.length; i++) {
                       envia[i]=data[i].id_nivel_curso;
                       
                      options += '<li class="parent-list"><input type="checkbox" id="cursoss" value="'+ data[i].id_nivel_curso + '"  class="canine parent" /> '+ data[i].nombre_curso +' <i class="icon-caret-down"></i><div class="'+ data[i].id_nivel_curso + '" name="'+ data[i].idCursos + '"></div>';
                   }
                   options+='</ul>';
                   options+='</table><input type="button" value="Guardar" id="guardarusuarioperiodo">';
                   //options+='<ul><li><input type="checkbox" name="" class="canine" id="" /> American Water Spaniel </li><li><input type="checkbox" name="" class="canine" id="" /> Brittany </li></ul></li>';
                   
                   $('#asignatura').html(options);
                   asignaturas(envia);
                /*var options = '<option value="Null">Seleccione Opción</option>';
                   for (var i = 0; i < data.length; i++) {
                       
                      options += '<option value="' + data[i].idCursos + '" name="'+ data[i].id_nivel_curso + '">' + data[i].nombre_curso + '</option>';
                   }
                   $('#curso_alumno').html(options);*/
             }
        });
        
      });
   });
      
      
      
//si presiona boton guardar 
$(document).ready(function() {
    $("#guardarusuarioperiodo").live('click',function () {
        
     
   
     var nombre = $('#username').val();
     var periodo = $('#periodo').val();


       //Obtenemos el valor de los check padres seleccionados
       
                
                //Obtenemos el valor de los check  seleccionados
        var valor2= new Object();
        valor2.as='';
        valor2.niv='';
        valor2.cur='';
        //obtenemos las asignaturas
        valor2.as = $('input[id=hijos]:checked ').map(function() {
        return valor2.as=$(this).val();
                }).get();
                
                //obtenemos los niveles
                valor2.niv=$('input[id=hijos]:checked ').map(function() {
        return valor2.niv=$(this).attr('name'); 
                }).get();
              //obtenemos los cursos  
         valor2.cur = $('input[id=hijos]:checked ').map(function() {
        return $(this).parents("div:first").attr('name');
                }).get();
                         
                
                //console.log(valor2);
         
         
            //alert('IDS: ' + valor.join(', '));
            
        //creamos el json con los datos para enviar y guardar
        var edited = "{";       
        for(var i=0;i<(valor2.as).length;i++) {
    
        edited += '"'+[i]+'":{"curso":"'+valor2.cur[i]+'","nivel":"'+valor2.niv[i]+'","nombre":"'+nombre+'","periodo":"'+periodo+'","asignatura":"'+valor2.as[i]+'"},';
    
         }
        edited = edited.slice(0, -1);
        edited += "}";
        
        //fin creacion json
        
       //validamos los datos del formulario 
      /* if(observacion==''|| observacion=='NULL'){
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
    //alert(dataString.observacion);/*/

       $.ajax(
        {
            cache: false,
            async: true,
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            url: '../../guardausuarioperiodo/',
            data: edited,
            beforeSend: function(data){
                    $('#div_cliente').html('<label>Cargando...</label>');
            },
            success: function(data){
            if(data.response=='existe'){
           $('#contenido').append('<div class="error mensajes">El periodo que intenta ingresar, ya existe para el usuario</div>');
           $(document).ready(function(){
            setTimeout(function(){ 
          $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();}, 3000); 
});
}
            else{
            
           window.location.replace(data.redirect);
       }       
            },
            error: function(requestData, strError, strTipoError){
                    alert('Error ' + strTipoError +': ' + strError);
            },
            complete: function(requestData, exito){
            }
        });
        //
        
        return false;
  });
  });     
  
 




    
    $("li.parent-list i").live('click',function () {
       // alert($(this).attr('id'));
          //var nivel=$(this).attr('id');
        
        
        
        
       $(this).toggleClass('icon-caret-up'); // toggle the font-awesome icon class on click
      $(this).next("div").toggle(); // toggle the visibility of the child list on click
        
    });



// check-uncheck all
    $(document).on('change', 'input[id="all"]', function () { 
        $('.canine').prop("checked", this.checked);
    });

// parent/child check-uncheck all
    $(function () {
        $('.parent').live('click', function () {
            $(this).closest('ul li').find(':checkbox').prop('checked', this.checked);
        });
    });
    
    
 /****************************carga asignaturas a curso correpondiente***************************/   
      function asignaturas(id){
          //console.log(id);
           //console.log(id);
           for (var i = 0; i < id.length; i++) {
               var nivel=id[i];
               //console.log(id[i]);
        if(nivel==1 || nivel==2 || nivel==3 || nivel==4 || nivel==5 || nivel==6){
            
  /**************************************Carga Asignaturas Basica******************************************************/      
      
       //console.log(cortado2);
       //alert(cortado2[0]);
          var ajax = $.ajax({
             type: "GET",
             url: "../../getasignatura/id/"+nivel,
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
              var options = '<ul>';
                   for (var i = 0; i < data.length; i++) {
                      options += '<li><input type="checkbox" value="'+data[i].ASIGNATURAP_ID+'" name="'+data[i].ASIGNATURAP_NIVEL_ID+'" id="hijos" class="canine" />'+data[i].ASIGNATURAP_NOMBRE+'</li>';
                   }
                   options+='</ul>';
                   
                   $('div.'+data[0].ASIGNATURAP_NIVEL_ID).html(options);
                   
                   //$('#indices').html(options2);
                   //console.log(options);
             },
             error: function(requestData, strError, strTipoError){
                    alert('Error ' + strTipoError +': ' + strError);
            }
        });
        }if(nivel==9 || nivel==10 || nivel==11 || nivel==12){
          /********************************Carga Asignaturas Media**********************************/
             var ajax = $.ajax({
             type: "GET",
             url: "../../getasignaturamedia/id/"+nivel,
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
              var options = '<ul>';
                   for (var i = 0; i < data.length; i++) {
                      options += '<li><input type="checkbox" value="'+data[i].ASIGNATURA_ID+'" name="'+data[i].ASIGNATURA_ID_NIVEL+'" id="hijos"  class="canine" />'+data[i].ASIGNATURA_NOMBRE+'</li>';
                   }
                   options+='</ul>';
                   
                   $('div.'+data[0].ASIGNATURA_ID_NIVEL).html(options);
                   
                   //$('#indices').html(options2);
                  // console.log(data);
             },
             error: function(requestData, strError, strTipoError){
                    alert('Error ' + strTipoError +': ' + strError);
            }
        });
            
        }
        if(nivel==7 || nivel==8 ){
          /********************************Carga Asignaturas Media**********************************/
             var ajax = $.ajax({
             type: "GET",
             url: "../../getasignaturasep/id/"+nivel,
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
              var options = '<ul>';
                   for (var i = 0; i < data.length; i++) {
                      options += '<li><input type="checkbox" value="'+data[i].ASIGNATURA_ID+'" name="'+data[i].ASIGNATURA_ID_NIVEL+'" id="hijos"  class="canine" />'+data[i].ASIGNATURA_NOMBRE+'</li>';
                   }
                   options+='</ul>';
                   
                   $('div.'+data[0].ASIGNATURA_ID_NIVEL).html(options);
                   
                   //$('#indices').html(options2);
                  // console.log(data);
             },
             error: function(requestData, strError, strTipoError){
                    alert('Error ' + strTipoError +': ' + strError);
            }
        });
            
        }
        
         if(nivel==13){
          /********************************Carga Asignaturas Media**********************************/
             var ajax = $.ajax({
             type: "GET",
             url: "../../getasignaturamediatecnico/id/"+nivel,
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
              var options = '<ul>';
                   for (var i = 0; i < data.length; i++) {
                      options += '<li><input type="checkbox" value="'+data[i].ASIGNATURA_ID+'" name="'+data[i].ASIGNATURA_ID_NIVEL+'" id="hijos"  class="canine" />'+data[i].ASIGNATURA_NOMBRE+'</li>';
                   }
                   options+='</ul>';
                   
                   $('div.'+data[0].ASIGNATURA_ID_NIVEL).html(options);
                   
                   //$('#indices').html(options2);
                  // console.log(data);
             },
             error: function(requestData, strError, strTipoError){
                    alert('Error ' + strTipoError +': ' + strError);
            }
        });
            
        }
           }
            
      };

