$(function () {


    $('#idCursos').live('change', function (e) {
        $('#asistencia').html('');
        $('#fecha').val('');
        var ajax = $.ajax({
            type: "GET",
            url: "getdias/id/" + $(this).val(),
            async: true,
            dataType: "json",
            beforeSend: function (x) {
                if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                }
            },
            success: function (data) {
                $('#fecha').datepicker('destroy');
                var disabledDays = new Array();
                for (var i = 0; i < data.length; i++) {
                    disabledDays[i] = data[i].fechaAsistencia;
                }


                var ano = $('#periodo').val();
                var fechain = '01-01-' + ano;
                var fechate = '31-12-' + ano;
                $("#fecha").datepicker({
                    minDate: fechain,
                    maxDate: 0,
                    dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
                    dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
                    firstDay: 1,
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                    monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    beforeShowDay: noSunday
                });

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

                    return [$return, $returnclass];
                }

                function noSunday(date) {
                    var noWeekend = $.datepicker.noWeekends(date);

                    return noWeekend[0] ? ocupados(date) : noWeekend;
                }

            }
        });

        e.stopImmediatePropagation();
    });

    $('#fecha').live('change', function (e) {
        $('#asistencia').html('');
        var curso = $('#idCursos').val();
        var ajax = $.ajax({
            type: "GET",
            url: "getalumnos/id/" + curso,
            async: true,
            dataType: "json",
            beforeSend: function (x) {
                if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                }
            },
            success: function (data) {

                if (data == '' || data == 'Null') {
                    $('#asistencia').html('<div class="info">No existen alumnos en éste curso</div>');

                } else {
                    $('#asistencia').html('');

                    var options = '<table class="striped sortable"><thead><tr><th>Nº</th><th>Rut</th></th><th>Alumnos</th><th>Opción</th></tr></thead>';
                    for (var i = 0; i < data.length; i++) {
                        options += '<tr><td>' + (i + 1) + '</td><td>' + data[i].rutAlumno + '</td></td><td><input type="hidden" value="' + data[i].idAlumnos + '"  />' + data[i].apaterno + ' ' + data[i].amaterno + ' ' + data[i].nombres + '</td><td><input type="checkbox"  class="asignatura" id="' + data[i].idAlumnos + '"</td></tr>';
                    }
                    options += '<tr><th>Presentes</th><th>Ausentes</th><th colspan="2">Matriculas</th></tr>';
                    options += '<tr><td id="presentes" style="text-align: right">' + data.length + '</td><td id="ausentes" style="text-align: right">0</td><td style="text-align: right" colspan="2">' + data.length + '</td></tr>';
                    options += '</table><input type="button" value="Guardar" id="guardarasistencia">';

                    $('#asistencia').html('<div style="position:relative" class="info mensajes">Nota: Marque los ausentes <span style="position:absolute;top:3px;left:95%;"><a id="cerrarinfo" style="cursor:pointer;"><i class="icon-remove"></i>Cerrar</a></span></div>');

                    $('#asistencia').html(options);

                }
            }
        });
        e.stopImmediatePropagation();
    });


    $("#cerrarinfo").live('click', function (e) {


        $(this).closest('div').remove();


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


    //si presiona boton guardar
    $("#guardarasistencia").live('click', function (e) {

        //obtenemos valor del curso

        var fecha = $('#fecha').val();
        var curso = $('#idCursos').val();


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
        edited += '"0":{"curso":"' + curso + '","fecha":"' + fecha + '","valores":{';
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
        console.log(edited);


        $.ajax(
            {
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: 'guardaasistencia/',
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
