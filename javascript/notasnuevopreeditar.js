$(function () {

    $("#guardarnotas").live('click', function (e) {

        //Obtenemos el valor de los check seleccionados
        var alumno = $('#idAlumnos').val();
        var check;
        check = $('.notas').map(function () {
            return $(this);
        }).get();
        var id=$("#ida").val();
        var s=$("#seg").val();
        var diast=$("#diasTrabajado").val();
        var diasi=$("#diasInasistencia").val();
        var obs=$("#observaciones").val();
        if(obs){
            obs=limpiartexto(obs);
        }



        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        for (var i = 0; i < check.length; i++) {


            edited += '"' + i + '":{"alumno":"'+alumno+'","nota":"' + check[i].val() + '","id":"' + check[i].attr('id') + '","ida":"' + id + '","s":"' + s + '","asignatura":"'+check[i].attr('name')+'","diast":"'+diast+'","diasi":"'+diasi+'","obs":"'+obs+'"},';


        }
        edited = edited.slice(0, -1);
        edited += "}";
        console.log(edited);

        $.ajax(
            {
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: '../../../../guardanotasprenuevoeditar/',
                data: edited,
                beforeSend: function (data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function (data) {


                    if (data.response == 'errorsesion') {
                        $('#contenido').append('<div class="error mensajes">El registro que intenta insertar, ya existe</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });

                    }
                    if (data.response == 'errorinserta') {
                        $('#contenido').append('<div class="error mensajes">Ocurrio un error al guardar las notas,porfavor intente nuevamente</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });

                    }
                    else {
                        //console.log(data);
                        window.location.replace(data.redirect);
                        //$('#contenido').append('<div class="error mensajes">El registro que intenta insertar, ya existe</div>');
                    }


                },
                error: function (data) {
                    //window.location.replace(data.redirect);
                    //alert('Error : ' + strError);
                },
                complete: function (requestData, exito) {
                }
            });


        e.stopImmediatePropagation();
    });

    function limpiartexto(texto) {
        return texto.trim().replace(/[^a-z0-9.,;:{}´¨*+`^ºª!"@·#$¢%∞&¬/÷(“)”=≠?´¿‚¡'<>≤≥áéíóúñ\s][\t+?]+/ig, " ").replace(/\s\s+/g, " ").replace(/\s*[\r\n][\r\n \t ]*/g, " ").replace(/\s*[""]["]*/g, "<comilla>").replace(/\r\n+|\r+|\n+|\t+/g, " ");
    }
});
