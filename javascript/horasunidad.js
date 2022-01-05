$(document).ready(function(){
        var horasu='60';
        //Validamos que los input solo acepten valores numericos
        
        $(".txt").keydown(function(event) {
    if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
        (event.keyCode == 65 && event.ctrlKey === true) || (event.keyCode >= 35 && event.keyCode <= 39) || (event.keyCode == 110 || event.keyCode == 190) ) {
            return;
        }
        else {
        
            if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105) ) {
                event.preventDefault();
            
            
        }}
    });

 
        //recorremos los input con clase txt y se envian a la funcion calcula
        //handler to trigger sum event
        $(".txt").each(function() {
 
            $(this).keyup(function(){
                calcula(horasu);
            });
        });
 
    });
 
    function calcula(max) {
 
        var sum = 0;
        //Recorremos los txt
        $(".txt").each(function() {
 
            //agrega solo valores numericos
            if(!isNaN(this.value) && this.value.length!=0) {
                sum += parseFloat(this.value);
            }
            
           
 
        });
      
        $("#sumu").html(max);
         if(sum>=max){
             $("#sum").html(max);
             $(".txt").each(function() {
                 //agrega solo valores numericos
            if(this.value==0) {
                
                
                $(this).keydown(function(event) {
                    if (event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 ||
        (event.keyCode == 65 && event.ctrlKey === true) || (event.keyCode >= 35 && event.keyCode <= 39) || (event.keyCode == 110 || event.keyCode == 190) ) {
            return;
           
                    }
                }); 
                 $(this).attr('disabled','disabled')
            }
             });
                
    
    }else{
    $('.txt').removeAttr('disabled');  
        //.toFixed() metodo que retorna los decimales en este caso sin decimal
        $("#sum").html(sum.toFixed(0));
    }  
        
       
    }
    
     function noSunday(date){
     var day = date.getDay();
     return [(day > 0), ''];
  }
      
      
  
            $.datepicker.setDefaults($.datepicker.regional["es"]);
		$('input').filter('.datepicker').datepicker({ 
		 //autoSize: true,
            showOn: "button",
            //showWeek:true,
			buttonImage: "/Planificaciones/calendario/css/images/calendar.gif",
			buttonImageOnly: true,
            beforeShowDay: noSunday,
               
     

            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sabado'],
            dayNamesMin: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
           weekHeader: 'Sm',
            firstDay: 1,
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
            dateFormat: 'dd-mm-yy',
            changeMonth: true,
            changeYear: true,
            yearRange: "-70:+1",
            minDate: new Date(13,2,01),
            maxDate: new Date(13,6,29),
                        /*onSelect: function(selected) {
                         $("#fechaperiodo2").datepicker("option","minDate", selected)
                        }*/
			
            });
		

	