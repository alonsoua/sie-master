$(function () {



//si presiona boton guardar
    $("#guardar").live('click', function (e) {



        //Obtenemos el valor de todos los checks
        var valores;
        valores = $('.eva' ).map(function () {
            return $(this);
        }).get();

        console.log(valores);


        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        edited += '"0":{"valores":{';
        for (var i = 0; i < valores.length; i++) {

            if ($(valores[i]).is(':checked')) {
                edited += '"' + [i] + '":{ "ev":"' + valores[i].val() + '","valor":"1"},';

            }else{
                edited += '"' + [i] + '":{ "ev":"' + valores[i].val() + '","valor":"2"},';
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
                url: 'guardapublicar/',
                data: edited,
                beforeSend: function (data) {
                    $('#div_cliente').html('<label>Cargando...</label>');
                },
                success: function (data) {
                    if (parseInt(data.response) === 1) {

                        $("#pdf").replaceWith('<div class="ventana" style="display:inline"><a href="' + data.url + '"  id="pdf" class="button medium"><i class="icon-print"></i> Imprimir</a></div>');

                        $('.ventana').each(function (i) {
                            $(this).find('a').attr('rel', 'ventana' + i)
                                .fancybox({
                                    overlayOpacity: 0.2,
                                    overlayColor: '#000',
                                    type: 'iframe',
                                    'width': 1000,
                                    'height': 900,
                                    'transitionIn': 'elastic', // this option is for v1.3.4
                                    'transitionOut': 'elastic', // this option is for v1.3.4
                                    'fitToView': false
                                });
                        });

                        Swal(
                            'Guardado',
                            '',
                            'success'
                        )


                    } else if (parseInt(data.response) === 0) {
                        switch (parseInt(data.status)) {
                            case 1:
                                const errors = Swal.mixin({
                                    toast: true,
                                    position: 'top',
                                    showConfirmButton: false,
                                    timer: 3000
                                });

                                errors({
                                    type: 'error',
                                    title: 'Reinicie su Sesión '
                                })
                                break;
                            case 2:
                                const error = Swal.mixin({
                                    toast: true,
                                    position: 'top',
                                    showConfirmButton: false,
                                    timer: 3000
                                });

                                error({
                                    type: 'error',
                                    title: 'Error al ingresar los datos '
                                })
                                break;

                            case 3:
                                const errorss = Swal.mixin({
                                    toast: true,
                                    position: 'top',
                                    showConfirmButton: false,
                                    timer: 3000
                                });

                                errorss({
                                    type: 'error',
                                    title: 'Complete todos los campos (*)'
                                })

                                break;


                        }
                    } else {
                        const error = Swal.mixin({
                            toast: true,
                            position: 'top',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        error({
                            type: 'error',
                            title: 'Error al ingresar los datos '
                        })
                    }
                },
                error: function () {
                    const error = Swal.mixin({
                        toast: true,
                        position: 'top',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    error({
                        type: 'error',
                        title: 'Error al ingresar los datos '
                    })
                }
            });


        e.stopImmediatePropagation();
    });




});
