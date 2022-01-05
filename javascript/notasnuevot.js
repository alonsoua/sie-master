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
    var texto;
    var auxnota;
    var asig;
    var largo = $("div#alumnos").children().length - 1;


    function promedio(asig, id, element) {
        $.ajax({
            global:false,
            type: "GET",
            url: "getpromedioperiodotrim",
            data: {as: asig, t: 2, id: id},
            async: true,
            dataType: "json",
            beforeSend: function (x) {
                if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                }
            },
            success: function (data) {

                var primero = $('#primer');
                var segundo = $('#segundo');
                var tercero = $('#tercero');
                var final = $('#final');
                var finalex = $('#finalex');


                if (tipo == 5) {
                    //Primer Trimestre

                    if (data[0]['primero'] > 39) {
                        $(primero.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['primeroconcepto'] + '</p>');

                    } else if (data[0]['primero'] > 0 && data[0]['primero'] <= 39) {
                        $(primero.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['primeroconcepto'] + '</p>');

                    } else if (data[0]['primero'] == 0) {

                        $(primero.children()[element]).find('p').replaceWith('<p>0</p>');
                    }

                    //Segundo Trimerstre

                    if (data[0]['segundo'] > 39) {
                        $(segundo.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['segundoconcepto'] + '</p>');

                    } else if (data[0]['segundo'] > 0 && data[0]['segundo'] <= 39) {
                        $(segundo.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['segundoconcepto'] + '</p>');

                    } else if (data[0]['segundo'] == 0) {

                        $(segundo.children()[element]).find('p').replaceWith('<p>0</p>');
                    }

                    //Tercer Trimerstre

                    if (data[0]['tercero'] > 39) {
                        $(tercero.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['terceroconcepto'] + '</p>');

                    } else if (data[0]['tercero'] > 0 && data[0]['tercero'] <= 39) {
                        $(tercero.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['terceroconcepto'] + '</p>');

                    } else if (data[0]['tercero'] == 0) {

                        $(tercero.children()[element]).find('p').replaceWith('<p>0</p>');
                    }

                    //Final

                    if (data[0]['final'] > 39) {
                        $(final.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['finalconcepto'] + '</p>');

                    } else if (data[0]['final'] > 0 && data[0]['final'] <= 39) {
                        $(final.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['finalconcepto'] + '</p>');

                    } else if (data[0]['final'] == 0) {

                        $(final.children()[element]).find('p').replaceWith('<p>0</p>');
                    }


                } else {
                    //Primer Trimestre

                    if (data[0]['primero'] > 39) {
                        $(primero.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['primero'] + '</p>');

                    } else if (data[0]['primero'] > 0 && data[0]['primero'] <= 39) {
                        $(primero.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['primero'] + '</p>');

                    } else if (data[0]['primero'] == 0) {

                        $(primero.children()[element]).find('p').replaceWith('<p>' + data[0]['primero'] + '</p>');
                    }

                    //Segundo Trimestre

                    if (data[0]['segundo'] > 39) {
                        $(segundo.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['segundo'] + '</p>');

                    } else if (data[0]['segundo'] > 0 && data[0]['segundo'] <= 39) {
                        $(segundo.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['segundo'] + '</p>');

                    } else if (data[0]['segundo'] == 0) {

                        $(segundo.children()[element]).find('p').replaceWith('<p>' + data[0]['segundo'] + '</p>');
                    }

                    //Tercer Trimestre

                    if (data[0]['tercero'] > 39) {
                        $(tercero.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['tercero'] + '</p>');

                    } else if (data[0]['tercero'] > 0 && data[0]['tercero'] <= 39) {
                        $(tercero.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['tercero'] + '</p>');

                    } else if (data[0]['tercero'] == 0) {

                        $(tercero.children()[element]).find('p').replaceWith('<p>' + data[0]['tercero'] + '</p>');
                    }

                    //Final

                    if (data[0]['final'] > 39) {
                        $(final.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['final'] + '</p>');

                    } else if (data[0]['final'] > 0 && data[0]['final'] <= 39) {
                        $(final.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['final'] + '</p>');

                    } else if (data[0]['final'] == 0) {

                        $(final.children()[element]).find('p').replaceWith('<p>' + data[0]['final'] + '</p>');
                    }

                    //Final+ Ex
                    if (data[0]['finalex'] > 39) {
                        $(finalex.children()[element]).find('p').replaceWith('<p class="azul">' + data[0]['finalex'] + '</p>');

                    } else if (data[0]['finalex'] > 0 && data[0]['finalex'] <= 39) {
                        $(finalex.children()[element]).find('p').replaceWith('<p class="red">' + data[0]['finalex'] + '</p>');

                    } else if (data[0]['finalex'] == 0) {

                        $(finalex.children()[element]).find('p').replaceWith('<p>' + data[0]['finalex'] + '</p>');
                    }
                }


            }


        });


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
                                $('#contenido').append('<div class="error mensajes">Esta asignatura no posee los conceptos creados para este período</div>');
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
                url: "getnotasasignaturatrim/id/" + $(this).attr('id'),
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

                        for (var i = 0; i < data.notas.length; i++) {

                            //Examen

                            if (data.notas[i]['tipoNota'] == 2 && data.notas[i]['tiempo'] == 6) { //Examen Final

                                $('#tblTabla').find('.ex').replaceWith('<div class="grid-col nota" id="' + data.notas[i]['idEvaluacion'] + '"><div class="grid-item grid-item--header examen" style="background-color: #edde34;"><div class="ventana" id="caja"><a class="button blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">Ex</a></div></div>');
                                $('#tblTabla').find('.exfinal').replaceWith('<div class="grid-col grid-col--fixed-right exfinal" id="finalex"><div class="grid-item grid-item--header finalex"><p class="head">Final + Examen</p></div>');
                                for (var k = 0; k < largo; k++) {

                                    if (data.notas[i]['alumnos'][k]['auth']) {
                                        if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                            $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas examen" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                        } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                            $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas examen" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                        } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                            $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas examen" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')

                                        }
                                        ind++;

                                    } else {

                                        $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas examen"  name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="not" >-</p></div>')

                                    }

                                    //Cargamos los Promedios

                                    if (data.promedios[k]['finalex'] > 39) {
                                        $("div#finalex").append('<div class="grid-item finalex"><p class="azul" >' + data.promedios[k]['finalex'] + '</p></div>')

                                    } else if (data.promedios[k]['finalex'] > 0 && data.promedios[k]['finalex'] <= 39) {
                                        $("div#finalex").append('<div class="grid-item finalex" ><p class="red" >' + data.promedios[k]['finalex'] + '</p></div>')

                                    } else if (data.promedios[k]['finalex'] == 0) {
                                        $("div#finalex").append('<div class="grid-item finalex" ><p>' + data.promedios[k]['finalex'] + '</p></div>')
                                     }

                                }


                            }

                            if (data.notas[i].tiempo == 3) {

                                if (data.notas[i]['coef'] == 2) {
                                    if(data.notas[i]['publicar'] == 1){

                                        $('#tblTabla').find('.primer').before('<div class="grid-col nota" id="' + data.notas[i]['idEvaluacion'] + '"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' <i class="icon-check-sign"></i></a></div></div>');
                                        $('#tblTabla').find('.primer').before('<div class="grid-col nota" id="coef' + data.notas[i]['idEvaluacion'] + '"><div class="grid-item grid-item--header coef"><a class="button medium " style="background: #3EBDFF;border: none;">' + data.notas[i]['fechaEvaluacion'] + ' <i class="icon-check-sign"></i></a></div>');


                                    }else{

                                        $('#tblTabla').find('.primer').before('<div class="grid-col nota" id="' + data.notas[i]['idEvaluacion'] + '"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' </a></div></div>');
                                        $('#tblTabla').find('.primer').before('<div class="grid-col nota" id="coef' + data.notas[i]['idEvaluacion'] + '"><div class="grid-item grid-item--header coef"><a class="button medium " style="background: #3EBDFF;border: none;">' + data.notas[i]['fechaEvaluacion'] + '</a></div>');


                                    }


                                    for (var k = 0; k < largo; k++) {


                                        if (tipo == 5) { //Si es Concepto
                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="red nota' + data.notas[i]['tiempo'] + '"  >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                //$(this).find('td').eq(h).replaceWith('<td tabindex="' + ind + '" class="notas red nota' + data.notas[i]['tiempo'] + '" id="coef" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</td>');
                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef">0</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p>0</p></div>')

                                            }
                                        } else {
                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="red nota' + data.notas[i]['tiempo'] + '"  >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef">0</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p>0</p></div>')

                                            }
                                        }

                                        ind++;

                                    }

                                } else {

                                    if (data.notas[i]['taller'] == 1) { //si es taller
                                        $('#tblTabla').find('.primer').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header" style="background-color: #f3b163;color: white">' + data.notas[i]['fechaEvaluacion'] + ' </div>');

                                        for (var k = 0; k < largo; k++) {


                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller" ><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller" ><p class="red nota' + data.notas[i]['tiempo'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {

                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller" ><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')

                                            }

                                            ind++;
                                        }


                                    } else {
                                        if(data.notas[i]['publicar'] == 1){
                                            $('#tblTabla').find('.primer').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div  class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' <i class="icon-check-sign"></i></a></div></div>');

                                        }else{
                                            $('#tblTabla').find('.primer').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div  class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' </a></div></div>');

                                        }
                                       for (var k = 0; k < largo; k++) {

                                            if (tipo == 5) { //Si es Concepto
                                                if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')

                                                } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                               } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>0</p></div>')

                                                }
                                            } else {


                                                if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                               } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="red nota' + data.notas[i]['tiempo'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                               } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {

                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')

                                                }
                                            }

                                            ind++;

                                        }


                                    }

                                }


                            } else if (data.notas[i].tiempo == 4) {

                                if (data.notas[i]['coef'] == 2) {

                                    if(data.notas[i]['publicar'] == 1){

                                        $('#tblTabla').find('.segundo').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' <i class="icon-check-sign"></i></a></div></div>');
                                        $('#tblTabla').find('.segundo').before('<div id="coef' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header coef"><a class="button medium " style="background: #3EBDFF;border: none;">' + data.notas[i]['fechaEvaluacion'] + ' <i class="icon-check-sign"></i></a></div>');


                                    }else{

                                        $('#tblTabla').find('.segundo').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' </a></div></div>');
                                        $('#tblTabla').find('.segundo').before('<div id="coef' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header coef"><a class="button medium " style="background: #3EBDFF;border: none;">' + data.notas[i]['fechaEvaluacion'] + '</a></div>');


                                    }
                                   for (var k = 0; k < largo; k++) {

                                        if (tipo == 5) { //Si es Concepto
                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef"class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="red nota' + data.notas[i]['tiempo'] + '"  >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                //$(this).find('td').eq(h).replaceWith('<td tabindex="' + ind + '" class="notas red nota' + data.notas[i]['tiempo'] + '" id="coef" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</td>');
                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef">0</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p>0</p></div>')

                                            }
                                        } else {
                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '" ><p id="coef" class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="red nota' + data.notas[i]['tiempo'] + '"  >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                //$(this).find('td').eq(h).replaceWith('<td tabindex="' + ind + '" class="notas red nota' + data.notas[i]['tiempo'] + '" id="coef" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</td>');
                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef">0</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p>0</p></div>')

                                            }
                                        }

                                        ind++;
                                    }


                                } else {

                                    if (data.notas[i]['taller'] == 1) { //si es taller
                                        //$('#tblTabla').find('.segundo').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><a class="button medium orange" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' </a></div>');
                                        $('#tblTabla').find('.segundo').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header" style="background-color: #f3b163;color: white">' + data.notas[i]['fechaEvaluacion'] + ' </div>');
                                        for (var k = 0; k < largo; k++) {


                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller" ><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller" ><p class="red nota' + data.notas[i]['tiempo'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                               $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller"><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')

                                            }

                                            ind++;
                                        }

                                    } else {
                                        $('#tblTabla').find('.segundo').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' </a></div></div>');
                                        for (var k = 0; k < largo; k++) {

                                            if (tipo == 5) { //Si es Concepto
                                                if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p  class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')

                                                } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p "class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                               } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>0</p></div>')

                                                }
                                            } else {


                                                if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                               } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="red nota' + data.notas[i]['tiempo'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                                } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {

                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')

                                                }

                                            }


                                            ind++;
                                        }

                                    }

                                }


                            }else if (data.notas[i].tiempo == 5) {

                                if (data.notas[i]['coef'] == 2) {

                                    if(data.notas[i]['publicar'] == 1){

                                        $('#tblTabla').find('.tercero').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' <i class="icon-check-sign"></i></a></div></div>');
                                        $('#tblTabla').find('.tercero').before('<div id="coef' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header coef"><a class="button medium " style="background: #3EBDFF;border: none;">' + data.notas[i]['fechaEvaluacion'] + ' <i class="icon-check-sign"></i></a></div>');


                                    }else{

                                        $('#tblTabla').find('.tercero').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' </a></div></div>');
                                        $('#tblTabla').find('.tercero').before('<div id="coef' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header coef"><a class="button medium " style="background: #3EBDFF;border: none;">' + data.notas[i]['fechaEvaluacion'] + '</a></div>');


                                    }
                                    for (var k = 0; k < largo; k++) {

                                        if (tipo == 5) { //Si es Concepto
                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef"class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="red nota' + data.notas[i]['tiempo'] + '"  >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                //$(this).find('td').eq(h).replaceWith('<td tabindex="' + ind + '" class="notas red nota' + data.notas[i]['tiempo'] + '" id="coef" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</td>');
                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef">0</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p>0</p></div>')

                                            }
                                        } else {
                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef" class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '" ><p id="coef" class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p class="red nota' + data.notas[i]['tiempo'] + '"  >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p id="coef">0</p></div>')
                                                $("div#coef" + data.notas[i]['idEvaluacion']).append('<div class="grid-item coef"><p>0</p></div>')

                                            }
                                        }

                                        ind++;
                                    }


                                } else {

                                    if (data.notas[i]['taller'] == 1) { //si es taller
                                       $('#tblTabla').find('.tercero').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header" style="background-color: #f3b163;color: white">' + data.notas[i]['fechaEvaluacion'] + ' </div>');
                                        for (var k = 0; k < largo; k++) {


                                            if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller" ><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                            } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller" ><p class="red nota' + data.notas[i]['tiempo'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                            } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item taller"><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')

                                            }

                                            ind++;
                                        }

                                    } else {
                                        $('#tblTabla').find('.tercero').before('<div id="' + data.notas[i]['idEvaluacion'] + '" class="grid-col nota" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="acciones/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['fechaEvaluacion'] + ' </a></div></div>');
                                        for (var k = 0; k < largo; k++) {

                                            if (tipo == 5) { //Si es Concepto
                                                if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p  class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')

                                                } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p "class="red nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['notaconconcepto'] + '</p></div>')
                                                } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p>0</p></div>')

                                                }
                                            } else {


                                                if (data.notas[i]['alumnos'][k]['nota'] > 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')
                                                } else if (data.notas[i]['alumnos'][k]['nota'] > 0 && data.notas[i]['alumnos'][k]['nota'] <= 39) {
                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class="red nota' + data.notas[i]['tiempo'] + '">' + data.notas[i]['alumnos'][k]['nota'] + '</p></div>')

                                                } else if (data.notas[i]['alumnos'][k]['nota'] == 0 || data.notas[i]['alumnos'][k]['nota'] == null) {

                                                    $("div#" + data.notas[i]['idEvaluacion']).append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '"><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')

                                                }

                                            }


                                            ind++;
                                        }

                                    }

                                }


                            }

                            h++;
                            ind++;


                        }
                        //Promedios Nuevos
                        //Primero
                        var primero = $('#primer');
                        var segundo = $('#segundo');
                        var tercero = $('#tercero');
                        var final = $('#final');

                        for (var k = 0; k < largo; k++) {

                            if (tipo == 5) {
                                //Primer Trimestre

                                if (data.promedios[k]['primero'] > 39) {
                                    $(primero.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['primeroconcepto'] + '</p>');

                                } else if (data.promedios[k]['primero'] > 0 && data.promedios[k]['primero'] <= 39) {
                                    $(primero.children()[k + 1]).append('<p class="red">' + data.promedios[k]['primeroconcepto'] + '</p>');

                                } else if (data.promedios[k]['primero'] == 0) {

                                    $(primero.children()[k + 1]).append('<p>0</p>');
                                }

                                //Segundo Trimestre

                                if (data.promedios[k]['segundo'] > 39) {
                                    $(segundo.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['segundoconcepto'] + '</p>');

                                } else if (data.promedios[k]['segundo'] > 0 && data.promedios[k]['segundo'] <= 39) {
                                    $(segundo.children()[k + 1]).append('<p class="red">' + data.promedios[k]['segundoconcepto'] + '</p>');

                                } else if (data.promedios[k]['segundo'] == 0) {

                                    $(segundo.children()[k + 1]).append('<p>0</p>');
                                }

                                //Tercer Trimestre

                                if (data.promedios[k]['tercero'] > 39) {
                                    $(tercero.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['terceroconcepto'] + '</p>');

                                } else if (data.promedios[k]['tercero'] > 0 && data.promedios[k]['tercero'] <= 39) {
                                    $(tercero.children()[k + 1]).append('<p class="red">' + data.promedios[k]['terceroconcepto'] + '</p>');

                                } else if (data.promedios[k]['tercero'] == 0) {

                                    $(tercero.children()[k + 1]).append('<p>0</p>');
                                }

                                //Final

                                if (data.promedios[k]['final'] > 39) {
                                    $(final.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['finalconcepto'] + '</p>');

                                } else if (data.promedios[k]['final'] > 0 && data.promedios[k]['final'] <= 39) {
                                    $(final.children()[k + 1]).append('<p class="red">' + data.promedios[k]['finalconcepto'] + '</p>');

                                } else if (data.promedios[k]['final'] == 0) {

                                    $(final.children()[k + 1]).append('<p>0</p>');
                                }

                            } else {
                                //Primer Trimestre

                                if (data.promedios[k]['primero'] > 39) {
                                    $(primero.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['primero'] + '</p>');

                                } else if (data.promedios[k]['primero'] > 0 && data.promedios[k]['primero'] <= 39) {
                                    $(primero.children()[k + 1]).append('<p class="red">' + data.promedios[k]['primero'] + '</p>');

                                } else if (data.promedios[k]['primero'] == 0) {

                                    $(primero.children()[k + 1]).append('<p>' + data.promedios[k]['primero'] + '</p>');
                                }

                                //Segundo Trimestre

                                if (data.promedios[k]['segundo'] > 39) {
                                    $(segundo.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['segundo'] + '</p>');

                                } else if (data.promedios[k]['segundo'] > 0 && data.promedios[k]['segundo'] <= 39) {
                                    $(segundo.children()[k + 1]).append('<p class="red">' + data.promedios[k]['segundo'] + '</p>');

                                } else if (data.promedios[k]['segundo'] == 0) {

                                    $(segundo.children()[k + 1]).append('<p>' + data.promedios[k]['segundo'] + '</p>');
                                }

                                //Tercer Trimestre

                                if (data.promedios[k]['tercero'] > 39) {
                                    $(tercero.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['tercero'] + '</p>');

                                } else if (data.promedios[k]['tercero'] > 0 && data.promedios[k]['tercero'] <= 39) {
                                    $(tercero.children()[k + 1]).append('<p class="red">' + data.promedios[k]['tercero'] + '</p>');

                                } else if (data.promedios[k]['tercero'] == 0) {

                                    $(tercero.children()[k + 1]).append('<p>' + data.promedios[k]['tercero'] + '</p>');
                                }

                                //Final

                                if (data.promedios[k]['final'] > 39) {
                                    $(final.children()[k + 1]).append('<p class="azul">' + data.promedios[k]['final'] + '</p>');

                                } else if (data.promedios[k]['final'] > 0 && data.promedios[k]['final'] <= 39) {
                                    $(final.children()[k + 1]).append('<p class="red">' + data.promedios[k]['final'] + '</p>');

                                } else if (data.promedios[k]['final'] == 0) {

                                    $(final.children()[k + 1]).append('<p>' + data.promedios[k]['final'] + '</p>');
                                }
                            }


                        }

                        //Fin Promedios Nuevos


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


                    }
                }
            });
            // }

            e.stopImmediatePropagation();
        }

    });

    var currCell = $('.grid-item').eq(4);
    var editing = false;


    $('.notas').live('click', function (e) {
        if ($(this).attr("tabindex")>=0) {
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
                    maxlength: '2',
                    id: 'notasinput',
                    blur: function () {

                        if (auxnota != 0 && this.value == "") {
                            this.value = auxnota;
                        }
                        if (auxnota == 0 && this.value == "") {
                            this.value = 0;
                        }
                        if (tip != 5) {
                            if ($this.attr('id') == "coef") {


                                if (this.value > 39) {
                                    $this.text(this.value);
                                    $(grid_col).next().children('div').eq(grid_index).find('p').text(this.value);


                                    if (!$this.hasClass("azul")) {
                                        $this.addClass("azul");
                                        $(grid_col).next().children('div').eq(grid_index).find('p').addClass("azul");
                                    }
                                    if ($this.hasClass("red")) {
                                        $this.removeClass("red");
                                        $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("red");
                                    }
                                } else if (this.value > 0 && this.value <= 39) {
                                    $this.text(this.value);
                                    $(grid_col).next().children('div').eq(grid_index).find('p').text(this.value);
                                    if (!$this.hasClass("red")) {
                                        $this.addClass("red");
                                        $(grid_col).next().children('div').eq(grid_index).find('p').addClass("red");
                                    }
                                    if ($this.hasClass("azul")) {
                                        $this.removeClass("azul");
                                        $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("azul");
                                    }
                                } else if (this.value == 0) {
                                    $this.text(0);
                                    $(grid_col).next().children('div').eq(grid_index).find('p').text(0);
                                    if ($this.hasClass("red")) {
                                        $this.removeClass("red");
                                        $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("red");
                                    }
                                    if ($this.hasClass("azul")) {
                                        $this.removeClass("azul");
                                        $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("azul");
                                    }
                                }
                            } else {

                                if (this.value > 39) {
                                    $this.text(this.value);

                                    if (!$this.hasClass("azul")) {
                                        $this.addClass("azul");
                                    }
                                    if ($this.hasClass("red")) {
                                        $this.removeClass("red");
                                    }
                                } else if (this.value > 0 && this.value <= 39) {
                                    $this.text(this.value);
                                    if (!$this.hasClass("red")) {
                                        $this.addClass("red");
                                    }
                                    if ($this.hasClass("azul")) {
                                        $this.removeClass("azul");
                                    }

                                } else if (this.value == 0) {
                                    $this.text(0);
                                    if ($this.hasClass("red")) {
                                        $this.removeClass("red");
                                    }
                                    if ($this.hasClass("azul")) {
                                        $this.removeClass("azul");
                                    }
                                }
                            }


                        } else {
                            //si la asignatura es con conceptos

                            for (var j = 0; j < datosconcepto.length; j++) {
                                if ($this.attr('id') == "coef") {
                                    if (datosconcepto[j]['concepto'] == this.value) {

                                        if (datosconcepto[j]['notaconcepto'] > 39) {
                                            $this.text(this.value);
                                            $(grid_col).next().children('div').eq(grid_index).find('p').text(this.value);
                                            if (!$this.hasClass("azul")) {
                                                $this.addClass("azul");
                                                $(grid_col).next().children('div').eq(grid_index).find('p').addClass("azul");
                                            }
                                            if ($this.hasClass("red")) {
                                                $this.removeClass("red");
                                                $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("red");
                                            }
                                        } else if (datosconcepto[j]['notaconcepto'] > 0 && datosconcepto[j]['notaconcepto'] <= 39) {
                                            $this.text(this.value);
                                            $(grid_col).next().children('div').eq(grid_index).find('p').text(this.value);
                                            if (!$this.hasClass("red")) {
                                                $this.addClass("red");
                                                $(grid_col).next().children('div').eq(grid_index).find('p').addClass("red");
                                            }
                                            if ($this.hasClass("azul")) {
                                                $this.removeClass("azul");
                                                $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("azul");
                                            }
                                        }
                                    } else if (this.value == 0) {
                                        $this.text(0);
                                        $(grid_col).next().children('div').eq(grid_index).find('p').text(0);
                                        if (!$this.hasClass("red")) {
                                            $this.removeClass("red");
                                            $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("red");
                                        }
                                        if ($this.hasClass("azul")) {
                                            $this.removeClass("azul");
                                            $(grid_col).next().children('div').eq(grid_index).find('p').removeClass("azul");
                                        }
                                    }
                                } else {

                                    if (datosconcepto[j]['concepto'] == this.value) {

                                        if (datosconcepto[j]['notaconcepto'] > 39) {
                                            $this.text(this.value);

                                            if (!$this.hasClass("azul")) {
                                                $this.addClass("azul");
                                            }
                                            if ($this.hasClass("red")) {
                                                $this.removeClass("red");
                                            }
                                        }

                                        else if (datosconcepto[j]['notaconcepto'] > 0 && datosconcepto[j]['notaconcepto'] <= 39) {
                                            $this.text(this.value);
                                            if (!$this.hasClass("red")) {
                                                $this.addClass("red");
                                            }
                                            if ($this.hasClass("azul")) {
                                                $this.removeClass("azul");
                                            }

                                        }


                                    } else if (this.value == 0) {
                                        $this.text(0);
                                        if (!$this.hasClass("red")) {
                                            $this.removeClass("red");
                                        }
                                        if ($this.hasClass("azul")) {
                                            $this.removeClass("azul");
                                        }
                                    }

                                }
                            }


                        }


                        if (aux != this.value) {
                            //obtenemos valor del curso
                            var id = $this.closest('div').attr('name');
                            var valor = this.value;

                            //creamos el json con los datos para enviar y guardar
                            var edited = "{";
                            if (tip != 5) {

                                if (valor.length == '1') {
                                    var valoragregado = valor;
                                    valoragregado += '0';
                                    edited += '"0":{"id":"' + id + '","valor":"' + valoragregado + '"},';
                                } else {
                                    edited += '"0":{"id":"' + id + '","valor":"' + valor.toString().replace(/\,/g, "") + '"},';
                                }
                            }
                            if (tip == 5) {
                                edited += '"0":{"id":"' + id + '","valor":"' + valor + '"},';
                            }


                            edited = edited.slice(0, -1);
                            edited += "}";

                            $.ajax(
                                {
                                    global:false,
                                    cache: false,
                                    async: true,
                                    dataType: 'json',
                                    type: 'POST',
                                    contentType: 'application/x-www-form-urlencoded',
                                    url: 'guardanotaseditar/',
                                    data: edited,
                                    success: function (data) {
                                    //     if (data.response == 'errorinserta') {
                                    //         $this.text(aux);
                                    //         $('#contenido').append('<div class="error mensajes">Ha ocurrido un error al insertar los datos,intente nuevamente</div>');
                                    //         $(document).ready(function () {
                                    //             setTimeout(function () {
                                    //                 $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                                    //             }, 3000);
                                    //         });
                                    //
                                    //     }
                                    //     if (data.response == 'errorsesion') {
                                    //         $('#contenido').append('<div class="error mensajes">Ha ocurrido un error de sesion,porfavor actualize la página</div>');
                                    //         $(document).ready(function () {
                                    //             setTimeout(function () {
                                    //                 $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                                    //             }, 3000);
                                    //         });
                                    //
                                    //     }
                                    //     if (data.response == 'exito') {
                                    //
                                    //         //Actualiza el Promedio
                                    //         var grid_index = $this.closest('div').index();
                                    //         var ida = $(".grid").find('#alumnos').children('div').eq(grid_index).attr('name');
                                    //         promedio(asig, ida, grid_index);
                                    //
                                    //
                                    //         $('#contenido').append('<div class="exito mensajes">Registro Actualizado con éxito</div>');
                                    //         $(document).ready(function () {
                                    //             setTimeout(function () {
                                    //                 $(".exito").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                                    //             }, 3000);
                                    //         });
                                    //     }
                                    //
                                    //
                                    // },
                                    // error: function (data) {
                                    //     $('#contenido').append('<div class="error mensajes">Se ha producido un error, intente nuevamente 2</div>');
                                    //     $(document).ready(function () {
                                    //         setTimeout(function () {
                                    //             $(".mensajes").fadeOut(800).fadeIn(800).fadeOut(500).fadeIn(500).fadeOut(300).remove();
                                    //         }, 3000);
                                    //     });
                                    // }

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

                                            //Actualiza el Promedio
                                            var grid_index = $this.closest('div').index();
                                            var ida = $(".grid").find('#alumnos').children('div').eq(grid_index).attr('name');
                                            promedio(asig, ida, grid_index);




                                            // <div class="ventana" style="display:inline">
                                            //
                                            //     <a  id="pdf" class="button medium"><i class="icon-print"></i> Imprimir</a>
                                            //
                                            // </div>

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

                                if (lar == 1 && !isNaN(check)) {
                                    if (check > '7') {
                                        $(this).val('');
                                        return false;
                                    }
                                }

                                else if (lar == '2' && !isNaN(check)) {
                                    if (check > '70' || check < '10') {
                                        $(this).val('');
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


            if (lar == '1') {
                if (valActual >= 7) {
                    $(this).val('70');

                } else if (valActual <= 7 && valActual > 0) {
                    var valornuevo = valActual;
                    valornuevo += '0';
                    $(this).val(valornuevo);
                } else if (valActual == 0) {
                    $(this).val(0);
                }

            }
            if (lar == '2') {
                if (valActual >= 70) {
                    $(this).val('70');


                }

            }
        }


    });

    $("input").live('focus', function () {
        $(this).closest('tr').css("background-color", "green");


    });
    $("input").live('focusout', function () {
        $(this).closest('tr').css("background-color", "");
    });


});
