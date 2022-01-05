$(function () {

    //si presiona boton guardar
    $("#cambiar").live('click', function (e) {

        //obtenemos valor del curso

        var id = $(this).attr('name');
        var valor = $(this).closest('td').prev('td').find('input').val();
        var tipo = $("#tip").val();

        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        if (tipo != 5) {

        if (valor.length == '1') {
            var valoragregado = valor;
            valoragregado += '0';
            edited += '"0":{"id":"' + id + '","valor":"' + valoragregado + '"},';
        } else {
            edited += '"0":{"id":"' + id + '","valor":"' + valor.toString().replace(/\,/g, "") + '"},';
        }
    }if (tipo==5){
            edited += '"0":{"id":"' + id + '","valor":"' + valor + '"},';
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
                url: '../../guardanotaseditar/',
                data: edited,
                beforeSend: function (data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function (data) {
                    //alert(data.redirect);


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
                    if (data.response == 'exito') {
                        $('#contenido').append('<div class="exito mensajes">Registro Actualizado con éxito</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".exito").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });
                    }


                },
                error: function (data) {
                    $('#contenido').append('<div class="error mensajes">Se ha producido un error, intente nuevamente 2</div>');
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

//si presiona boton guardar 
    $("#cambiarnuevo").live('click', function (e) {

        //obtenemos valor del curso

        var id = $(this).attr('name');
        var valor = $(this).closest('td').prev('td').find('input').val();
        var as = $('#as').val();
        var cu = $('#cu').val();

        var al = $(this).closest('tr').find("td:first-child input").val();


        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        if (valor.length == '1') {
            var valoragregado = valor;
            valoragregado += '0';
            edited += '"0":{"ev":"' + id + '","valor":"' + valoragregado + '","as":"' + as + '","cu":"' + cu + '","al":"' + al + '"},';
        } else {
            edited += '"0":{"ev":"' + id + '","valor":"' + valor.toString().replace(/\,/g, "") + '","as":"' + as + '","cu":"' + cu + '","al":"' + al + '"},';
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
                url: '../../guardanotaseditarnuevo/',
                data: edited,
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
                        //console.log(data);
                        window.location.replace(data.redirect);
                        //$('#contenido').append('<div class="error mensajes">El registro que intenta insertar, ya existe</div>');
                    }


                },
                error: function (data) {
                    $('#contenido').append('<div class="error mensajes">Se ha producido un error, intente nuevamente 2</div>');
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


});
