//si presiona boton guardar
$(document).ready(function () {
    $("#guardaasignatura").live('click', function () {
        var datos = [];
        $("#tblTabla tbody>tr").each(function (index) {

            var id=0, horas=0, priorizadas=0,electivos=0;
            $($(this)).children("td").each(function (index2) {

                switch (index2) {

                    case 1:
                        var valor = $(this).find('input').map(function () {
                            return $(this).val();
                        }).get();
                        id = valor;

                        break;

                    case 4:
                        var horas_semanales = $(this).find('input').map(function () {
                            return $(this).val();
                        }).get();
                        if(horas_semanales[0]>0 && horas_semanales[0]!=""){
                            horas = horas_semanales;
                        }else {
                            horas_semanales[0]="0";
                            horas = horas_semanales;
                        }
                        break;

                    case 6:
                        var priorizada = $(this).find('input').map(function () {
                            if ($(this).is(':checked')) {
                                return 0;
                            }else{
                                return 1
                            }
                        }).get();

                        priorizadas = priorizada;
                        break;

                    case 7:
                        var electivo = $(this).find('input').map(function () {
                            if ($(this).is(':checked')) {
                                return 1;
                            }else{
                                return 0
                            }
                        }).get();

                        electivos = electivo;
                        break;

                }
            });
            datos[index] = {
                ida: id,
                in: priorizadas,
                hor:horas,
                el:electivos

            };
        });
        var n = $('#curso').val();
        var Datos=[];
        for (var i = 0; i < datos.length; i++) {


            Datos[i] = {
                "idasignatura":datos[i].ida[0],
                "curso": n,
                "prioritaria": datos[i].in[0],
                "horas":datos[i].hor[0],
                "electivo":datos[i].el[0],

            }
        }

        $.ajax({
            cache: false,
            async: true,
            dataType: 'json',
            type: 'POST',
            contentType: 'application/x-www-form-urlencoded',
            url: '../../guardaasignatura/',
            data: JSON.stringify(Datos),
            beforeSend: function (data) {
                $('#div_cliente').html('<label>Cargando...</label>');
            },
            success: function (data) {
                if (data.response == 'error') {
                    $('#contenido').append('<div class="error mensajes">Se ha producido un error, intente nuevamente</div>');
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
                alert('Error ' + strTipoError + ': ' + strError);
            },
            complete: function (requestData, exito) {
            }
        });
    });
});
