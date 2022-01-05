$(function () {

    var us = $('#us').val();
    var apx = $('#apx').val();
    var tipo;
    var nuevo;
    var url = $('#nuevo').attr('href');
    var tabla = $('#tblTabla').html();

    var codigos = [];
    var conceptos = [];
    var datosconcepto = [];
    var porcentajes = [];
    var texto;
    var auxnota;
    var asig;
    var largo = $("div#alumnos").children().length - 2;
    var listaid;


    var arr_u = [
        {val: 1, text: 'Entregada a Tiempo'},
        {val: 2, text: 'Entregada a Destiempo'},
        {val: 3, text: 'No Entregada'},
    ];

    var arr_d = [
        {val: 1, text: 'Entregada Completa'},
        {val: 2, text: 'Entregada Incompleta'},

    ];

    var arr_t = [
        {val: 1, text: 'Logro Entre 80% y 100%'},
        {val: 2, text: 'Logro Entre 60% y 79%'},
        {val: 3, text: 'Logro Menor al 60%'},
    ];

    var arr_n = [
        {val: 70, text: '70'},
        {val: 50, text: '50'},
        {val: 30, text: '30'},

    ];

    var sel_u = $('<select>');
    sel_u.attr('class', 'estadoguia_u');
    $(arr_u).each(function () {
        sel_u.append($("<option>").attr('value', this.val).text(this.text));
    });

    var sel_d = $('<select>');
    sel_d.attr('class', 'estadoguia_d');
    $(arr_d).each(function () {
        sel_d.append($("<option>").attr('value', this.val).text(this.text));
    });

    var sel_t = $('<select>');
    sel_t.attr('class', 'estadoguia_t');
    $(arr_t).each(function () {
        sel_t.append($("<option>").attr('value', this.val).text(this.text));
    });

    var sel_n_u = $('<select>');
    sel_n_u.attr('class', 'estadoguia_n_u');
    sel_n_u.attr('disabled', 'disabled');
    $(arr_n).each(function () {
        sel_n_u.append($("<option>").attr('value', this.val).text(this.text));
    });
    var sel_n_d = $('<select>');
    sel_n_d.attr('class', 'estadoguia_n_d');
    sel_n_d.attr('disabled', 'disabled');
    $(arr_n).each(function () {
        sel_n_d.append($("<option>").attr('value', this.val).text(this.text));
    });
    var sel_n_t = $('<select>');
    sel_n_t.attr('class', 'estadoguia_n_t');
    sel_n_t.attr('disabled', 'disabled');
    $(arr_n).each(function () {
        sel_n_t.append($("<option>").attr('value', this.val).text(this.text));
    });


    function colorelemento(elemento,opc) {

        if(opc){
            var id = elemento;


        }else{
            var id = $(elemento).attr('name');
        }
        var nota = $("div[name=" + id + "]").find('p').text();


        switch (true) {

            case (parseInt(nota) > 39):
                if (!$("div[name=" + id + "]").find('p').hasClass("azul")) {
                    $("div[name=" + id + "]").find('p').addClass("azul");

                }
                if ($("div[name=" + id + "]").find('p').hasClass("red")) {
                    $("div[name=" + id + "]").find('p').removeClass("red");

                }
                break;
            case (nota > 0 && nota < 40):
                if (!$("div[name=" + id + "]").find('p').hasClass("red")) {
                    $("div[name=" + id + "]").find('p').addClass("red");

                }
                if ($("div[name=" + id + "]").find('p').hasClass("azul")) {
                    $("div[name=" + id + "]").find('p').removeClass("azul");

                }
                break;
            case (nota == 0):
                if ($("div[name=" + id + "]").find('p').hasClass("red")) {
                    $("div[name=" + id + "]").find('p').removeClass("red");

                }
                if ($("div[name=" + id + "]").find('p').hasClass("azul")) {
                    $("div[name=" + id + "]").find('p').removeClass("azul");

                }
                break;


        }


    }

    function actualizanota(elemento) {


        var id = $(elemento).attr('name');


        let n_10 = $(elemento).find(".estadoguia_n_u").val();
        let n_30 = $(elemento).find(".estadoguia_n_d").val();
        let n_60 = $(elemento).find(".estadoguia_n_t").val();
        let total_10 = Math.round(parseInt(n_10) * 0.1);
        let total_30 = Math.round(parseInt(n_30) * 0.3);
        let total_60 = Math.round(parseInt(n_60) * 0.6);

        let total_100 = total_10 + total_30 + total_60;

        $("div[name=" + id + "]").find('p').replaceWith('<p>' + total_100 + '</p>');
        colorelemento(elemento,false);



    }


    // function contabilizar(lista, elemento) {
    //
    //
    //     //H
    //     for (var i = 0; i < lista.length; i++) {
    //         var entregada = 0;
    //         var noentregada = 0;
    //         var revisada = 0;
    //         var revisadain = 0;
    //         var reenviada = 0;
    //         var id = lista[i]['idAlumnosActual'];
    //
    //
    //         $("div.grid-item.selector." + id).each(function (index, item) {
    //             switch (true) {
    //                 case ($(item).find('select option:selected').val() == 1):
    //                     entregada += 1;
    //                     break;
    //                 case ($(item).find('select option:selected').val() == 2):
    //                     noentregada += 1;
    //                     break;
    //                 case ($(item).find('select option:selected').val() == 3):
    //                     revisada += 1;
    //                     break;
    //                 case ($(item).find('select option:selected').val() == 4):
    //                     revisadain += 1;
    //                     break;
    //
    //
    //
    //             }
    //
    //         });
    //
    //
    //         $(".entregada" + id).find('p').replaceWith('<p style="color: white">' + entregada + '</p>');
    //         $(".noentregada" + id).find('p').replaceWith('<p style="color: white">' + noentregada + '</p>');
    //         $(".revisada" + id).find('p').replaceWith('<p>' + revisada + '</p>');
    //         $(".revisadain" + id).find('p').replaceWith('<p>' + revisadain + '</p>');
    //
    //
    //
    //     }
    //     //V
    //
    //     if (elemento != null) {
    //         for (var k = 0; k < 4; k++) {
    //
    //             switch (k) {
    //
    //                 case 0:
    //                     var total = $(elemento).find('select option:selected[value="1"]').length;
    //                     $(elemento).find('.entregado p').replaceWith('<p style="color: white">' + total + '</p>');
    //                     break;
    //                 case 1:
    //                     var total = $(elemento).find('select option:selected[value="2"]').length;
    //                     $(elemento).find('.noentregado p').replaceWith('<p style="color: white">' + total + '</p>');
    //                     break;
    //                 case 2:
    //                     var total = $(elemento).find('select option:selected[value="3"]').length;
    //                     $(elemento).find('.revisado p').replaceWith('<p>' + total + '</p>');
    //                     break;
    //
    //                 case 3:
    //                     var total = $(elemento).find('select option:selected[value="4"]').length;
    //                     $(elemento).find('.revisadoin p').replaceWith('<p>' + total + '</p>');
    //                     break;
    //
    //
    //
    //             }
    //
    //         }
    //     }
    //
    //
    // }


    $('.asignatura').live('click', function (e) {
        $("#tblTabla").scrollTop(0);
        asig = $(this).attr('id');

        if ($(this).val() != 'Null') {
            $('#tblTabla').html('');
            $('#tblTabla').html(tabla);
            var auxurl;
            auxurl = url + $(this).attr('id');
            $('#nuevo').attr('href', auxurl);
            tipo = $(this).attr('name');


            if (tipo == 5) {

                $(".notas").val('');

                $.ajax({
                    type: "GET",
                    url: "getasignaturas/id/" + $(this).attr('id'),
                    async: true,
                    dataType: "json",
                    beforeSend: function (x) {
                        if (x && x.overrideMimeType) {
                            x.overrideMimeType("application/j-son;charset=UTF-8");
                        }
                    },
                    success: function (data) {

                        if (data != '') {
                            datosconcepto = data;
                            texto = '';
                            if (data[0]['concepto'] == null) {
                                $('#contenido').append('<div class="error mensajes">Esta asignatura no posee los conceptos crreados para este período</div>');
                                $(document).ready(function () {
                                    setTimeout(function () {
                                        $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                                    }, 3000);
                                });
                                window.stop();
                            } else {
                                for (var i = 0; i < data.length; i++) {

                                    codigos[i] = data[i].concepto.charCodeAt(0);
                                    conceptos[i] = data[i].concepto;
                                    texto += 'event.which===' + data[i].concepto.charCodeAt(0) + ' || ';
                                }
                                texto = texto.slice(0, -3);
                            }


                        } else {
                            $('#contenido').append('<div class="error mensajes">Esta asignatura no posee los conceptos crreados para este período</div>');
                            $(document).ready(function () {
                                setTimeout(function () {
                                    $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                                }, 3000);
                            });
                            window.stop();
                        }


                    }


                });


            }
            codigos = [];

            $.ajax({
                //global: false,
                type: "GET",
                url: "getnotasguia/id/" + $(this).attr('id'),
                async: true,
                dataType: "json",
                beforeSend: function (x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function (data) {

                    if (data.notas == '' || data.notas == 'Null') {

                    } else {
                        var h = 4;
                        var g = 35;
                        var aux = 4;
                        var ind = 0;
                        var indg = 0;
                        var inde = 0;

                        listaid = data.notas[0]['alumnos'];

                        for (var i = 0; i < data.notas.length; i++) {


                            //Ponderado 10%

                            $('#tblTabla').find('.segundo').before('<div id="guia' + data.notas[i]['idEvaluacion'] + '" class="grid-col" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="accionesguia/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['contenido'] + ' </a></div><div style="display: -webkit-inline-flex;width: 674px"><div class="grid-item-pond-header" style="background-color: orange" >Ponderación 10%</div><div class="grid-item-pond-header" style="background-color: green" >Ponderación 30%</div><div class="grid-item-pond-header" style="background-color: cyan" >Ponderación 60%</div></div></div>');
                            for (var k = 0; k < largo; k++) {

                                switch (data.notas[i]['alumnos'][k]['valorGuia']) {

                                    case 1:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '" style="width: 674px"></div>');
                                        $("div[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item-pond" style="background-color: orange" >' + sel_u[0].outerHTML + '</div>');
                                        $("div[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item-pond-nota" style="background-color: orange" >' + sel_n_u[0].outerHTML + '</div>');
                                        $("div[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item-pond" style="background-color: green" >' + sel_d[0].outerHTML + '</div>');
                                        $("div[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item-pond-nota" style="background-color: green" >' + sel_n_d[0].outerHTML + '</div>');
                                        $("div[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item-pond" style="background-color: cyan" >' + sel_t[0].outerHTML + '</div>');
                                        $("div[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item-pond-nota" style="background-color: cyan" >' + sel_n_t[0].outerHTML + '</div>');


                                        //Seteamos los Selects

                                        if(data.notas[i]['alumnos'][k]['ponderacion_10']==3){

                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_u option[value='" + data.notas[i]['alumnos'][k]['ponderacion_10'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_u option[value='" + data.notas[i]['alumnos'][k]['nota_10'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_u ").prop('disabled', true);


                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_d option[value='" + data.notas[i]['alumnos'][k]['ponderacion_30'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_d option[value='" + data.notas[i]['alumnos'][k]['nota_30'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_d ").prop('disabled', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_d ").prop('disabled', true);


                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_t option[value='" + data.notas[i]['alumnos'][k]['ponderacion_60'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_t option[value='" + data.notas[i]['alumnos'][k]['nota_60'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_t ").prop('disabled', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_t ").prop('disabled', true);


                                        }else{
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_u option[value='" + data.notas[i]['alumnos'][k]['ponderacion_10'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_u option[value='" + data.notas[i]['alumnos'][k]['nota_10'] + "']").prop('selected', true);


                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_d option[value='" + data.notas[i]['alumnos'][k]['ponderacion_30'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_d option[value='" + data.notas[i]['alumnos'][k]['nota_30'] + "']").prop('selected', true);


                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_t option[value='" + data.notas[i]['alumnos'][k]['ponderacion_60'] + "']").prop('selected', true);
                                            $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] .estadoguia_n_t option[value='" + data.notas[i]['alumnos'][k]['nota_60'] + "']").prop('selected', true);

                                        }



                                        //$(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='1']").prop('selected', true);

                                        break;
                                    case 2:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '"  tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + sel_u[0].outerHTML + '</div>');
                                        $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='2']").prop('selected', true);
                                        break;
                                    case 3:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '"  tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + sel_u[0].outerHTML + '</div>');
                                        $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='3']").prop('selected', true);
                                        break;
                                    case 4:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + ' " data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '"  tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + sel_u[0].outerHTML + '</div>');
                                        $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='4']").prop('selected', true);
                                        break;


                                }


                                indg++;
                            }


                            //Nota

                            $('#tblTabla').find('.segundo').before('<div id="porcentaje' + data.notas[i]['idEvaluacion'] + '" class="grid-col" id="nota"><div class="grid-item grid-item--header"><p>Notas ' + data.notas[i]['contenido'] + '</p></div>');
                            for (var k = 0; k < largo; k++) {


                                $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '" style="text-align: center;display: block;"><p>' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>');

                                //Asignamos el Color

                                colorelemento(data.notas[i]['alumnos'][k]['idNotas'],true);

                                inde++;
                            }

                            //Estadistica

                            for (var k = 0; k < 1; k++) {


                                switch (k) {

                                    case 0:

                                        var total_1 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_u option:selected[value="1"]').length;
                                        var total_2 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_u option:selected[value="2"]').length;
                                        var total_3 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_u option:selected[value="3"]').length;
                                        var total_4 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_d option:selected[value="1"]').length;
                                        var total_5 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_d option:selected[value="2"]').length;
                                        var total_6 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_t option:selected[value="1"]').length;
                                        var total_7 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_t option:selected[value="2"]').length;
                                        var total_8 = $("div#guia" + data.notas[i]['idEvaluacion']).find('.estadoguia_t option:selected[value="3"]').length;
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div style="display: -webkit-inline-flex; width: 676px"><div class="grid-item-pond-header" style="background-color: orange;height: 75px;width: 219px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p style="color:white;">Entregada a Tiempo: ' + total_1 + '</p><p style="color:white;">Entregada a Destiempo: ' + total_2 + '</p><p style="color:white;">No Entregada: ' + total_3 + '</p></div><div class="grid-item-pond-header" style="background-color: green;height: 75px;width: 219px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p style="color:white;">Entregada Completa: ' + total_4 + '</p><p style="color:white;">Entregada Incompleta: ' + total_5 + '</p></div><div class="grid-item-pond-header" style="background-color: cyan;height: 75px;width: 219px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>Logro entre 80% y 100%: ' + total_6 + '</p><p>Logro entre 60% y 79%: ' + total_7 + '</p><p>Logro menor al 60%: ' + total_8 + '</p></div></div>');
                                        break;



                                }
                                //$("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p></p></div>');
                                $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item" style="height: 80px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p></p></div>');

                            }


                            h++;
                            ind++;
                            inde++;

                        }

                        //Recargamos los div de ventanas

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


                        //contabilizar(listaid);
                    }


                }

                //Recorremos el largo de alumnos


            });


            e.stopImmediatePropagation();
        }


    });


    window.Eliminar = function () {
        var boton = $('.current').find('.asignatura');
        $(boton).trigger('click');

    }

    window.Cambiar = function () {

        var boton = $('.current').find('.asignatura');
        $(boton).trigger('click');
    }

    window.Nuevo = function (valor) {
        var boton = $('.current').find('.asignatura');
        nuevo = valor;
        $(boton).trigger('click');

    }


    $('select').live('change', function (e) {


        var idalumno = $(this).closest('div').attr('data-value');
        var elemento = $(this).closest('.selector');
        var clase = $(this).attr('class');


        //Cambiamos el color
        var id = Number($(this).find(":checked").val());

        if (clase == 'estadoguia_u') {



            if (id == 3) {
                //$(elemento).find(".estadoguia_u").val();
                $(elemento).find(".estadoguia_n_u").val(30);
                $(elemento).find(".estadoguia_d").val(2);
                $(elemento).find(".estadoguia_n_d").val(30);
                $(elemento).find(".estadoguia_t").val(3);
                $(elemento).find(".estadoguia_n_t").val(30);
                $(elemento).find(".estadoguia_n_u").attr('disabled', 'disabled');
                $(elemento).find(".estadoguia_d").attr('disabled', 'disabled');
                $(elemento).find(".estadoguia_n_d").attr('disabled', 'disabled');
                $(elemento).find(".estadoguia_t").attr('disabled', 'disabled');
                $(elemento).find(".estadoguia_n_t").attr('disabled', 'disabled');
            } else {

                if(id==2){
                    $(elemento).find(".estadoguia_n_u").val(50);
                }else if (id==1){
                    $(elemento).find(".estadoguia_n_u").val(70);
                }

                if($(elemento).find(".estadoguia_d").val()==1){
                    $(elemento).find(".estadoguia_n_d").val(70);
                }else if($(elemento).find(".estadoguia_d").val()==2){
                    $(elemento).find(".estadoguia_n_d").val(50);
                }


                $(elemento).find(".estadoguia_d").removeAttr('disabled');
                $(elemento).find(".estadoguia_t").removeAttr('disabled');

            }
        }

        if (clase == 'estadoguia_d') {



            if (id == 1) {

                $(elemento).find(".estadoguia_n_d").val(70);

            } else if(id==2){

                $(elemento).find(".estadoguia_n_d").val(50);



            }
        }

        if (clase == 'estadoguia_t') {



            if (id == 1) {

                $(elemento).find(".estadoguia_n_t").val(70);

            } else if(id==2){

                $(elemento).find(".estadoguia_n_t").val(50);



            }
            else if(id==3){

                $(elemento).find(".estadoguia_n_t").val(30);



            }
        }


        //obtenemos valor del curso
        //var id = $(this).closest('div').attr('name');
        var valor_u = $(elemento).find(".estadoguia_u").val();
        var valor_u_n = $(elemento).find(".estadoguia_n_u").val();
        var valor_d = $(elemento).find(".estadoguia_d").val();
        var valor_d_n = $(elemento).find(".estadoguia_n_d").val();
        var valor_t = $(elemento).find(".estadoguia_t").val();
        var valor_t_n = $(elemento).find(".estadoguia_n_t").val();


        actualizanota(elemento);

        var ids=$(this).closest('.selector').attr('name');

        var Datos = {
            "id": ids,
            "valor_u": valor_u,
            "valor_u_n":valor_u_n,
            "valor_d":valor_d,
            "valor_d_n":valor_d_n,
            "valor_t":valor_t,
            "valor_t_n":valor_t_n,

        }


        $.ajax(
            {
                global: false,
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: 'guardaestadoguiaponderacion/',
                data: JSON.stringify(Datos),
                success: function (data) {


                    if (data.response == 'exito') {

                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        Toast.fire({
                            type: 'success',
                            title: 'Registro Actualizado'
                        })
                        var obj = {
                            ['idAlumnosActual']: idalumno,
                        }
                        var listass = [];
                        listass[0] = obj;
                        //contabilizar(listass, elemento);


                    } else if (data.response == 'errorinserta') {

                        const Toasterror = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000
                        });

                        Toasterror.fire({
                            type: 'error',
                            title: 'Error al ingresar los datos'
                        })
                    }
                },
                error: function () {
                    const Toasterror = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000
                    });

                    Toasterror.fire({
                        type: 'error',
                        title: 'Error al ingresar los datos'
                    })
                }
            });
        e.stopImmediatePropagation();

    });

});
