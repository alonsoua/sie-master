//si presiona boton guardar
$(document).ready(function () {
    $("#guardaorden").live('click', function () {
        var datos = [];
        $("#tblTabla tbody tr").each(function (index) {
            var campo1;
            $(this).children("td").each(function (index2) {
                switch (index2) {

                    case 0:

                        var valor = $(this).find('input').map(function () {
                            return $(this).val();
                        }).get();
                        campo1 = valor;
                        break;

                }

            });
            1

            datos[index] = {
                ids: campo1

            };
        });

        var Datos={};
        var aux = 1;
        for (var i = 0; i < datos.length; i++) {
            Datos[i]={idalumno:datos[i].ids[0],orden:aux};
            aux++;
        }

        $.ajax(
            {
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: '../../guardarorden/',
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

                    }
                    else {
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
