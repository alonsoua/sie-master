$(function () {


    //si presiona boton guardar
    $("#guardarasistencia").live('click', function (e) {

        var ida=$('#ida').val();

        //Obtenemos el valor de los check seleccionados
        var check;
        var asistencia = new Array();
        check = $('.cambiar').map(function () {
            return $(this);
        }).get();
        var datos;
        for (var i = 0; i < check.length; i++) {

            if (check[i].is(':checked')) {
                asistencia[i] = {valor: 1};

            } else {
                asistencia[i] = {valor: 2};
            }

        }
        datos={ida:ida,asistencia:asistencia};

        $.ajax(
            {
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: '../../guardaasistenciaeditar/',
                data: JSON.stringify(datos),
                beforeSend: function (data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function (data) {
                    if (data.response == 'errorinserta') {
                        $('#contenido').append('<div class="error mensajes">Ha ocurrido un error al insertar los datos,intente nuevamente</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });

                    }
                    if (data.response == 'errorsesion') {
                        $('#contenido').append('<div class="error mensajes">Ha ocurrido un error de sesion,porfavor actualize la página</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });

                    }
                    else {
                        window.location.replace(data.redirect);
                    }


                },
                error: function (data) {
                    $('#contenido').append('<div class="error mensajes">Se ha producido un error, intente nuevamente </div>');
                    $(document).ready(function () {
                        setTimeout(function () {
                            $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                        }, 3000);
                    });
                },
                complete: function (requestData, exito) {
                }
            });


        e.stopImmediatePropagation();
    });

    $(".asignatura").live('click', function (e) {

        if ($(this).is(':checked')) {
            var presente = parseInt($('#presentes').html());
            $('#presentes').html(presente - 1);
            var ausente = parseInt($('#ausentes').html());
            $('#ausentes').html(ausente + 1);

        } else {
            var presente = parseInt($('#presentes').html());
            $('#presentes').html(presente + 1);
            var ausente = parseInt($('#ausentes').html());
            $('#ausentes').html(ausente - 1);

        }

        e.stopImmediatePropagation();
    });


});
