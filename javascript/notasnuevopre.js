$(function(){
$('#tipoEvaluacionPrueba').live('change',function(e){
 if($(this).val()!='Null'){
        if($('#alumnos').find('table').length==0){
         var ajax = $.ajax({
             type: "GET",
             url: "../../../../getindicadorespre",
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){

                 if(data=='' || data=='Null'){
                     $('#alumnos').html('<div class="info">No existen alumnos en éste curso</div>');

                 }else{
                 var options='';
                options+='<table class="striped sortable" style="margin-top:30px;"><th>Indicador</th><th>Concepto</th>';
                   for (var i = 0; i < data.length; i++) {
                      options+= '<tr><td><input type="hidden" value="' + data[i].idAsignatura + '"  /><ul class="breadcrumbs"><li><a style="color:#000000">'+data[i].nombreAmbito+'</a></li><li><a style="color:#000000">'+data[i].nombreNucleo+'</a></li></ul>'+data[i].nombreAsignatura +'</td><td><input  onpaste="return false" type="text" size="5" maxlength="1" class="notas" id="' + data[i].idAsignatura + '"/></td></tr>';
                   }
                   options+='</table><input type="button" value="Guardar" id="guardarnotas">';

                   $('#alumnos').html(options);

             }
         }
        });
       }
    }else{
       $('#alumnos').html('');
    }
        e.stopImmediatePropagation();

       });








      //si presiona boton guardar
    $("#guardarnotas").live('click',function (e) {

    //obtenemos valor del curso
     var alumno = $('#idAlumnos').val();
     var tipoevaluacion=$('#tipoEvaluacionPrueba').val();

      //Obtenemos el valor de los check seleccionados
    var check;
    check = $('.notas').map(function() {
        return $(this);
    }).get();


    if(alumno=='' || alumno=='Null' || alumno<0){
       $('#idAsignatura').focus();
                    var $re=$('#idAlumnos'),
                    x=4000;
                    $re.css("background", "#FFBABA");
                    setTimeout(function(){
                    $re.css("background", "white");
                    }, x);
                    return false;

    }


    if(tipoevaluacion=='' || tipoevaluacion=='Null' || tipoevaluacion<0){
       $('#tipoEvaluacionPrueba').focus();
                    var $re=$('#tipoEvaluacionPrueba'),
                    x=4000;
                    $re.css("background", "#FFBABA");
                    setTimeout(function(){
                    $re.css("background", "white");
                    }, x);
                    return false;

    }
        if(check=='' || check=='Null'){

            return false;

        }

        for (var i=0;i<check.length;i++){
            if(check[i].val()=='' || check[i].val()=='Null' || check[i]==undefined){
                $(check[i]).focus();
                var $re=check[i],
                    x=4000;
                $re.css("background", "#FFBABA");
                setTimeout(function(){
                    $re.css("background", "white");
                }, x);

                return false;
                break;
            }

        };





        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        for(var i=0;i<check.length;i++) {


        edited += '"'+i+'":{"nota":"'+check[i].val()+'","alumno":"'+alumno+'","tipoevaluacion":"'+tipoevaluacion+'","asignatura":"'+check[i].attr('id')+'"},';



    }
        edited = edited.slice(0, -1);
        edited += "}";


        $.ajax(
        {
            cache: false,
            async: true,
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            url: '../../../../guardanotasprenuevo/',
            data: edited,
            beforeSend: function(data){
                    $('#div_cliente').html('<label>Cargando...</label>');
            },
            success: function(data){
            //alert(data.redirect);


            if(data.response=='errorsesion'){
           $('#contenido').append('<div class="error mensajes">El registro que intenta insertar, ya existe</div>');
           $(document).ready(function(){
      setTimeout(function(){
          $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();}, 3000);
});

            }
            if(data.response=='errorinserta'){
           $('#contenido').append('<div class="error mensajes">Ocurrio un error al guardar las notas,porfavor intente nuevamente</div>');
           $(document).ready(function(){
      setTimeout(function(){
          $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();}, 3000);
});

            }
            else{

              window.location.replace(data.redirect);
              //$('#contenido').append('<div class="error mensajes">El registro que intenta insertar, ya existe</div>');
            }


            },
            error: function(data){
                    //window.location.replace(data.redirect);
                    //alert('Error : ' + strError);
            },
            complete: function(requestData, exito){
            }
        });




       e.stopImmediatePropagation();
  });
    });
