$(function() {
    $('#cargo').live('change', function(e) {
        $('#guardar').html('');
        if ($(this).val() == 2 || $(this).val() == 3 || $(this).val() == 4 || $(this).val() == 6 ) {
            $('#rbd-label').show();
            $('#rbd').show();
            if ($('#rbd').val() != '' && $('#periodo').val() != '') {
                $('#periodo').trigger("change");
            } else {
                $('#rbd-label').show();
                $('#rbd').show();
            }
        } else {
            $('#rbd-label').hide();
            $('#rbd').hide();
            if ($('#periodo').val() != '') {
                $('#periodo').trigger("change");
            }
        }
        e.stopImmediatePropagation();
    });
    $('#rbd').live('change', function(e) {
        $('#guardar').html('');
        if ($('#cargo').val() != '' && $('#periodoo').val() != '') {
            $('#periodo').trigger("change");
        }
        e.stopImmediatePropagation();
    });
    //si cambia el select de Establecimiento
    $('#periodo').live('change', function(e) {
        $('#guardar').html('');
        $('#asignatura').html('');
        var id = '';
        if ($('#cargo').val() == 1) {
            id = 0;
        } else {
            id = $('#rbd').val();
        }
        if ($('#cargo').val() != '' && $(this).val() != '' && (id != '' || id != null)) {
            var idu = $('#idCuenta').val();
            var user = $('#usuario').val();
            var periodo = $('#periodo').val();
            var rol = $('#cargo').val();
            var ajax = $.ajax({
                type: "GET",
                url: "../../getrol/id/" + idu + "-" + rol + "-" + id,
                async: true,
                dataType: "json",
                beforeSend: function(x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function(data) {
                    if (rol == '3' || rol == '4' || rol == '6') {
                        $('#contenido').find('.mensajes').remove();
                        var options2 = '<input type="button" value="Guardar" id="guardarusuario">';
                        $('#guardar').html(options2);
                        $('#rbd-label').show();
                        $('#rbd').show();
                    }
                    if (rol == '1') {
                        $('#contenido').find('.mensajes').remove();
                        $("#rbd").val($("#rbd option:first").val());
                        $('#rbd-label').hide();
                        $('#rbd').hide();
                        var options2 = '<input type="button" value="Guardar" id="guardarusuario">';
                        $('#guardar').html(options2);
                    }
                    //Si el rol es docente
                    if (rol == '2') {
                        //si data devuelve 0 se puede agregar el nuevo rol
                        if (data == 0) {
                            var ajax = $.ajax({
                                type: "GET",
                                url: "../../getcurso/id/" + id+"/idp/"+periodo,
                                async: true,
                                dataType: "json",
                                beforeSend: function(x) {
                                    if (x && x.overrideMimeType) {
                                        x.overrideMimeType("application/j-son;charset=UTF-8");
                                    }
                                },
                                success: function(data) {
                                    if (data) {

                                        $('#guardar').html('<input type="hidden" value="1" id="tipo" /><input type="button" value="Guardar" id="guardarusuario">');

                                    }
                                }
                            });
                        }
                        //si devuelve un objeto quiere decir que ese rol ya existe para el establecimienro y el usuario
                        else {
                            $('#guardar').html('');
                            if ($('#contenido').find('.mensajes').length == 0) {
                                $('#contenido').append('<div class="error mensajes">El rol que intenta ingresar ya existe para el establecimiento ' + data[0].nombreEstablecimiento + '</div>');
                            }
                            return false;
                        } //fin if data 0
                    } //fin if rol
                } //fin success
            }); //fin ajax get rol
        } //fin if null
        e.stopImmediatePropagation();
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
$(document).ready(function() {
    $("#guardarusuario").live('click', function() {
        var nombre = $('#idCuenta').val();
        var periodo = $('#periodo').val();
        var rbd = $('#rbd').val();
        var cargo = $('#cargo').val();
        var tipo = $('#tipo').val();
        var cursos;
        var niveles = new Array();
        var selected = [];
        var selectedeliminados = [];
        if (periodo == '' || periodo == 'Null' || nombre == '' || nombre == 'Null' || cargo == '' || cargo == 'Null') {
            $('#contenido').append('<div class="error mensajes">Existen datos sin completar</div>');
            $(document).ready(function() {
                setTimeout(function() {
                    $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                }, 3000);
            });
            return false;
        } else {
            if (cargo == '3' || cargo == '4' || cargo == '5' || cargo == '6' || (cargo == '2' && tipo == '1')) {
                var edited = "{";
                edited += '"0":{"nombre":"' + nombre + '","rbd":"' + rbd + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '"}';
                edited += "}";
            }
            if (cargo == '1') {
                var edited = "{";
                edited += '"0":{"nombre":"' + nombre + '","rbd":"' + rbd + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '"}';
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
                //creamos el json con los datos para enviar y guardar
                var edited = "{";
                for (var i = 0; i < (cursoslimpios).length; i++) {
                    edited += '"' + [i] + '":{"nombre":"' + nombre + '","rbd":"' + rbd + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '","curso":"' + cursoslimpios[i] + '","asignaturas":{';
                    for (var j = 0; j < (selected[cursoslimpios[i]]).length; j++) {
                        var valor = selected[cursoslimpios[i]][j].value;
                        edited += '"' + j + '":"' + valor + '",';
                    }
                    edited = edited.slice(0, -1);
                    edited += "}},";
                }
                edited = edited.slice(0, -1);
                edited += "}";


            }
            $.ajax({
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: '../../guardarolusuario/',
                data: edited,
                beforeSend: function(data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function(data) {
                    if (data.response == 'error') {
                        $('#contenido').append('<div class="error mensajes">Se ha Producido un error, por favor intente nuevamnete</div>');
                        $(document).ready(function() {
                            setTimeout(function() {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });
                    } else {
                        window.location.replace(data.redirect);
                    }
                },
                error: function(requestData, strError, strTipoError) {
                    alert('Error ' + strTipoError + ': ' + strError);
                },
                complete: function(requestData, exito) {}
            });
        }
        return false;
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
