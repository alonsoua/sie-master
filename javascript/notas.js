$(function () {


    $('#coef').live('change', function (e) {

        if ($(this).val() != 'Null') {
            if ($('#alumnos').find('table').length == 0) {
                var curso = $('#Cursos_idCursos').val();
                var ajax = $.ajax({
                    type: "GET",
                    url: "getalumnos",
                    async: true,
                    dataType: "json",
                    beforeSend: function (x) {
                        if (x && x.overrideMimeType) {
                            x.overrideMimeType("application/j-son;charset=UTF-8");
                        }
                    },
                    success: function (data) {
                        if (data == '' || data == 'Null') {
                            $('#alumnos').html('<div class="info">No existen alumnos en éste curso</div>');

                        } else {

                            var options = '<table class="striped sortable"><th>Nº</th><th style="text-align:left;">Alumnos</th><th style="text-align:left;">Nota</th>';
                            for (var i = 0; i < data.length; i++) {
                                options += '<tr><td>'+(i+1)+'</td><td><input type="hidden" value="' + data[i].idAlumnos + '"  />' + data[i].apaterno + ' ' + data[i].amaterno + ' ' + data[i].nombres + '</td><td><input  onpaste="return false" type="text" size="5" maxlength="2" class="notas" id="' + data[i].idAlumnos + '"</td></tr>';
                            }
                            options += '</table><input type="button" value="Guardar" id="guardarnotas">';
                            $('#alumnos').html(options);
                        }
                    }
                });
            }
        } else {
            $('#alumnos').html('');
        }
        e.stopImmediatePropagation();

    });


    //si presiona boton guardar
    $("#guardarnotas").live('click', function (e) {

        //obtenemos valor del curso
        var asignatura = $('#idAsignatura').val();
        var tipo= $('#idAsignatura').find(":selected").attr('class');
        var contenido = $('#conte').val();
        var tipoevaluacion = $('#tipoEvaluacionPrueba').val();
        var fecha = $('#fecha').val();
        var coef = $('#coef').val();


        //Obtenemos el valor de los check seleccionados
        var check;
        check = $('.notas').map(function () {
            return $(this);
        }).get();

        if (asignatura == '' || asignatura == 'Null' || asignatura < 0) {
            $('#idAsignatura').focus();
            var $re = $('#idAsignatura'),
                x = 4000;
            $re.css("background", "#FFBABA");
            setTimeout(function () {
                $re.css("background", "white");
            }, x);
            return false;

        }
        if (tipoevaluacion == '' || tipoevaluacion == 'Null' || tipoevaluacion < 0) {
            $('#tipoEvaluacionPrueba').focus();
            var $re = $('#tipoEvaluacionPrueba'),
                x = 4000;
            $re.css("background", "#FFBABA");
            setTimeout(function () {
                $re.css("background", "white");
            }, x);
            return false;

        }

        if (coef == '' || coef == 'Null' || coef < 0) {
            $('#coef').focus();
            var $re = $('#coef'),
                x = 4000;
            $re.css("background", "#FFBABA");
            setTimeout(function () {
                $re.css("background", "white");
            }, x);
            return false;

        }

        if (contenido == '' || contenido == 'Null' || contenido == null) {
            $('#conte').focus();
            var $re = $('#conte'),
                x = 4000;
            $re.css("background", "#FFBABA");
            setTimeout(function () {
                $re.css("background", "white");
            }, x);
            return false;

        }

        if (fecha == '' || fecha == 'Null' || fecha == null) {
            $('#fecha').focus();
            var $re = $('#fecha'),
                x = 4000;
            $re.css("background", "#FFBABA");
            setTimeout(function () {
                $re.css("background", "white");
            }, x);
            return false;

        }


        if (check == '' || check == 'Null') {

            return false;

        }


        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        for (var i = 0; i < check.length; i++) {
            if (check[i].val().length == '1') {
                var valoragregado = check[i].val();
                valoragregado += '0';
                edited += '"' + i + '":{"asignatura":"' + asignatura + '","nota":"' + valoragregado + '","alumno":"' + check[i].attr('id') + '","contenido":"' + contenido + '","tipoevaluacion":"' + tipoevaluacion + '","fecha":"' + fecha + '","coef":"' + coef + '","tipo":"' + tipo + '"},';


            } else {

                edited += '"' + i + '":{"asignatura":"' + asignatura + '","nota":"' + check[i].val().toString().replace(/\,/g, "") + '","alumno":"' + check[i].attr('id') + '","contenido":"' + contenido + '","tipoevaluacion":"' + tipoevaluacion + '","fecha":"' + fecha + '","coef":"' + coef + '","tipo":"' + tipo + '"},';

            }

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
                url: 'guardanotas/',
                data: edited,
                beforeSend: function (data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function (data) {
                    //alert(data.redirect);


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


});