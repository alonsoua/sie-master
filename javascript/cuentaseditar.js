$(window).load(function () {
    $('#periodo').trigger("change");
});
$(function () {

    $('#cargo').live('change', function (e) {
        if ($('#periodo').val() != '' && $('#idEstablecimiento').val() != '') {
            $('#idEstablecimiento').trigger("change");
            e.stopImmediatePropagation();
        }
    });
    $('#periodo').live('change', function (e) {
        if ($('#cargo').val() != '' && $('#idEstablecimiento').val() != '') {
            $('#idEstablecimiento').trigger("change");
        }
        e.stopImmediatePropagation();
    });


    //si cambia el select de Establecimiento
    $('#idEstablecimiento').live('change', function (e) {
        if ($('#cargo').val() != '' && $('#periodo').val() != '') {
            $('#asignatura').html('');
            $('#guardar').html('');
            var a = $('#cargo').val();
            if (a == '3' || a == '4' || a == '5' || a == '6') {
                var options2 = '<input type="button" value="Guardar" id="guardarusuario">';
                //options+='<ul><li><input type="checkbox" name="" class="canine" id="" /> American Water Spaniel </li><li><input type="checkbox" name="" class="canine" id="" /> Brittany </li></ul></li>';
                $('#asignatura').html(options2);
                $('#idEstablecimiento-label').show();
                $('#idEstablecimiento').show();
            }
            if (a == '1') {
                $('#idEstablecimiento-label').hide();
                $('#idEstablecimiento').hide();
                var options2 = '<input type="button" value="Guardar" id="guardarusuario">';
                $('#asignatura').html(options2);
            }
            if (a == '2') {
                $('#idEstablecimiento-label').show();
                $('#idEstablecimiento').show();
                var id = $('#idEstablecimiento').val();
                var idu = $('#idCuenta').val();
                var user = $('#usuario').val();
                var periodo = $('#periodo').val();

                if ($(this).val() != '' && $('#periodo').val() != '') {
                    var ajax = $.ajax({
                        type: "GET",
                        url: "../../../../getcurso/id/" + id + "/idp/" + $('#periodo').val(),
                        async: true,
                        dataType: "json",
                        beforeSend: function (x) {
                            if (x && x.overrideMimeType) {
                                x.overrideMimeType("application/j-son;charset=UTF-8");
                            }
                        },
                        success: function (data) {
                            if (data.length > 0) {
                                if (data[0].ingresonota == 2) {

                                    var options = '<ul class="span4" style="margin:20px 0 20px 0;"><li><input type="checkbox" id="all" name="check-all" /> Seleccionar Todos </li>';
                                    for (var i = 0; i < data.length; i++) {
                                        if (data[i].asignatura.length > 0) {

                                            options += '<fieldset class="group" style="overflow-x:auto;" ><legend>' + data[i].nombreGrado + ' ' + data[i].letra + '</legend><ul><li class="parent-list"><input type="checkbox" id="cursoss"  class="canine parent" /> Seleccionar Asignaturas </li><div name="' + data[i].idCursos + '"><ul>';

                                            for (var j = 0; j < data[i].asignatura.length; j++) {
                                                options += '<li><input type="checkbox" value="' + data[i].asignatura[j].idAsignaturaCurso + '"  id="hijos" class="canine" />' + data[i].asignatura[j].nombreAsignatura + '</li>';
                                            }
                                            options += '</ul><input type="hidden" class="cuentacurso" value="" /></div></fieldset>';
                                        }

                                    }

                                    options += '</ul>';
                                    var options2 = '<input type="hidden" value="2" id="tipo" /><input type="button" value="Guardar" id="guardarusuario">';
                                    $('#asignatura').html(options);
                                    $('#guardar').html(options2);
                                    //obtenemos las asignaturas que posee el usuario y marcamos los checkbox
                                    var ajax2 = $.ajax({
                                        type: "GET",
                                        url: "../../../../getnivelusuario/id/" + idu + "/idp/" + $('#periodo').val() + "/ide/" + $('#idEstablecimiento').val(),
                                        async: true,
                                        dataType: "json",
                                        beforeSend: function (x) {
                                            if (x && x.overrideMimeType) {
                                                x.overrideMimeType("application/j-son;charset=UTF-8");
                                            }
                                        },
                                        success: function (data) {
                                            for (var i = 0; i < data.length; i++) {
                                                $('div[name=' + data[i].idCursos + ']').find('.cuentacurso').val(data[i].idCuentaCurso);
                                                for (var j = 0; j < data[i].asignaturas.length; j++) {
                                                    $('div[name=' + data[i].idCursos + ']').find('input[type=checkbox][value=' + data[i].asignaturas[j] + ']').val(data[i].asignaturas[j]).prop("checked", "checked").addClass("cr").closest('li').css("background-color", "green");
                                                }
                                            }
                                        }
                                    });
                                } else {
                                    $('#guardar').html('<input type="hidden" value="1" id="tipo" /><input type="button" value="Guardar" id="guardarusuario">');
                                }
                            } else {
                                $('#guardar').html('');
                            }
                        }
                    });

                } else {
                    $('#asignatura').html('');
                    $('#guardar').html('');
                }
            }
            e.stopImmediatePropagation();
        }
    });
});

function eliminateDuplicates(arr) {
    var i,
        len = arr.length,
        out = [],
        obj = {};
    for (i = 0; i < len; i++) {
        obj[arr[i]] = 0;
    }
    for (i in obj) {
        out.push(i);
    }
    return out;
}

//si presiona boton guardar
$(document).ready(function () {
    $("#guardarusuario").live('click', function () {
        var idu = $('#idCuenta').val();
        var rbd = $('#idEstablecimiento').val();
        var nombres = $('#usuario').val();
        var nombrereal = $('#nombrescuenta').val();
        var paterno = $('#paternocuenta').val();
        var materno = $('#maternocuenta').val();
        var correo = $('#correo').val();
        var periodo = $('#periodo').val();
        var cargo = $('#cargo').val();
        var tipo = $('#tipo').val();
        var cursos;
        var niveles = new Array();
        var selected = [];
        var selectedeliminados = [];
        if (periodo == '' || periodo == 'Null' || rbd == '' || rbd == 'Null' || nombrereal == '' || nombrereal == 'Null' || nombres == '' || nombres == 'Null' || cargo == '' || cargo == 'Null') {
            alert('Existen datos sin completar, por favor rellene los campos');
            return false;
        } else {
            if (cargo == '3' || cargo == '4' || cargo == '5' || cargo == '6' || (cargo == '2' && tipo == '1')) {
                var edited = "{";
                edited += '"0":{"nombre":"' + idu + '","nombrereal":"' + nombrereal + '","paterno":"' + paterno + '","materno":"' + materno + '","correo":"' + correo + '","rbd":"' + rbd + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '"}';
                edited += "}";
            }
            if (cargo == '1') {
                var edited = "{";
                edited += '"0":{"nombre":"' + idu + '","nombrereal":"' + nombrereal + '","paterno":"' + paterno + '","materno":"' + materno + '","correo":"' + correo + '","rbd":"0","cargo":"' + cargo + '","periodo":"' + periodo + '"}';
                edited += "}";

            }
            if (cargo == '2' && tipo == '2') {
                //obtenemos los cursos
                cursos = $('input[id=hijos]:checked ').map(function () {
                    return $(this).parents("div:first").attr('name');
                }).get();
                var cursoslimpios = eliminateDuplicates(cursos);
                for (var i = 0; i < cursoslimpios.length; i++) {
                    //obtenemos los niveles
                    $('div[name=' + cursoslimpios[i] + ']').map(function () {
                        return niveles[i] = $(this).attr('class');
                    }).get();
                    selected[cursoslimpios[i]] = $('div[name="' + cursoslimpios[i] + '"] input[type=checkbox]:checked').each(function () {
                        return selected[cursoslimpios[i]] = $(this).val();
                    });
                }
                selectedeliminados = $('div #asignatura input.rmv:checkbox:not(:checked)').each(function () {
                    return selectedeliminados = $(this).val();
                });

                if(cursoslimpios.length>0){
                    //creamos el json con los datos para enviar y guardar
                    var edited = "{";
                    for (var i = 0; i < (cursoslimpios).length; i++) {
                        edited += '"' + [i] + '":{"nombre":"' + idu + '","nombrereal":"' + nombrereal + '","paterno":"' + paterno + '","materno":"' + materno + '","correo":"' + correo + '","rbd":"' + rbd + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '","curso":"' + cursoslimpios[i] + '","asignaturas":{';
                        for (var j = 0; j < (selected[cursoslimpios[i]]).length; j++) {
                            var valor = selected[cursoslimpios[i]][j].value;
                            edited += '"' + j + '":"' + valor + '",';
                        }
                        edited = edited.slice(0, -1);
                        edited += "}},";
                    }
                    edited = edited.slice(0, -1);
                    edited += "}";
                }else{
                    var edited = "{";
                    edited += '"0":{"nombre":"' + idu + '","nombrereal":"' + nombrereal + '","paterno":"' + paterno + '","materno":"' + materno + '","correo":"' + correo + '","rbd":"' + rbd + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '"}';
                    edited += "}";
                }


            }
            console.log(edited);
            $.ajax({
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: '../../../../guardaeditarusuario/',
                data: edited,
                beforeSend: function (data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function (data) {
                    if (data.response == 'error') {
                        $('#contenido').append('<div class="error mensajes">Se ha Producido un error, por favor intente nuevamnete</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });
                    }
                    else if (data.response == 'usuarioexiste') {
                        $('#contenido').append('<div class="error mensajes">El Usuario que intenta ingresar, ya existe</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });
                    } else {
                        window.location.replace(data.redirect);
                    }
                },
                error: function (requestData, strError, strTipoError) {
                    $('#contenido').append('<div class="error mensajes">Se ha Producido un error, sera redirigido</div>');
                    $(document).ready(function () {
                        setTimeout(function () {
                            $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                        }, 3000);
                    });
                },
                complete: function (requestData, exito) {
                }
            });
            return false;
        }
    });
});

// check-uncheck Todos
$(document).on('change', 'input[id="all"]', function () {
    if ($('.canine').closest('li').attr('style') == undefined) {
        $('.canine').closest('li').css("background-color", "green");
        $('.canine.rmv').removeClass("rmv").addClass("cr");
    } else {
        $('.canine').closest('li').removeAttr("style");
        $('.canine.cr').removeClass("cr").addClass("rmv");
    }
    $('.canine').prop("checked", this.checked);
});
// parent/child check-uncheck all
$(function () {
    $('.parent').live('click', function () {
        if ($(this).closest('ul li').find(':checkbox.canine').closest('li').attr('style') == undefined) {
            $(this).closest('ul li').find(':checkbox.canine').closest('li').css("background-color", "green");
            $(this).closest('ul li').find(':checkbox.rmv').removeClass("rmv").addClass("cr");
        } else {
            $(this).closest('ul li').find(':checkbox.canine').closest('li').removeAttr("style");
            $(this).closest('ul li').find(':checkbox.cr').removeClass("cr").addClass("rmv");
            $(this).addClass("rmv");
        }
        $(this).closest('ul li').find(':checkbox.canine').prop('checked', this.checked);
    });
});
$(document).on('change', 'input[class="canine cr"]', function () {
    $(this).removeClass("cr");
    $(this).addClass("rmv");
    $(this).closest('li').removeAttr("style");
});
$(document).on('change', 'input[class="canine rmv"]', function () {
    $(this).removeClass("rmv");
    $(this).addClass("cr");
    $(this).closest('li').css("background-color", "green");
});
$(document).on('change', 'input[class="canine"]', function () {
    if ($(this).closest('li').attr('style') == undefined) {
        $(this).closest('li').css("background-color", "green");
    } else {
        $(this).closest('li').removeAttr("style");
    }
});

// parent/child check-uncheck all
$(function () {
    $('.parent').live('click', function () {
        $(this).parent().next().find(':checkbox').prop('checked', this.checked);
    });
});
