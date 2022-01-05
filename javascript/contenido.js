$(function () {


    $('#asistencia').html('');
    //obtenemos los dias que ya estan regitrados

    var ajax = $.ajax({
        type: "GET",
        url: "getdiascalendario/t/1",
        async: true,
        dataType: "json",
        beforeSend: function (x) {
            if (x && x.overrideMimeType) {
                x.overrideMimeType("application/j-son;charset=UTF-8");
            }
        },
        success: function (data) {
            $('#fechaContenido').datepicker('destroy');
            var disabledDays = new Array();
            for (var i = 0; i < data[1].length; i++) {
                disabledDays[i] = data[1][i].fechaEvento;
            }
            //Disable days of weeks
            var disabledDaysofweek = new Array();
            for (var i = 0; i < data[2].length; i++) {
                disabledDaysofweek[i] = data[2][i].dia;
            }

            //Complete days

            var dayscomplete = new Array();
            for (var i = 0; i < data[3].length; i++) {
                dayscomplete[i] = data[3][i].fechaCompleta;
            }


            if (data[0]) {
                if (typeof data[0][0]['fechaInicioClase'] !== 'undefined' && typeof data[0][0]['fechaTerminoClase'] !== 'undefined') {
                    $("#fechaContenido").datepicker({
                        minDate: data[0][0]['fechaInicioClase'],
                        maxDate: data[0][0]['fechaTerminoClase'],
                        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                        dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                        firstDay: 1,
                        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                        dateFormat: 'dd-mm-yy',
                        changeMonth: true,
                        beforeShowDay: noSunday
                    });
                }
            }

            function ocupados(mydate) {
                var $return = true;
                var $returnclass = "available";
                $checkdate = $.datepicker.formatDate('yy-mm-dd', mydate);

                for (var i = 0; i < disabledDays.length; i++) {
                    if (disabledDays [i] == $checkdate) {
                        $return = false;
                        $returnclass = "unavailable";
                    }

                }

                for (var i = 0; i < dayscomplete.length; i++) {
                    if (dayscomplete [i] == $checkdate) {
                        $return = false;
                        $returnclass = "complete";
                    }

                }
                return [$return, $returnclass];
            }

            function noSunday(date) {

                var a = [0, 1, 2, 3, 4, 5, 6];

                for (i = 0; i < disabledDaysofweek.length; i++) {
                    a = a.filter(item => item !== parseInt(disabledDaysofweek[i]))
                }
                var weekend = !($.inArray(date.getDay(), a) > -1);
                return weekend ? ocupados(date) : weekend;
            }

        }
    });



    $('#fechaContenido').live('change', function (e) {
        $.ajax({
            type: "GET",
            url: "getasignaturabloque/date/" + $(this).val(),
            async: true,
            dataType: "json",
            beforeSend: function (x) {
                if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                }
            },
            success: function (data) {
                if (data == '' || data == 'Null') {
                    $('#asistencia').html('<div class="info">No existen asignaturas para la fecha seleccionada</div>');

                } else {

                    $('#bloque').empty();
                    $('#bloque').trigger("chosen:updated")
                    $('#idAsignatura').empty();

                    var options = '';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].idAsignatura + '">' + data[i].nombreAsignatura + '</option>';
                    }
                    $('#idAsignatura').html(options);
                    $('#idAsignatura').trigger("change");

                }


            }
        });


        e.stopImmediatePropagation();
    });

    $('#idAsignatura').trigger("change");

    $('#idAsignatura').live('change', function (e) {
        var asignatura = $("#idAsignatura").val();
        var fecha = $("#fechaContenido").val();

        $.ajax({
            type: "GET",
            url: "getbloque/date/" + fecha + "/id/" + asignatura+"/t/1",
            async: true,
            dataType: "json",
            beforeSend: function (x) {
                if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                }
            },
            success: function (data) {
                if (data == '' || data == 'Null') {
                    $('#asistencia').html('<div class="info">No existen Datos</div>');
                    $('#bloque').empty();
                    $('#bloque').trigger("chosen:updated")

                } else {

                    $('#asistencia').find('.info').remove();

                    var options = '';
                    for (var i = 0; i < data.length; i++) {
                        options += '<option value="' + data[i].idHorario + '">' + data[i].tiempoInicio + '-' + data[i].tiempoTermino + '</option>';
                    }
                    $('#bloque').html(options);
                    $('#bloque').trigger("chosen:updated")


                }
            }
        });

        e.stopImmediatePropagation();
    });

    $('.asignatura').live('click', function (e) {


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
                url: "getcategoria/id/" + categoria,
                async: true,
                dataType: "json",
                beforeSend: function (x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function (data) {
                    if (data == '' || data == 'Null') {
                        $('#asistencia').html('<div class="info">No existen datos</div>');

                    } else {
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
                                if (data[i].codigo == 13288) {
                                    cat += '<option selected value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
                                } else {
                                    cat += '<option value="' + data[i].codigo + '">' + data[i].descripcion + '</option>';
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


    //si presiona boton guardar
    $("#guardarasistencia").live('click', function (e) {

        //obtenemos valor del curso

        var fecha = $('#fechaAsistencia').val();
        var curso = $('#idCursos').val();
        var asignatura = $('#idAsignatura').val();
        var bloque = $('#bloque').val();


        if (fecha == '' || fecha == 'Null') {
            $('#fecha').focus();
            var $re = $('#fecha'),
                x = 4000;
            $re.css("background", "#FFBABA");
            setTimeout(function () {
                $re.css("background", "white");
            }, x);
            return false;

        }

        if (curso == '' || curso == 'Null') {

            return false;

        }


        //Obtenemos el valor de todos los checks
        var valores;
        valores = $('input[type=checkbox]').map(function () {
            return $(this);
        }).get();


        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        edited += '"0":{"curso":"' + curso + '","asignatura":"' + asignatura + '","horario":"' + bloque + '","fecha":"' + fecha + '","valores":{';
        for (var i = 0; i < valores.length; i++) {
            if ($(valores[i]).is(':checked')) {
                edited += '"' + [i] + '":{ "alumno":"' + valores[i].attr('id') + '","ausencia":"1"},';
            } else {
                edited += '"' + [i] + '":{ "alumno":"' + valores[i].attr('id') + '","ausencia":"2"},';
            }
        }
        edited = edited.slice(0, -1);
        edited += "}},";
        edited = edited.slice(0, -1);
        edited += "}";

        $.ajax(
            {
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: 'guardarcontrolasistencia/',
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

                    } else {
                        window.location.replace(data.redirect);
                        //$('#contenido').append('<div class="error mensajes">El registro que intenta insertar, ya existe</div>');
                    }


                },
                error: function (data) {
                    $('#contenido').append('<div class="error mensajes">Se ha producido un error, intente nuevamente</div>');
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
