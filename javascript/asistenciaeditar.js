$(function () {

    $('input[name="tipo"]').live('change', function (e) {
        var elemento = $(this).val();
        $('input[type=checkbox]').map(function () {

            if (elemento == 1) {
                if (!$(this).is(':checked')) {

                    $(this).closest('td').next('td').find('select').val(13288);
                }

            } else {

                if (!$(this).is(':checked')) {

                    $(this).closest('td').next('td').find('select').val(13291);
                }
            }


        }).get();


        e.stopImmediatePropagation();
    });

    $('.asistencia').live('click', function (e) {

        var categoria = 0;

        var elemento = $(this).closest('td').next('td').find('select');

        if ($(this).is(':checked')) {
            categoria = 1;

        } else {
            categoria = 2;
        }

        if (categoria > 0) {
            $.ajax({
                type: "GET",
                url: "../../getcategoria/id/" + categoria,
                async: true,
                dataType: "json",
                beforeSend: function (x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function (data) {
                    if (data == '' || data == 'Null') {
                        $('#asistencia').prepend('<div class="info">No existen datos</div>');

                    } else {

                        var tipo = $('input[name="tipo"]:checked').val();
                        var cat = '';

                        for (var i = 0; i < data.length; i++) {
                            if (categoria == 1) {
                                if (data[i].codigo == 13303) {
                                    cat += '<option selected value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
                                } else {
                                    cat += '<option value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
                                }
                            }

                            if (categoria == 2) {


                                if(tipo==1){

                                    if (data[i].codigo == 13288) {
                                        cat += '<option selected value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
                                    } else {
                                        cat += '<option value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
                                    }

                                }else{

                                    if (data[i].codigo == 13291) {
                                        cat += '<option selected value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
                                    } else {
                                        cat += '<option value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
                                    }
                                }

                            }


                        }

                        $(elemento).html(cat);


                    }
                }
            });
        }


        e.stopImmediatePropagation();
    });

    jQuery.fn.ForceNumericOnly =
        function()
        {
            return this.each(function()
            {
                $(this).keydown(function(e)
                {
                    var key = e.charCode || e.keyCode || 0;
                    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
                    // home, end, period, and numpad decimal
                    return (
                        key == 8 ||
                        key == 9 ||
                        key == 13 ||
                        key == 46 ||
                        key == 110 ||
                        key == 190 ||
                        (key >= 35 && key <= 40) ||
                        (key >= 48 && key <= 57) ||
                        (key >= 96 && key <= 105));
                });
            });
        };


    //si presiona boton guardar
    $("#guardarasistencia").live('click', function (e) {

        var id=$('#idcontrol').val();
        var tipo = $('input[name="tipo"]:checked').val();

        //Obtenemos el valor de los check seleccionados
        var check;
        var asistencia = new Array();
        check = $('.cambiar').map(function () {
            return $(this);
        }).get();


        //Obtenemos el valor de todos los Select de Asistencia
        var categorias;
        categorias = $('.observacion').map(function () {
            return $(this).val();
        }).get();

        Swal.fire({
            title: 'Firma Digital',
            text: 'Ingrese su Código de 6 Dígitos',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: '6'

            },
            showCancelButton: true,
            confirmButtonText: 'Firmar',
            cancelButtonText:'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: (value) => {

                var regex = /^[0-9-+()]*$/;

                if (value == '' || value == null || value.length < 6 || !regex.test(value)) {

                    swal.showValidationMessage('Firma no Válida');
                    e.preventDefault();

                }else{

                    return new Promise(function(resolve,reject) {
                        $.ajax({
                            global:false,
                            async: true,
                            dataType: "json",
                            url: '../../validafirma/id/'+value,
                            type: 'GET',
                        })
                            .done(function(data) {
                                if(data.ok){
                                    data['token']=value;
                                    resolve(data)
                                }else{

                                    reject(data)
                                }

                            })
                            .fail(function(data) {
                                reject(data)
                            });

                    })
                }


            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value.ok) {
                valid=true;

                var datos;
                for (var i = 0; i < check.length; i++) {

                    if (check[i].is(':checked')) {
                        asistencia[i] = {valor: 1,idca: check[i][0].value};

                    } else {
                        asistencia[i] = {valor: 2,idca: check[i][0].value};
                    }

                }
                datos={id:id,tipo:tipo,asistencia:asistencia,cat:categorias,token:result.value.token};

                $.ajax(
                    {
                        cache: false,
                        async: true,
                        dataType: 'json',
                        type: 'POST',
                        contentType: 'application/x-www-form-urlencoded',
                        url: '../../guardacontrolasistenciaeditar/',
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

                            } else {
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




            }
        }).catch((result) => {

            swal.fire({
                title: "Validación",
                text: result.message,
                confirmButtonText:'Intentar de nuevo',
                showCancelButton: true,
                type: 'error',
                cancelButtonColor: '#d33',
            })
                .then((res) => {
                    if(res.value){
                        $('#guardarasistencia').trigger('click');
                    }
                });
        });

        $('.swal2-input').ForceNumericOnly();




        e.stopImmediatePropagation();
    });


});
