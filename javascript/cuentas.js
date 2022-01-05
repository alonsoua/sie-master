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
                if ($(this).val() != '' && $('#periodo').val() != '') {
                    $('#guardar').html('<input type="hidden" value="1" id="tipo" /><input type="button" value="Guardar" id="guardarusuario">');
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
        //alert(nivel);
        var rbd = $('#idEstablecimiento').val();
        var nombres = $('#usuario').val();
        var nombrereal = $('#nombrescuenta').val();
        var paterno = $('#paternocuenta').val();
        var materno = $('#maternocuenta').val();
        var correo = $('#correo').val();
        var periodo = $('#periodo').val();
        var cargo = $('#cargo').val();
        var tipo = $('#tipo').val();
        var contraseña = $('input[type=password]').each(function (i, item) {
            return ($(item).val())
        });
        var cursos;
        var niveles = new Array();
        var selected = [];
        var selectedeliminados = [];
        if (periodo == '' || periodo == 'Null' || rbd == '' || rbd == 'Null' || nombrereal == '' || nombrereal == 'Null' || nombres == '' || nombres == 'Null' || cargo == '' || cargo == 'Null') {
            alert('Existen datos sin completar, por favor rellene los campos');
            return false;
        }else if($('#usuario').hasClass('errorinput')){

            alert('El rut no es válido');
            return false;

        } else {
            if (cargo == '3' || cargo == '4' || cargo == '5' || cargo == '6' || (cargo == '2' && tipo == '1')) {
                var edited = "{";
                edited += '"0":{"nombre":"' + nombres + '","nombrereal":"' + nombrereal + '","paterno":"' + paterno + '","materno":"' + materno + '","correo":"' + correo + '","rbd":"' + rbd + '","pass":"' + btoa(contraseña.val()) + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '"}';
                edited += "}";
            }
            if (cargo == '1') {
                var edited = "{";
                edited += '"0":{"nombre":"' + nombres + '","nombrereal":"' + nombrereal + '","paterno":"' + paterno + '","materno":"' + materno + '","correo":"' + correo + '","rbd":"0","pass":"' + btoa(contraseña.val()) + '","cargo":"' + cargo + '","periodo":"' + periodo + '"}';
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
                    edited += '"' + [i] + '":{"nombre":"' + nombres + '","nombrereal":"' + nombrereal + '","paterno":"' + paterno + '","materno":"' + materno + '","correo":"' + correo + '","pass":"' + btoa(contraseña.val()) + '","rbd":"' + rbd + '","cargo":"' + cargo + '","periodo":"' + periodo + '","tipo":"' + tipo + '","curso":"' + cursoslimpios[i] + '","asignaturas":{';
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
                url: 'guardausuario/',
                data: edited,
                beforeSend: function (data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function (data) {
                    if (data.response == 'error') {
                        $('#contenido').append('<div class="error mensajes">Se ha Producido un error, por favor intente nuevamente</div>');
                        $(document).ready(function () {
                            setTimeout(function () {
                                $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                            }, 3000);
                        });
                    } else if (data.response == 'usuarioexiste') {

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
                    $('#contenido').append('<div class="error mensajes">Se ha Producido un error</div>');
                    $(document).ready(function () {
                        setTimeout(function () {
                            $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                        }, 3000);
                    });
                }
            });
            return false;
        }
    });
});

// check-uncheck all
$(document).on('change', 'input[id="all"]', function () {
    $('.canine').prop("checked", this.checked);
});
// parent/child check-uncheck all
$(function () {
    $('.parent').live('click', function () {
        $(this).parent().next().find(':checkbox').prop('checked', this.checked);
    });
});
