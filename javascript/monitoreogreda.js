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
    var porcentajess = [];
    var texto;
    var auxnota;
    var asig;
    var largo = $("div#alumnos").children().length - 5;
    var listaid;

    //Obtenemos los Rangos

    $.ajax({

        type: "GET",
        url: "getporcentajes/",
        async: true,
        dataType: "json",
        beforeSend: function (x) {
            if (x && x.overrideMimeType) {
                x.overrideMimeType("application/j-son;charset=UTF-8");
            }
        },
        success: function (data) {

            if (data != '') {

                console.log(data);
                let aux=0;

                for (let i = 0; i < data.length; i++) {

                    if(data[i]['tiempoPonderacion']==1){
                        porcentajes[i]=[];
                        porcentajes[i]['porcentaje_valor'] = data[i]['idEquivalencia'];
                        porcentajes[i]['porcentaje_inicio'] = data[i]['porcentaje_inicio'];
                        porcentajes[i]['porcentaje_final'] = data[i]['porcentaje_final'];
                        porcentajes[i]['equivalencia_nota'] = data[i]['equivalencia_nota'];

                    }else if(data[i]['tiempoPonderacion']==2){
                        porcentajess[aux]=[];
                        porcentajess[aux]['porcentaje_valor'] = data[i]['idEquivalencia'];
                        porcentajess[aux]['porcentaje_inicio'] = data[i]['porcentaje_inicio'];
                        porcentajess[aux]['porcentaje_final'] = data[i]['porcentaje_final'];
                        porcentajess[aux]['equivalencia_nota'] = data[i]['equivalencia_nota'];
                        aux++;
                    }



                }

                console.log(porcentajess);


            } else {
                $('#contenido').append('<div class="error mensajes">No Existen Niveles de Logros Creados</div>');
                $(document).ready(function () {
                    setTimeout(function () {
                        $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                    }, 3000);
                });
                window.stop();
            }


        }


    });


    var arr = [
        {val: 1, text: 'Entregada'},
        {val: 2, text: 'No Entregada'},
        {val: 3, text: 'Retroalimentada'},
        {val: 4, text: 'Evaluada Formativamente'},

    ];

    var sel = $('<select>');
    sel.attr('class', 'estadoguia');
    $(arr).each(function () {
        sel.append($("<option>").attr('value', this.val).text(this.text));
    });

    // RQM 42
    mostrarNucleos($("#sltAmbito").val());

    $('#sltAmbito').live('change', function (e) {
        mostrarNucleos($(this).val());
    });

    function mostrarNucleos (idambito) {
        
        $('.asignaturali').each(function(i) {
            var ambito = $(this).attr('id');
            if (ambito != idambito) {
                $(this).css('display', 'none');
            } else {
                $(this).css('display', '');
            }
           
        });

    }


    function colorelemento(elemento) {

        var id = elemento;
        var nota = $("div[tabindex=e" + id + "]").find('p').text();

        switch (true) {

            case (parseInt(nota) > 39):
                if (!$("div[tabindex=e" + id + "]").find('p').hasClass("azul")) {
                    $("div[tabindex=e" + id + "]").find('p').addClass("azul");

                }
                if ($("div[tabindex=e" + id + "]").find('p').hasClass("red")) {
                    $("div[tabindex=e" + id + "]").find('p').removeClass("red");

                }
                break;
            case (nota > 0 && nota < 40):
                if (!$("div[tabindex=e" + id + "]").find('p').hasClass("red")) {
                    $("div[tabindex=e" + id + "]").find('p').addClass("red");

                }
                if ($("div[tabindex=e" + id + "]").find('p').hasClass("azul")) {
                    $("div[tabindex=e" + id + "]").find('p').removeClass("azul");

                }
                break;
            case (nota == 0):
                if ($("div[tabindex=e" + id + "]").find('p').hasClass("red")) {
                    $("div[tabindex=e" + id + "]").find('p').removeClass("red");

                }
                if ($("div[tabindex=e" + id + "]").find('p').hasClass("azul")) {
                    $("div[tabindex=e" + id + "]").find('p').removeClass("azul");

                }
                break;


        }


    }

    function actualizanota(elemento) {

        var id = $(elemento).closest('div').attr('tabindex');
        var nota = $(elemento).closest('p').text().replace("%", "");
        var tiempo = $(elemento).closest('div').attr('id');
        console.log(tiempo);
        if(tiempo==1){

            switch (true) {

                case (nota >= porcentajes[0]['porcentaje_inicio'] && nota <= porcentajes[0]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajes[0]['equivalencia_nota'] + '</p>');
                    break;
                case (nota >= porcentajes[1]['porcentaje_inicio'] && nota <= porcentajes[1]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajes[1]['equivalencia_nota'] + '</p>');
                    break;
                case (nota >= porcentajes[2]['porcentaje_inicio'] && nota <= porcentajes[2]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajes[2]['equivalencia_nota'] + '</p>');
                    break;

                case (nota >= porcentajes[3]['porcentaje_inicio'] && nota <= porcentajes[3]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajes[3]['equivalencia_nota'] + '</p>');
                    break;

                case (nota >= porcentajes[4]['porcentaje_inicio'] && nota <= porcentajes[4]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajes[4]['equivalencia_nota'] + '</p>');
                    break;

                case (nota >= porcentajes[5]['porcentaje_inicio'] && nota <= porcentajes[5]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajes[5]['equivalencia_nota'] + '</p>');
                    break;


                case (nota >= porcentajes[6]['porcentaje_inicio'] && nota <= porcentajes[6]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajes[6]['equivalencia_nota'] + '</p>');
                    break;

            }

        }else if(tiempo==2){
            switch (true) {

                case (nota >= porcentajess[0]['porcentaje_inicio'] && nota <= porcentajess[0]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajess[0]['equivalencia_nota'] + '</p>');
                    break;
                case (nota >= porcentajess[1]['porcentaje_inicio'] && nota <= porcentajess[1]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajess[1]['equivalencia_nota'] + '</p>');
                    break;
                case (nota >= porcentajess[2]['porcentaje_inicio'] && nota <= porcentajess[2]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajess[2]['equivalencia_nota'] + '</p>');
                    break;

                case (nota >= porcentajess[3]['porcentaje_inicio'] && nota <= porcentajess[3]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajess[3]['equivalencia_nota'] + '</p>');
                    break;

                case (nota >= porcentajess[4]['porcentaje_inicio'] && nota <= porcentajess[4]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajess[4]['equivalencia_nota'] + '</p>');
                    break;

                case (nota >= porcentajess[5]['porcentaje_inicio'] && nota <= porcentajess[5]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajess[5]['equivalencia_nota'] + '</p>');
                    break;


                case (nota >= porcentajess[6]['porcentaje_inicio'] && nota <= porcentajess[6]['porcentaje_final']):
                    $("div[tabindex=e" + id + "]").find('p').replaceWith('<p>' + porcentajess[6]['equivalencia_nota'] + '</p>');
                    break;

            }
        }




    }


    function contabilizar(lista, elemento) {


        //H
        for (var i = 0; i < lista.length; i++) {
            var entregada = 0;
            var noentregada = 0;
            var revisada = 0;
            var revisadain = 0;
            var reenviada = 0;
            var id = lista[i]['idAlumnosActual'];


            $("div.grid-item.selector." + id).each(function (index, item) {
                switch (true) {
                    case ($(item).find('select option:selected').val() == 1):
                        entregada += 1;
                        break;
                    case ($(item).find('select option:selected').val() == 2):
                        noentregada += 1;
                        break;
                    case ($(item).find('select option:selected').val() == 3):
                        revisada += 1;
                        break;
                    case ($(item).find('select option:selected').val() == 4):
                        revisadain += 1;
                        break;


                }

            });


            $(".entregada" + id).find('p').replaceWith('<p style="color: white">' + entregada + '</p>');
            $(".noentregada" + id).find('p').replaceWith('<p style="color: white">' + noentregada + '</p>');
            $(".revisada" + id).find('p').replaceWith('<p>' + revisada + '</p>');
            $(".revisadain" + id).find('p').replaceWith('<p>' + revisadain + '</p>');


        }
        //V

        if (elemento != null) {
            for (var k = 0; k < 4; k++) {

                switch (k) {

                    case 0:
                        var total = $(elemento).find('select option:selected[value="1"]').length;
                        $(elemento).find('.entregado p').replaceWith('<p style="color: white">' + total + '</p>');
                        break;
                    case 1:
                        var total = $(elemento).find('select option:selected[value="2"]').length;
                        $(elemento).find('.noentregado p').replaceWith('<p style="color: white">' + total + '</p>');
                        break;
                    case 2:
                        var total = $(elemento).find('select option:selected[value="3"]').length;
                        $(elemento).find('.revisado p').replaceWith('<p>' + total + '</p>');
                        break;

                    case 3:
                        var total = $(elemento).find('select option:selected[value="4"]').length;
                        $(elemento).find('.revisadoin p').replaceWith('<p>' + total + '</p>');
                        break;


                }

            }
        }


    }


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

                var ajax = $.ajax({
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


                            //Estado Guia

                            $('#tblTabla').find('.segundo').before('<div id="guia' + data.notas[i]['idEvaluacion'] + '" class="grid-col" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="accionesguia/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['contenido'] + ' </a></div></div>');
                            for (var k = 0; k < largo; k++) {

                                switch (data.notas[i]['alumnos'][k]['valorGuia']) {

                                    case 1:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" style="background-color: green" tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + sel[0].outerHTML + '</div>');
                                        $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='1']").prop('selected', true);

                                        break;
                                    case 2:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" style="background-color: red"  tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + sel[0].outerHTML + '</div>');
                                        $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='2']").prop('selected', true);
                                        break;
                                    case 3:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" style="background-color: cyan"  tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + sel[0].outerHTML + '</div>');
                                        $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='3']").prop('selected', true);
                                        break;
                                    case 4:
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + ' " data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" style="background-color: yellow"  tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + sel[0].outerHTML + '</div>');
                                        $(".selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "'] select option[value='4']").prop('selected', true);
                                        break;


                                }


                                indg++;
                            }


                            //Porcentaje

                            $('#tblTabla').find('.segundo').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><p>Porcentaje ' + data.notas[i]['contenido'] + '</p></div>');


                            //$('#tblTabla').find('.segundo').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="accionesguia/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['contenido'] + ' </a></div></div>');
                            for (var k = 0; k < largo; k++) {

                                $("div#" + data.notas[i]['idEvaluacion']).append('<div id="' + data.notas[i]['tiempo'] + '"  class="grid-item porcentaje notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['porcentajenota'] + '&#37;</p></div>')

                                ind++;
                            }

                            //Nota Convertida

                            $('#tblTabla').find('.segundo').before('<div id="porcentaje' + data.notas[i]['idEvaluacion'] + '" class="grid-col" id="nota"><div class="grid-item grid-item--header"><p>Notas ' + data.notas[i]['contenido'] + '</p></div>');
                            for (var k = 0; k < largo; k++) {


                                if(data.notas[i]['tiempo']==1){

                                    switch (true) {

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajes[0]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajes[0]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajes[0]['equivalencia_nota'] + '</p></div>');
                                            break;
                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajes[1]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajes[1]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajes[1]['equivalencia_nota'] + '</p></div>');
                                            break;
                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajes[2]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajes[2]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajes[2]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajes[3]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajes[3]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajes[3]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajes[4]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajes[4]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajes[4]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajes[5]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajes[5]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajes[5]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajes[6]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajes[6]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajes[6]['equivalencia_nota'] + '</p></div>');
                                            break;


                                    }

                                }else if(data.notas[i]['tiempo']==2){
                                    switch (true) {

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajess[0]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajess[0]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajess[0]['equivalencia_nota'] + '</p></div>');
                                            break;
                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajess[1]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajess[1]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajess[1]['equivalencia_nota'] + '</p></div>');
                                            break;
                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajess[2]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajess[2]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajess[2]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajess[3]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajess[3]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajess[3]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajess[4]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajess[4]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajess[4]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajess[5]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajess[5]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajess[5]['equivalencia_nota'] + '</p></div>');
                                            break;

                                        case (data.notas[i]['alumnos'][k]['porcentajenota'] >= porcentajess[6]['porcentaje_inicio'] && data.notas[i]['alumnos'][k]['porcentajenota'] <= porcentajess[6]['porcentaje_final']):
                                            $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + porcentajess[6]['equivalencia_nota'] + '</p></div>');
                                            break;


                                    }
                                }



                                //Asignamos el Color

                                colorelemento(inde);


                                inde++;
                            }


                            //Estadistica

                            for (var k = 0; k < 4; k++) {


                                switch (k) {

                                    case 0:

                                        var total = $("div#guia" + data.notas[i]['idEvaluacion']).find('select option:selected[value="1"]').length;
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector entregado" style="background-color: green;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p style="color: white">' + total + '</p></div>');
                                        break;
                                    case 1:
                                        var total = $("div#guia" + data.notas[i]['idEvaluacion']).find('select option:selected[value="2"]').length;
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector noentregado" style="background-color: red;"  tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p style="color: white">' + total + '</p></div>');
                                        break;
                                    case 2:
                                        var total = $("div#guia" + data.notas[i]['idEvaluacion']).find('select option:selected[value="3"]').length;
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector revisado" style="background-color: cyan;"  tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + total + '</p></div>');
                                        break;

                                    case 3:
                                        var total = $("div#guia" + data.notas[i]['idEvaluacion']).find('select option:selected[value="4"]').length;
                                        $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector revisadoin" style="background-color: yellow;"  tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>' + total + '</p></div>');
                                        break;


                                }
                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p></p></div>');
                                $("div#porcentaje" + data.notas[i]['idEvaluacion']).append('<div class="grid-item " tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p></p></div>');

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

                        if (nuevo != undefined || nuevo > 0) {
                            var tablas = $('div#' + nuevo + '>div').eq(1);
                            currCell = $(tablas);
                            edit();
                        }

                        contabilizar(listaid);
                    }


                }

                //Recorremos el largo de alumnos


            });


            e.stopImmediatePropagation();
        }


    });


    $('.selector').appendTo(sel);

    var currCell = $('.grid-item').eq(4);
    var editing = false;


    $('.notas').live('click', function (e) {
        if ($(this).attr("tabindex") >= 0) {
            currCell = $(this);
            edit();
        }

        e.stopImmediatePropagation();
    });


    function edit() {

        if (us == 1 || us == 3) {
            return false;
        } else {
            editing = true;
            if (currCell.val() != 'Null') {

                currCell.val().replace(/&#37;|&nbsp;/g, '');

                var aux = 0;
                var $this = currCell.find('p');
                var grid_col = $this.closest('.grid-col');
                var grid_index = $this.closest('div').index();
                aux = $this.text();
                var tip = $('.current').find('.asignatura').attr('name');
                var $input = $('<input>', {
                    value: $this.text(),
                    type: 'text',
                    size: '3',
                    autocomplete: 'off',
                    maxlength: '3',
                    id: 'notasinput',
                    blur: function () {

                        if (auxnota != 0 && this.value == "") {
                            this.value = auxnota;
                        }
                        if (auxnota == 0 && this.value == "") {
                            this.value = 0;
                        }
                        if (tip != 5) {

                            $this.text(this.value);

                        }


                        if (aux != this.value) {
                            //obtenemos valor del curso
                            var id = $this.closest('div').attr('name');
                            var valor = this.value;
                            var ideq = 0;

                            switch (true) {

                                case (valor >= porcentajes[0]['porcentaje_inicio'] && valor <= porcentajes[0]['porcentaje_final']):
                                    ideq = porcentajes[0]['porcentaje_valor'];
                                    break;
                                case (valor >= porcentajes[1]['porcentaje_inicio'] && valor <= porcentajes[1]['porcentaje_final']):
                                    ideq = porcentajes[1]['porcentaje_valor'];
                                    break;
                                case (valor >= porcentajes[2]['porcentaje_inicio'] && valor <= porcentajes[2]['porcentaje_final']):
                                    ideq = porcentajes[2]['porcentaje_valor'];
                                    break;

                                case (valor >= porcentajes[3]['porcentaje_inicio'] && valor <= porcentajes[3]['porcentaje_final']):
                                    ideq = porcentajes[3]['porcentaje_valor'];
                                    break;

                                case (valor >= porcentajes[4]['porcentaje_inicio'] && valor <= porcentajes[4]['porcentaje_final']):
                                    ideq = porcentajes[4]['porcentaje_valor'];
                                    break;

                                case (valor >= porcentajes[5]['porcentaje_inicio'] && valor <= porcentajes[5]['porcentaje_final']):
                                    ideq = porcentajes[5]['porcentaje_valor'];
                                    break;

                                case (valor >= porcentajes[6]['porcentaje_inicio'] && valor <= porcentajes[6]['porcentaje_final']):
                                    ideq = porcentajes[6]['porcentaje_valor'];
                                    break;


                            }


                            var Datos = {
                                "id": id,
                                "valor": valor.toString().replace(/\,/g, ""),
                                "ideq": ideq
                            }


                            $.ajax(
                                {
                                    global: false,
                                    cache: false,
                                    async: true,
                                    dataType: 'json',
                                    type: 'POST',
                                    contentType: 'application/x-www-form-urlencoded',
                                    url: 'guardaporcentajenota/',
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

                                            var nuevovalor = $this.text();
                                            var porcentaje = "&#37;";
                                            var resp = nuevovalor.concat(porcentaje);
                                            $this.html(resp);


                                            //Actualiza el Nivel de Logro
                                            actualizanota($this);
                                            colorelemento($this.closest('div').attr('tabindex'));


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

                        }

                        editing = false;

                    },
                    keyup: function (event) {

                        if (codigos.length > 0) {
                            if (eval(texto) || (event.which > 36 && event.which < 41) || event.which === 8) {
                                for (var i = 0; i < codigos.length; i++) {

                                    eval("if(event.which === codigos[i]){if(conceptos[i].length==2){$(this).val(conceptos[i])}else{$(this).val(conceptos[i]);}}");

                                }

                            } else {
                                $(this).val('');
                                return false;
                            }

                        } else {
                            if ((event.which > 47 && event.which < 58) || (event.which > 95 && event.which < 106) || (event.which > 36 && event.which < 41)) {


                                var valActual = $(this).val();
                                var lar = valActual.length;
                                var check = parseInt(valActual);

                                if (!isNaN(check)) {
                                    if (check > '100' || check < '0') {
                                        $(this).val('');
                                        return false;
                                    }
                                    if (check == '00' || check == '000') {
                                        $(this).val('0');
                                        return false;
                                    }


                                } else {
                                    $(this).val('');
                                    return false;
                                }


                                $(this).val(valActual);
                            } else {
                                $(this).val('');
                                return false;
                            }
                        }

                    },
                    focusin: function (event) {
                        $(this).closest('td').css('background-color', '#f44');
                        auxnota = $(this).val();
                        $(this).val("");
                    },
                    focusout: function (event) {
                        $(this).closest('td').css('background-color', '');

                    }


                }).appendTo($this.empty()).focus();


            }
        }

    }


    $('#tblTabla').keydown(function (e) {
        var c = "";
        var aux = "";
        if (e.which == 39) {
            //Derecha
            e.preventDefault();
            var tab = currCell.attr("tabindex");
            tab = tab + (largo + 1);
            var busc = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");


            if (busc.length == 0) {
                $("#tblTabla").scrollLeft(1500);
            }
            aux = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            if (aux.attr('class') != "not") {
                c = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            }


        } else if (e.which == 37) {
            // Izq
            e.preventDefault();
            var tab = currCell.attr("tabindex");
            tab = tab - (largo + 1);
            var busc = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");


            if (busc.length == 0) {
                $("#tblTabla").scrollLeft(0);
            }
            aux = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            if (aux.attr('class') != "not") {
                c = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            }


        } else if (e.which == 38) {
            // Arriba
            e.preventDefault();
            var tab = currCell.attr("tabindex");
            tab--;
            var busc = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");


            if (busc.length == 0) {
                $("#tblTabla").scrollTop(0);
            }

            aux = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            if (aux.attr('class') != "not") {
                c = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            }
        } else if (e.which == 40) {
            // Abajo
            var tab = currCell.attr("tabindex");
            tab++;
            aux = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            if (aux.attr('class') != "not") {
                c = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            }
            e.stopImmediatePropagation();
        } else if (!editing && (e.which == 13 || e.which == 32)) {
            // Enter o espacio
            e.preventDefault();
            edit();

        } else if (!editing && (e.which == 9 && !e.shiftKey)) {
            // Tab
            e.preventDefault();
            var tab = currCell.attr("tabindex");
            tab++;
            c = $(currCell).closest('tr').find("td[tabindex='" + tab + "']");

        } else if (!editing && (e.which == 9 && e.shiftKey)) {
            // Shift + Tab
            e.preventDefault();
            c = currCell.prev();

        } else if (editing && e.which == 27) {
            editing = false;
            currCell.focus();
        }

        if (c.length > 0) {
            currCell = c;
            currCell.focus();
            edit();

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


    $("#notasinput").live('focusout', function () {

        if (codigos.length == 0) {
            var valActual = $(this).val();
            var lar = valActual.length;


            switch (lar) {

                case 1:
                    if (valActual == '0') {
                        $(this).val('0');

                    }
                    break;

                case 2:
                    if (valActual == '00') {
                        $(this).val('0');

                    } else if (parseInt(valActual) < 10 && parseInt(valActual) >= 1) {
                        $(this).val(valActual.substring(1));

                    } else {
                        $(this).val(valActual);
                    }
                    break;

                case 3:

                    if (valActual > 100) {
                        valActual = 100;
                    } else if (parseInt(valActual) < 100 && parseInt(valActual) >= 10) {

                        valActual = valActual.substring(1, 3);
                    } else if (parseInt(valActual) < 10) {
                        valActual = valActual.substring(2, 3)
                    } else if (valActual == '000') {

                        valActual = 0;

                    }
                    $(this).val(valActual);
                    break;


            }

        }


    });

    $("input").live('focus', function () {
        $(this).closest('tr').css("background-color", "green");


    });
    $("input").live('focusout', function () {
        $(this).closest('tr').css("background-color", "");
    });

    $('.estadoguia').live('change', function (e) {


        var idalumno = $(this).closest('div').attr('data-value');
        var elemento = $(this).closest('.grid-col');


        //Cambiamos el color
        var id = Number($(this).find(":checked").val());
        switch (id) {

            case 1:
                $(this).closest('div').attr('style', 'background-color:green');
                break;
            case 2:
                $(this).closest('div').attr('style', 'background-color:red');
                break;
            case 3:
                $(this).closest('div').attr('style', 'background-color:cyan');
                break;
            case 4:
                $(this).closest('div').attr('style', 'background-color:yellow');
                break;
            case 5:
                $(this).closest('div').attr('style', 'background-color:darkgray');
                break;


        }


        //obtenemos valor del curso
        var id = $(this).closest('div').attr('name');
        var valor = $(this).find(":checked").val();

        //creamos el json con los datos para enviar y guardar
        var edited = "{";
        edited += '"0":{"id":"' + id + '","valor":"' + valor + '"},';

        edited = edited.slice(0, -1);
        edited += "}";

        $.ajax(
            {
                global: false,
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: 'guardaestadoguia/',
                data: edited,
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
                        contabilizar(listass, elemento);


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
