$(function () {


    $('#asistencia').html('');
    //obtenemos los dias que ya estan regitrados

    var ajax = $.ajax({
        type: "GET",
        url: "getdiascalendario/t/2",
        async: true,
        dataType: "json",
        beforeSend: function (x) {
            if (x && x.overrideMimeType) {
                x.overrideMimeType("application/j-son;charset=UTF-8");
            }
        },
        success: function (data) {
            $('#fechaAsistencia').datepicker('destroy');
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
                    $("#fechaAsistencia").datepicker({
                        showOn: "button",
                        buttonImage: "../calendario/css/images/calendar.gif",
                        buttonImageOnly: true,
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

    if ($(this).val() != 'Null') {

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
                if (data[0] == '' || data[0] == 'Null') {
                    $('#asistencia').html('<div class="info datos">No existen alumnos en éste curso</div>');

                } else {
                    $('#asistencia').find('.datos').remove();

                    var options = '<table class="striped sortable"><thead><tr><th>Nº</th><th>Rut</th></th><th>Alumnos</th><th>Asistencia</th><th>Observación</th></tr></thead>';
                    for (var i = 0; i < data[0].length; i++) {
                        options += '<tr><td>' + (i + 1) + '</td><td>' + data[0][i].rutAlumno + '</td></td><td><input type="hidden" value="' + data[0][i].idAlumnos + '"  />' + data[0][i].nombres + ' ' + data[0][i].apaterno + ' ' + data[0][i].amaterno + '</td><td><input type="checkbox"  class="asignatura" id="' + data[0][i].idAlumnos + '"</td><td><select class="observacion"></select></td></tr>';
                    }
                    options += '</table><input type="button" class="button medium blue" value="Guardar" id="guardarasistencia">';

                    $('#asistencia').append('<div style="position:relative" class="alerta mensajes">Nota: Marque los ausentes</div>');

                    $('#asistencia').append(options);


                    var cat = '<table class="striped sortable"><thead><tr><th>Nº</th><th>Rut</th></th><th>Alumnos</th><th>Opción</th><th>Observación</th></tr></thead>';
                    for (var i = 0; i < data[1].length; i++) {
                        if (data[1][i].codigo == 13288) {
                            cat += '<option selected value="' + data[1][i].codigo + '">' + data[1][i].descripcion + '</option>';
                        } else {
                            cat += '<option value="' + data[1][i].codigo + '">' + data[1][i].descripcion + '</option>';
                        }

                    }

                    $('.observacion').append(cat);


                }
            }
        });
    } else {
        $('#asistencia').html('');
    }

    $('#fechaAsistencia').live('change', function (e) {
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
                    $('#asistencia').prepend('<div class="info datos">No existen asignaturas para la fecha seleccionada</div>');

                } else {
                    $('#asistencia').find('.datos').remove();

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
        var fecha = $("#fechaAsistencia").val();

        $.ajax({
            type: "GET",
            url: "getbloque/date/" + fecha + "/id/" + asignatura + "/t/2",
            async: true,
            dataType: "json",
            beforeSend: function (x) {
                if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                }
            },
            success: function (data) {
                if (data == '' || data == 'Null') {
                    $('#bloque').empty();
                    $('#bloque').trigger("chosen:updated")
                    $('#asistencia').prepend('<div class="info datos">Ya existen Registros Para esta Asignatura</div>');

                } else {

                    $('#asistencia').find('.datos').remove();

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
        function () {
            return this.each(function () {
                $(this).keydown(function (e) {
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

    $("#guardarasistencia").live('click', function (e) {

        //obtenemos valor del curso

        var fecha = $('#fechaAsistencia').val();
        var curso = $('#idCursos').val();
        var asignatura = $('#idAsignatura').val();
        var bloque = $('#bloque').val();
        var tipo = $('input[name="tipo"]:checked').val();


        if (fecha == '' || fecha == 'Null') {
            return false;

        }

        if (curso == '' || curso == 'Null') {

            return false;

        }

        if (bloque == '' || bloque == 'Null' || bloque == null) {

            return false;

        }

        var valores;
        valores = $('input[type=checkbox]').map(function () {
            return $(this);
        }).get();
        var categorias;
        categorias = $('.observacion').map(function () {
            return $(this).val();
        }).get();

        var edited = "{";

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
            cancelButtonText: 'Cancelar',
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
                            url: 'validafirma/id/'+value,
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
                valid = true;

                edited += '"0":{"token":"' + result.value.token + '","curso":"' + curso + '","asignatura":"' + asignatura + '","horario":"' + bloque + '","tipo":"'+tipo+'","fecha":"' + fecha + '","valores":{';
                for (var i = 0; i < valores.length; i++) {
                    if ($(valores[i]).is(':checked')) {
                        edited += '"' + [i] + '":{ "alumno":"' + valores[i].attr('id') + '","valorasistencia":"1","categoria":"' + categorias[i] + '"},';
                    } else {
                        edited += '"' + [i] + '":{ "alumno":"' + valores[i].attr('id') + '","valorasistencia":"2","categoria":"' + categorias[i] + '"},';
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
