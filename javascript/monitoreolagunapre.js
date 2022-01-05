$(function () {

    var us = $('#us').val();
    var tipo;
    var nuevo;
    var url = $('#nuevo').attr('href');
    var tabla = $('#tblTabla').html();

    var codigos = [];
    var datosconcepto = [];
    var texto;
    var auxnota;
    var asig;
    var largo = $("div#alumnos").children().length - 1;
    var listaid;

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

    function actualizanota(elemento,promedio,porcentaje,concepto) {
        $(elemento).closest('.selector').find('.notasfinal p').replaceWith('<p>' + promedio+ '</p>');
        if(porcentaje!=null){
            $(elemento).closest('.selector').find('.porcentajefinal p').replaceWith('<p>' + porcentaje+ '%</p>');
        }else{
            $(elemento).closest('.selector').find('.porcentajefinal p').replaceWith('<p>-</p>');
        }
        if(concepto!=null){
            $(elemento).closest('.selector').find('.conceptofinal p').replaceWith('<p>' + concepto+ '</p>');

        }else{
            $(elemento).closest('.selector').find('.conceptofinal p').replaceWith('<p>-</p>');
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
            codigos = [];

            $.ajax({
                //global: false,
                type: "GET",
                url: "getnotasguialagpre/id/" + $(this).attr('id'),
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

                            if (data.notas[i].tiempo == 1 || data.notas[i].tiempo == 2 || data.notas[i].tiempo == 3 || data.notas[i].tiempo == 4 ||data.notas[i].tiempo == 5 ) {
                                $('#tblTabla').find('.primer').before('<div id="guia' + data.notas[i]['idEvaluacion'] + '" name="'+data.notas[i]['idEvaluacion']+'" class="grid-col" id="nota"><div class="grid-item grid-item--header"><div class="ventana" id="caja"><a class="button medium blue" title="' + data.notas[i]['contenido'] + '" href="accionesguialag/id/' + data.notas[i]['idEvaluacion'] + '" rel="ventana' + (ind + 1) + '">' + data.notas[i]['contenido'] + ' </a></div><div style="display: -webkit-inline-flex;"><div class="grid-item-pond-header" style="background-color: orange" >Desempeño</div><div class="grid-item-pond-header" style="background-color: green" >Participación</div><div class="grid-item-pond-header" style="background-color: cyan" >Cumplimiento</div><div class="grid-item-pond-header" style="background-color: greenyellow" >Nota Final</div><div class="grid-item-pond-header">% Logro</div><div class="grid-item-pond-header">Nivel de Logro</div></div></div>');


                            }

                            for (var k = 0; k < largo; k++) {

                                if(data.notas[i]['alumnos'][k]['valorGuia']) {

                                    $("div#guia" + data.notas[i]['idEvaluacion']).append('<div class="grid-item selector ' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" data-value="' + data.notas[i]['alumnos'][k]['idAlumnosActual'] + '" tabindex="g' + indg + '" name="' + data.notas[i]['alumnos'][k]['idNotas'] + '" style="width: 292px"></div>');

                                    for(let j=1;j<4;j++){
                                        if (data.notas[i]['alumnos'][k]['nota_'+j] > 39) {
                                            $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotasGuia'] + '"><p class="azul nota' + data.notas[i]['tiempo'] + '" >' + data.notas[i]['alumnos'][k]['nota_'+j] + '</p></div>')
                                        } else if (data.notas[i]['alumnos'][k]['nota_'+j] > 0 && data.notas[i]['alumnos'][k]['nota_'+j] <= 39) {
                                            $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotasGuia'] + '"><p class="red nota' + data.notas[i]['tiempo'] + '">' + data.notas[i]['alumnos'][k]['nota_'+j] + '</p></div>')

                                        } else if (data.notas[i]['alumnos'][k]['nota_'+j] == 0 || data.notas[i]['alumnos'][k]['nota_'+j] == null) {

                                            $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item notas" tabindex="' + ind + '" name="' + data.notas[i]['alumnos'][k]['idNotasGuia'] + '"><p class=" nota' + data.notas[i]['tiempo'] + '" >0</p></div>')
                                        }

                                        ind++;
                                    }

                                    $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item notasfinal" style="background-color: greenyellow" ><p class=" nota' + data.notas[i]['tiempo'] + '" >'+data.notas[i]['alumnos'][k]['nota']+'</p></div>')



                                    if(data.notas[i]['alumnos'][k]['porcentaje'].length>0){
                                        $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item porcentajefinal"><p>'+data.notas[i]['alumnos'][k]['porcentaje'][0]+'%</p></div>')
                                        $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item conceptofinal"><p>'+data.notas[i]['alumnos'][k]['porcentaje'][1]+'</p></div>')

                                    }else{
                                        $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item porcentajefinal"><p>-</p></div>')
                                        $("div.selector[name='" + data.notas[i]['alumnos'][k]['idNotas'] + "']").append('<div class="grid-item conceptofinal"><p>-</p></div>')

                                    }



                                }


                                indg++;
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


                    }


                }

                //Recorremos el largo de alumnos


            });


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
                            var ids = $this.closest('div.selector').attr('name');
                            var lista =$(".notas[name=" + id+"]");


                            if(lista.length==3){
                                var valor_1=$(lista[0]).find('p').text();
                                var valor_2=$(lista[1]).find('p').text();
                                var valor_3=$(lista[2]).find('p').text();

                                if (valor_1.length == '1' && valor_1!=0) {
                                    valor_1 += '0';
                                }
                                if (valor_2.length == '1' && valor_2!=0) {
                                    valor_2 += '0';
                                }
                                if (valor_3.length == '1' && valor_3!=0) {
                                    valor_3 += '0';
                                }

                                var Datos = {
                                    "ids":ids,
                                    "id": id,
                                    "nota_1": valor_1,
                                    "nota_2":valor_2,
                                    "nota_3":valor_3,
                                }

                            }

                            $.ajax(
                                {
                                    global:false,
                                    cache: false,
                                    async: true,
                                    dataType: 'json',
                                    type: 'POST',
                                    contentType: 'application/x-www-form-urlencoded',
                                    url: 'guardaguianotalagpre/',
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

                                            actualizanota($this,data.p,data.pr,data.con);




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
            var tab = currCell.attr("tabindex");
            tab++;
            aux = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            if (aux.attr('class') != "not") {
                c = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            }
            e.stopImmediatePropagation();




        } else if (e.which == 37) {
            // Izq


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


        } else if (e.which == 38) {
            // Arriba
            e.preventDefault();
            var tab = currCell.attr("tabindex");
            tab = tab - 3;
            var busc = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");


            if (busc.length == 0) {
                $("#tblTabla").scrollLeft(0);
            }
            aux = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            if (aux.attr('class') != "not") {
                c = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            }
        } else if (e.which == 40) {
            // Abajo
            e.preventDefault();
            var tab = currCell.attr("tabindex");
            tab = tab + 3;
            var busc = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");


            if (busc.length == 0) {
                $("#tblTabla").scrollLeft(1500);
            }
            aux = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            if (aux.attr('class') != "not") {
                c = $(currCell).closest('.grid').find(".grid-item[tabindex='" + tab + "']");
            }
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


});
