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
    var largo = $("div#alumnos").children().length - 2;
    var listaid;

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



                let aux = 0;

                for (let i = 0; i < data.length; i++) {

                    if (data[i]['tiempoPonderacion'] == 1) {
                        porcentajes[i] = [];
                        porcentajes[i]['porcentaje_valor'] = data[i]['idEquivalencia'];
                        porcentajes[i]['porcentaje_inicio'] = data[i]['porcentaje_inicio'];
                        porcentajes[i]['porcentaje_final'] = data[i]['porcentaje_final'];
                        porcentajes[i]['equivalencia_nota'] = data[i]['equivalencia_nota'];
                        porcentajes[i]['logro'] = data[i]['nivelLogro'];

                    } else if (data[i]['tiempoPonderacion'] == 2) {
                        porcentajess[aux] = [];
                        porcentajess[aux]['porcentaje_valor'] = data[i]['idEquivalencia'];
                        porcentajess[aux]['porcentaje_inicio'] = data[i]['porcentaje_inicio'];
                        porcentajess[aux]['porcentaje_final'] = data[i]['porcentaje_final'];
                        porcentajess[aux]['equivalencia_nota'] = data[i]['equivalencia_nota'];
                        porcentajess[aux]['logro'] = data[i]['nivelLogro'];
                        aux++;
                    }

                }

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


    var arr_u = [
        {val: 0, text: '-'},
        {val: 1, text: 'Entregada '},
        {val: 2, text: 'No Entregada'},
    ];

    var arr_d = [
        {val: 0, text: '-'},
        {val: 1, text: 'RetroAlimentada'},
        {val: 2, text: 'No RetroAlimentada'},

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


    // RQM 41
    var sel_u_sem2 = $('<select>');
    sel_u_sem2.attr('class', 'estadoguia_u_sem2');
    $(arr_u).each(function () {
        sel_u_sem2.append($("<option>").attr('value', this.val).text(this.text));
    });

    var sel_d_sem2 = $('<select>');
    sel_d_sem2.attr('class', 'estadoguia_d_sem2');
    $(arr_d).each(function () {
        sel_d_sem2.append($("<option>").attr('value', this.val).text(this.text));
    });


    function actualizanota(elemento, thisElement) {

        var id = $(elemento).attr('name');

        var tipo;
        if  ($(elemento).find(".estadoguia_u").val() === undefined) {

            // Solo cambia los vaores del input porcentaje, por el select de cumplimiento
            //if (thisElement.attr('class') != 'estadoguia_d_sem2') {
                tipo = $(elemento).find(".estadoguia_d_sem2").val();
                // Validaciones de cumplimiento sobre el input porcentaje
                var input = $('input[data-idvalorguia='+id+']');
                if (tipo == 0) {
                    input[0].disabled = true;
                    input.val(null);
                }else if (tipo == 1) {
                    input[0].disabled = false;

                } else if (tipo == 2) {
                    input[0].disabled = false;

                }

            porcentaje(input, 0);
            //}

        } else {
            tipo = $(elemento).find(".estadoguia_u").val();
            switch (tipo) {
                case "0":
                    $("div[name=" + id + "]").find('p').replaceWith('<p>-</p>');
                    break;
                case "1":
                    $("div[name=" + id + "]").find('p').replaceWith('<p>100%</p>');
                    break;
                case "2":
                    $("div[name=" + id + "]").find('p').replaceWith('<p>0%</p>');
                    break;
            }
        }
    }


    function notafinal(elemento,porcentaje,nota, sem, pf,nf) {

        var id = $(elemento).attr('data-value');
        var logro=null;

        if (sem == 1) {

            if(porcentaje!=null){
                for(var u=0;u<porcentajes.length;u++){
                    if(porcentaje>=porcentajes[u]['porcentaje_inicio'] && porcentaje<=porcentajes[u]['porcentaje_final']){
                        logro=porcentajes[u]['logro'];
                        porcentaje=porcentaje+"%";
                        break;
                    }

                }


            }else{
                porcentaje="-";
                logro="-";
                nota="-";
            }

            if (nota > 39) {
                $("div.nota_final[name=" + id + "]").find('p').replaceWith('<p class="azul">'+nota+'</p>');

            } else if (nota > 0 && nota <= 39) {

                $("div.nota_final[name=" + id + "]").find('p').replaceWith('<p class="red">'+nota+'</p>');

            } else if (nota == 0) {


                $("div.nota_final[name=" + id + "]").find('p').replaceWith('<p>'+nota+'</p>');
            }
            else if (nota == "-") {


                $("div.nota_final[name=" + id + "]").find('p').replaceWith('<p>-</p>');
            }


            $("div.porcentaje[name=" + id + "]").find('p').replaceWith('<p>'+porcentaje+'</p>');
            $("div.nivel[name=" + id + "]").find('p').replaceWith('<p>'+logro+'</p>');
        }
        else if (sem == 2) {
            var idGuia = $(elemento).attr('name');
            getConceptoyNota(pf, idGuia);
            console.log(elemento);
            var title = "";
            var promedionota;
            if (porcentaje != null){
                for (var u = 0; u < porcentajess.length; u++){
                    if (porcentaje >= porcentajess[u]['porcentaje_inicio'] && porcentaje <= porcentajess[u]['porcentaje_final']) {
                        logro = porcentajess[u]['logro'];
                        porcentaje = porcentaje +"%";
                        break;
                    }

                }
            } else {
                porcentaje = "-";
                logro = "-";
                nota = "-";
            }


            promedionota = nota;



            if (nota > 39) {
                $("div.nota_final_sem2[name=" + id + "]").find('p').replaceWith('<p class="azul" title="'+title+'">'+promedionota+'</p>');

            } else if (nota > 0 && nota <= 39) {

                $("div.nota_final_sem2[name=" + id + "]").find('p').replaceWith('<p class="red" title="'+title+'">'+promedionota+'</p>');

            } else if (nota == 0) {

                $("div.nota_final_sem2[name=" + id + "]").find('p').replaceWith('<p>'+promedionota+'</p>');
            }
            else if (nota == "-") {
                $("div.nota_final_sem2[name=" + id + "]").find('p').replaceWith('<p>-</p>');
            }

            $("div.porcentaje_sem2[name=" + id + "]").find('p').replaceWith('<p>'+porcentaje+'</p>');
            $("div.nivel_sem2[name=" + id + "]").find('p').replaceWith('<p>'+logro+'</p>');

        }

    }


    function contabilizar(elemento, sem) {
        if (elemento != null) {
            for (var k = 0; k < 4; k++) {

                if (sem == 1) {

                    switch (k) {

                        case 0:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_u option:selected[value="1"]').length;
                            $(elemento).closest('div.grid-col').find('.total_entregada').replaceWith('<p class="total_entregada" style="color: white">Entregada: ' + total + '</p>');
                            break;
                        case 1:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_u option:selected[value="2"]').length;
                            $(elemento).closest('div.grid-col').find('.total_noentregada').replaceWith('<p class="total_noentregada" style="color: white">No Entregada: ' + total + '</p>');
                            break;
                        case 2:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_d option:selected[value="1"]').length;
                            $(elemento).closest('div.grid-col').find('.total_retro').replaceWith('<p class="total_retro" style="color: white">RetroAlimentada: ' + total + '</p>');
                            break;

                        case 3:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_d option:selected[value="2"]').length;
                            $(elemento).closest('div.grid-col').find('.total_noretro').replaceWith('<p class="total_noretro" style="color: white">No RetroAlimentada: ' + total + '</p>');
                            break;

                    }
                } else if (sem == 2) {

                    switch (k) {

                        case 0:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_u_sem2 option:selected[value="1"]').length;
                            $(elemento).closest('div.grid-col').find('.total_entregada_sem2').replaceWith('<p class="total_entregada_sem2" style="color: white">Entregada: ' + total + '</p>');
                            break;
                        case 1:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_u_sem2 option:selected[value="2"]').length;
                            $(elemento).closest('div.grid-col').find('.total_noentregada_sem2').replaceWith('<p class="total_noentregada_sem2" style="color: white">No Entregada: ' + total + '</p>');
                            break;
                        case 2:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_d_sem2 option:selected[value="1"]').length;
                            $(elemento).closest('div.grid-col').find('.total_retro_sem2').replaceWith('<p class="total_retro_sem2" style="color: white">RetroAlimentada: ' + total + '</p>');
                            break;

                        case 3:
                            var total = $(elemento).closest('div.grid-col').find('.estadoguia_d_sem2 option:selected[value="2"]').length;
                            $(elemento).closest('div.grid-col').find('.total_noretro_sem2').replaceWith('<p class="total_noretro_sem2" style="color: white">No RetroAlimentada: ' + total + '</p>');
                            break;
                    }
                }
            }
        }
    }
    // F RQM 41

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

            codigos = [];

            $.ajax({
                //global: false,
                type: "GET",
                url: "getvaloresguia/id/" + $(this).attr('id'),
                async: true,
                dataType: "json",
                beforeSend: function (x) {
                    if (x && x.overrideMimeType) {
                        x.overrideMimeType("application/j-son;charset=UTF-8");
                    }
                },
                success: function (data) {

                    console.log(data);
                    if (data==null || data== 'Null') {

                    } else {
                        var h = 4;
                        var g = 35;
                        var aux = 4;

                        var ind = 0;
                        var indg = 0;
                        var inde = 0;

                        listaid = data.notas[0]['alumnos'];

                        for (var i = 0; i < data.notas.length; i++) {

                            if (data.notas[i].tiempoGuia == 1) {

                                //Ponderado 10%
                                $('#tblTabla').find('.primer').before(''
                                + '<div id="guia' + data.notas[i]['idGuia'] + '" class="grid-col" id="nota">'
                                    + '<div class="grid-item grid-item--header">'
                                        + '<div class="ventana" id="caja">'

                                            + '<a class="button medium blue" '
                                            + 'title="' + data.notas[i]['nombreguia'] + '" '
                                            + 'href="accionesguia/id/' + data.notas[i]['idGuia'] + '" '
                                            + 'rel="ventana' + (ind + 1) + '">' + data.notas[i]['nombreguia'] + '</a>'

                                        + '</div>'

                                        + '<div style="display: -webkit-inline-flex;"><div class="grid-item-pond-header" style="background-color: orange" >Cumplimiento</div>'
                                        + '<div class="grid-item-pond-header" style="background-color: green" >Formativa</div>'

                                    + '</div>'
                                + '</div>');

                                for (var k = 0; k < largo; k++) {

                                    $("div#guia" + data.notas[i]['idGuia']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idValorGuia'] + '"></div>');
                                    $("div.selector[name='" + data.notas[i]['alumnos'][k]['idValorGuia'] + "']").append('<div class="grid-item-pond" style="background-color: orange" >' + sel_u[0].outerHTML + '</div>');
                                    $("div.selector[name='" + data.notas[i]['alumnos'][k]['idValorGuia'] + "']").append('<div class="grid-item-pond" style="background-color: green" >' + sel_d[0].outerHTML + '</div>');
                                    $(".selector[name='" + data.notas[i]['alumnos'][k]['idValorGuia'] + "'] .estadoguia_u option[value='" + data.notas[i]['alumnos'][k]['valorGuias'] + "']").prop('selected', true);
                                    $(".selector[name='" + data.notas[i]['alumnos'][k]['idValorGuia'] + "'] .estadoguia_d option[value='" + data.notas[i]['alumnos'][k]['valorFormativa'] + "']").prop('selected', true);

                                    indg++;
                                }


                                //Nota

                                $('#tblTabla').find('.primer').before('<div id="porcentaje' + data.notas[i]['idGuia'] + '" class="grid-col" id="nota"><div class="grid-item grid-item--header"><p>Porcentaje ' + data.notas[i]['nombreguia'] + '</p></div>');

                                for (var k = 0; k < largo; k++) {



                                    switch (data.notas[i]['alumnos'][k]['valorGuias']) {

                                        case 0:
                                            $("div#porcentaje" + data.notas[i]['idGuia']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idValorGuia'] + '" style="text-align: center;display: block;"><p>-</p></div>');
                                            break;

                                        case 1:
                                            $("div#porcentaje" + data.notas[i]['idGuia']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idValorGuia'] + '" style="text-align: center;display: block;"><p>100%</p></div>');
                                            break;

                                        case 2:
                                            $("div#porcentaje" + data.notas[i]['idGuia']).append('<div class="grid-item selector" tabindex="e' + inde + '" name="' + data.notas[i]['alumnos'][k]['idValorGuia'] + '" style="text-align: center;display: block;"><p>0%</p></div>');
                                            break;

                                    }

                                    inde++;
                                }

                                //Estadistica
                                for (var k = 0; k < 1; k++) {

                                    switch (k) {

                                        case 0:

                                            var total_1 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_u option:selected[value="1"]').length;
                                            var total_2 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_u option:selected[value="2"]').length;
                                            var total_3 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_d option:selected[value="1"]').length;
                                            var total_4 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_d option:selected[value="2"]').length;

                                            $("div#guia" + data.notas[i]['idGuia']).append('<div style="display: -webkit-inline-flex;"><div class="grid-item-pond-header" style="background-color: orange;height: 75px; width: 164px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="total_entregada" style="color:white;">Entregada: ' + total_1 + '</p><p class="total_noentregada"  style="color:white;">No Entregada: ' + total_2 + '</p></div><div class="grid-item-pond-header" style="background-color: green;height: 75px;width: 164px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="total_retro"  style="color:white;">RetroAlimentada: ' + total_3 + '</p><p class="total_noretro"  style="color:white;">No RetroAlimentada: ' + total_4 + '</p></div></div>');
                                            break;

                                    }

                                    $("div#" + data.notas[i]['idGuia']).append('<div class="grid-item selector" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p></p></div>');
                                    $("div#porcentaje" + data.notas[i]['idGuia']).append('<div class="grid-item" style="height: 80px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnos'][k]['idValorGuia'] + '"><p></p></div>');

                                }

                            // RQM 41
                            } else if (data.notas[i].tiempoGuia == 2) {

                                // - SEMESTRE 2 - //

                                // MOSTRAR GUIAS
                                $('#tblTabla').find('.segundo').before(''
                                + '<div id="guia' + data.notas[i]['idGuia'] + '" class="grid-col" id="nota">'
                                    + '<div class="grid-item grid-item--header">'
                                        + '<div class="ventana" id="caja">'

                                            + '<a class="button medium blue" '
                                            + 'title="' + data.notas[i]['nombreguia'] + '" '
                                            + 'href="accionesguia/id/' + data.notas[i]['idGuia'] + '" '
                                            + 'rel="ventana' + (ind + 1) + '">' + data.notas[i]['nombreguia'] + '</a>'

                                        + '</div>'

                                        + '<div style="display: -webkit-inline-flex;">'
                                        + '<div class="grid-item-pond-header" style="background-color: green" >Formativa</div>'

                                        // Porcentaje
                                        + '<div class="grid-item-pond-header" style="width:100px;" id="porcentaje_sem2' + data.notas[i]['idGuia'] + '">Porcentaje Ingresado</div>'
                                        + '<div class="grid-item-pond-header" style="width:50px;" id="porcentajef_sem2' + data.notas[i]['idGuia'] + '">Porcentaje Final</div>'
                                        // Concepto
                                        + '<div class="grid-item-pond-header" style="width:50px;" id="concepto_sem2' + data.notas[i]['idGuia'] + '">Concepto</div>'
                                        // Nota
                                        + '<div class="grid-item-pond-header" style="width:50px;" id="nota_sem2' + data.notas[i]['idGuia'] + '">Nota</div>'

                                    + '</div>'
                                + '</div>');

                                for (var k = 0; k < largo; k++) {

                                    var tipo = data.notas[i]['alumnosSegSem'][k]['valorFormativa'];
                                    $("div#guia" + data.notas[i]['idGuia']).append('<div class="grid-item selector ' + data.notas[i]['alumnosSegSem'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnosSegSem'][k]['idAlumnosActual'] + '" tabindex="g' + indg + '" name="' + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + '"></div>');
                                    $("div[name='" + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + "']").append('<div class="grid-item-pond" style="background-color: green" >' + sel_d_sem2[0].outerHTML + '</div>');
                                    $(".selector[name='" + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + "'] .estadoguia_d_sem2 option[value='" + tipo + "']").prop('selected', true);

                                    // Porcentaje INPUT
                                    var porcentajeGuia;
                                    if (tipo != 0) {
                                        porcentajeGuia = data.notas[i]['alumnosSegSem'][k]['porcentajeGuia'] ===  null ? '': data.notas[i]['alumnosSegSem'][k]['porcentajeGuia'];
                                    }
                                    else {
                                        porcentajeGuia = '';
                                    }


                                    $("div[name='" + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + "']").append('<div class="grid-item-pond" style="width:102px;">'
                                        + '<input type="number" '
                                        + ' class="input_porcentaje_sem2" '
                                        + ' data-idValorGuia="' + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + '"'
                                        + ' style="margin-left: 20px; margin-right: 20px; margin-top: 11px; width:50%;" '
                                        + ' placeholder="%"'
                                        + ' min="0"'
                                        + ' value="'+porcentajeGuia+'"'
                                        + ' id="'+data.notas[i]['alumnosSegSem'][k]['idValorGuia']+'_porcentaje_sem2">'
                                    + '</div>');


                                    var input = $("#"+data.notas[i]['alumnosSegSem'][k]['idValorGuia']+"_porcentaje_sem2");

                                    if (tipo == 0) {
                                        input[0].disabled = true;
                                        input.val(null);
                                    } else {
                                        input[0].disabled = false;
                                    }


                                    // Porcentaje Final
                                    $("div[name='" + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + "']").
                                    append('<div class="grid-item-pond" id="porcentajef_sem2_'+data.notas[i]['alumnosSegSem'][k]['idValorGuia']+'" style="width:52px; padding-top: 5px;"><p>-</p></div>');
                                    // Concepto
                                    $("div[name='" + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + "']").
                                    append('<div class="grid-item-pond" id="concepto_sem2_'+data.notas[i]['alumnosSegSem'][k]['idValorGuia']+'" style="width:52px; padding-top: 5px;"><p>-</p></div>');
                                    // Nota
                                    $("div[name='" + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + "']").
                                    append('<div class="grid-item-pond" id="nota_sem2_'+ data.notas[i]['alumnosSegSem'][k]['idValorGuia'] +'" style="width:52px; padding-top: 5px;"><p>-</p></div>');

                                    getConceptoyNota(data.notas[i]['alumnosSegSem'][k]['porcentajeGuiaFinal'], data.notas[i]['alumnosSegSem'][k]['idValorGuia']);


                                    indg++;
                                }

                                //Estadistica
                                for (var k = 0; k < 1; k++) {

                                    switch (k) {

                                        case 0:

                                            var total_1 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_u_sem2 option:selected[value="1"]').length;
                                            var total_2 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_u_sem2 option:selected[value="2"]').length;
                                            var total_3 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_d_sem2 option:selected[value="1"]').length;
                                            var total_4 = $("div#guia" + data.notas[i]['idGuia']).find('.estadoguia_d_sem2 option:selected[value="2"]').length;

                                            $("div#guia" + data.notas[i]['idGuia']).append('<div style="display: -webkit-inline-flex;"><div class="grid-item-pond-header" style="background-color: green;height: 75px;width: 164px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnosSegSem'][k]['idNotas'] + '"><p class="total_retro_sem2"  style="color:white;">RetroAlimentada: ' + total_3 + '</p><p class="total_noretro_sem2"  style="color:white;">No RetroAlimentada: ' + total_4 + '</p></div></div>');
                                            break;

                                    }

                                    $("div#" + data.notas[i]['idGuia']).append('<div class="grid-item selector" tabindex="es' + ind + '" name="' + data.notas[i]['alumnosSegSem'][k]['idNotas'] + '"><p></p></div>');
                                    $("div#porcentaje" + data.notas[i]['idGuia']).append('<div class="grid-item" style="height: 80px;" tabindex="es' + ind + '" name="' + data.notas[i]['alumnosSegSem'][k]['idValorGuia'] + '"><p></p></div>');

                                }
                            }
                            //F RQM 41
                            h++;
                            ind++;
                            inde++;
                        }

                        //Promedios Nuevos
                        //Primero
                        var primero = $('#primer');
                        var primerol = $('#primerl');
                        var primeron = $('#primern');

                        // Segundo Semestre
                        var segundo = $('#segundo');
                        var segundol = $('#segundol');
                        var segundon = $('#segundon');

                        for (var k = 0; k < largo; k++) {

                            //Primer Semestre
                            if (data.notaFinal[k]['porcentajenota'] >= 0 && data.notaFinal[k]['porcentajenota'] != null) {
                                $(primero.children()[k + 1]).append('<p >' + data.notaFinal[k]['porcentajenota'] + '%</p>');
                                var logro;
                                for (var u = 0;u < porcentajes.length; u++) {
                                    if (data.notaFinal[k]['porcentajenota'] >= porcentajes[u]['porcentaje_inicio']
                                        && data.notaFinal[k]['porcentajenota'] <= porcentajes[u]['porcentaje_final']) {
                                        logro = porcentajes[u]['logro'];
                                        break;
                                    }
                                }

                                $(primerol.children()[k + 1]).append('<p>' + logro+ '</p>');
                                if (data.notaFinal[k]['nota'] > 39) {
                                    $(primeron.children()[k + 1]).append('<p class="azul">' + data.notaFinal[k]['nota'] + '</p>');

                                } else if (data.notaFinal[k]['nota'] > 0 && data.notaFinal[k]['nota'] <= 39) {

                                    $(primeron.children()[k + 1]).append('<p class="red">' + data.notaFinal[k]['nota'] + '</p>');

                                } else if (data.notaFinal[k]['nota'] == 0) {


                                    $(primeron.children()[k + 1]).append('<p>' + data.notaFinal[k]['nota'] + '</p>');
                                }

                            } else if (data.notaFinal[k]['porcentajenota'] == null) {
                                $(primero.children()[k + 1]).append('<p>-</p>');
                                $(primerol.children()[k + 1]).append('<p>-</p>');
                                $(primeron.children()[k + 1]).append('<p>-</p>');

                            }

                            // RQM 41
                            //Segundo Semestre
                            if (data.notaFinalSegSem[k]['porcentajenota'] >= 0 && data.notaFinalSegSem[k]['porcentajenota'] != null) {
                                $(segundo.children()[k + 1]).append('<p >' + data.notaFinalSegSem[k]['porcentajenota'] + '%</p>');
                                var logro;
                                for (var u = 0;u < porcentajess.length; u++) {
                                    if (data.notaFinalSegSem[k]['porcentajenota'] >= porcentajess[u]['porcentaje_inicio']
                                        && data.notaFinalSegSem[k]['porcentajenota'] <= porcentajess[u]['porcentaje_final']) {
                                        logro = porcentajess[u]['logro'];
                                        break;
                                    }
                                }
                                var textoPunto = '';

                                if (data.notaFinalSegSem[k]['punto']) {
                                    textoPunto = '+1';
                                }

                                $(segundol.children()[k + 1]).append('<p>' + logro+ '</p>');
                                if (data.notaFinalSegSem[k]['nota'] > 39) {
                                    $(segundon.children()[k + 1]).append('<p class="azul">' + data.notaFinalSegSem[k]['nota']+'</p>');

                                } else if (data.notaFinalSegSem[k]['nota'] > 0 && data.notaFinalSegSem[k]['nota'] <= 39) {

                                    $(segundon.children()[k + 1]).append('<p class="red">' + data.notaFinalSegSem[k]['nota'] + '</p>');

                                } else if (data.notaFinalSegSem[k]['nota'] == 0) {


                                    $(segundon.children()[k + 1]).append('<p>' + data.notaFinalSegSem[k]['nota'] + '</p>');
                                }

                            } else if (data.notaFinalSegSem[k]['porcentajenota'] == null) {
                                $(segundo.children()[k + 1]).append('<p>-</p>');
                                $(segundol.children()[k + 1]).append('<p>-</p>');
                                $(segundon.children()[k + 1]).append('<p>-</p>');

                            }
                            //F RQM 41

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

    // RQM 41
    // Porcentage
    $('.input_porcentaje_sem2').live('keyup', function (e) {
        var input = $(this);
        porcentaje(input, e);
    });

    $('.input_porcentaje_sem2').live('change', function (e) {
        var input = $(this);
        porcentaje(input, e);
    });


    function porcentaje(input, e) {

        var val = input.val();

        if (val === '') {
            input.val(null);
        }

        if (val > 100) {
            input.val(100);
            val = 100;
        }

        var idGuia = input.attr('id').split('_');


        //getConceptoyNota(val, idGuia);


        var elemento = $('.selector[name="'+idGuia[0]+'"]');

        var idValorGuia = input.attr('data-idValorGuia');
        var valor_u_sem2 = $(elemento).find(".estadoguia_u_sem2").val();
        var valor_d_sem2 = $(elemento).find(".estadoguia_d_sem2").val();

        // guardar porcentaje y mostrar notas finales
        var Datos = {
            "id": idValorGuia,
            "valor": valor_u_sem2,
            "valor_f": valor_d_sem2,
            "porcentaje": val,
        };

        $.ajax(
            {
                global: false,
                cache: false,
                async: true,
                dataType: 'json',
                type: 'POST',
                contentType: 'application/x-www-form-urlencoded',
                url: 'guardaguianotasegsem/',
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
                        notafinal(elemento,data.p,data.n, 2, data.pf,data.nf);
                        contabilizar(elemento, 2);


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

                    } else if (data.response == 'sinporcentaje') {

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
            if (e != 0) {
                e.stopImmediatePropagation();
            }

    }


    function getConceptoyNota (porc, idGuia) {
        var nota;
        var concepto;

        if (porc) {
            for (let u = 0; u < porcentajess.length; u++) {

                if (porc >= porcentajess[u]['porcentaje_inicio'] && porc <= porcentajess[u]['porcentaje_final']) {

                    concepto = porcentajess[u]['logro'];
                    nota = porcentajess[u]['equivalencia_nota'];
                    $("div#porcentajef_sem2_"+idGuia).find('p').replaceWith('<p>'+porc+'</p>');
                    $("div#concepto_sem2_"+idGuia).find('p').replaceWith('<p>'+concepto+'</p>');
                    $("div#nota_sem2_"+idGuia).find('p').replaceWith('<p>'+nota+'</p>');

                    break;
                }
            }
        }
        else{
            $("div#porcentajef_sem2_"+idGuia).find('p').replaceWith('<p>-</p>');
            $("div#concepto_sem2_"+idGuia).find('p').replaceWith('<p>-</p>');
            $("div#nota_sem2_"+idGuia).find('p').replaceWith('<p>-</p>');
        }
    }
    //F RQM 41

    $('select').live('change', function (e) {


        var idalumno = $(this).closest('div.selector').attr('data-value');
        var elemento = $(this).closest('.selector');

        //obtenemos valor del curso
        //var id = $(this).closest('div').attr('name');
        var valor_u = $(elemento).find(".estadoguia_u").val();
        var valor_d = $(elemento).find(".estadoguia_d").val();

        // RQM 41
        var thisElement = $(this);
        var valor_u_sem2 = $(elemento).find(".estadoguia_u_sem2").val();
        var valor_d_sem2 = $(elemento).find(".estadoguia_d_sem2").val();
        var porcentaje = $(elemento).find(".input_porcentaje_sem2").val();

        actualizanota(elemento, thisElement);


        var ids = $(this).closest('.selector').attr('name');

        var Datos;
        var semestre;

        if (valor_u_sem2 === undefined && valor_d_sem2 === undefined) {

            semestre = 1;
            Datos = {
                "id": ids,
                "valor": valor_u,
                "valor_f": valor_d,
            };
            $.ajax(
                {
                    global: false,
                    cache: false,
                    async: true,
                    dataType: 'json',
                    type: 'POST',
                    contentType: 'application/x-www-form-urlencoded',
                    url: 'guardaguianota/',
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


                            notafinal(elemento,data.p,data.n,1,false);
                            contabilizar(elemento, 1);


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
        }  else {

            semestre = 2;
            Datos = {
                "id": ids,
                "valor_f": valor_d_sem2,
                "porcentaje": porcentaje
            };
        }
        // F RQM 41

        e.stopImmediatePropagation();

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

});
