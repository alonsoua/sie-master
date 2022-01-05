//Taxonomias
   
     function marz(id){

       var idtax='';
     
       idtax=$("#taxmarzano").attr('name');
         
            // codigo crea y carga formulario de marzano
            var ajax3 = $.ajax({
             type: "GET",
             url: "gettaxonomia/id/" + 2,
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
          
            var opcionestax='';
            opcionestax='<option value="Null">Seleccione Opción</option>';
            
                for(var i=0; i< data.length;i++){
                    
                   
                    opcionestax+='<option value="'+data[i].id_categoria+'">'+data[i].nombre_categoria+'</option>'; 
                  
                }
                
               
                
                
                $("#taxmarzano"+id).html(opcionestax);
                var opcionestax2='<div id="divniveltax'+id+'"></div>';
                $("#tax"+id).append(opcionestax2);
                
                
             }
        });}
         $('.marzt').live('change',function(e){ 
             //alert($(this).val());
             idtax=$(this).attr('name');
            $("#divtaxn"+idtax).html('');
             var ajax4 = $.ajax({
             type: "GET",
             url: "gettaxonomianivelmarzano/id/" + $(this).val(),
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
            
            var opcionestax='<select class="marz" id="taxmarzanonivel">';
            opcionestax+='<option value="Null">Seleccione Opción</option>';
                for(var i=0; i< data.length;i++){
                   
                    opcionestax+='<option value="'+data[i].id_nivel_marzano+'">'+data[i].nombre_nivel+'</option>'; 
                  
                }
                opcionestax+='</select><div class="checktax" id="divtax'+idtax+'"></div>';
                
                console.log(data);
                $("#divnivel"+idtax).html(opcionestax);
                 //alert(idtax);
             }
             });
             
             $('.marzt').unbind('change');
             e.stopImmediatePropagation();

        });
        
        $('#taxmarzanonivel').live('change',function(e){ 
            $("#divtax"+idtax).html('');
            var id = $(this).parent("div").parent("td").attr("id");
            
            //alert(id);
           
             var ajax5 = $.ajax({
             type: "GET",
             url: "gettaxonomiaverbomarzano/id/" + $(this).val(),
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
                 // [Object object]
            //$("#muestraform").html(data);
             console.log(data);
            var opcionestax='<ol>';
          
                for(var i=0; i< data.length;i++){
                   
                    opcionestax+='<li style="list-style:none" class=checkmarz><input type="checkbox" value="'+data[i].id_verbo_marzano+'" id="inputtax'+data[i].id_verbo_marzano+'">'+data[i].nombre_verbo+'</li>'; 
                  
                }
                opcionestax+='</ol>';
                
                
                //console.log(opcionestax);
                $("#divtax"+id).html(opcionestax);
                 //alert(idtax);
                 idtax='';
             }
             });
             e.stopImmediatePropagation();

        });
        
        
    
    /***************************** Codigo crea y carga formulario de bloom***********/    
        
     function bloom(id){ 
         
         var idtax='';
        idtax=$("#taxbloom").attr('name');
            var ajax6 = $.ajax({
             type: "GET",
             url: "gettaxonomiabloom/id/" + 1,
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
                 // [Object object]
            //$("#muestraform").html(data);
             console.log(data);
            var opcionestax='';
            opcionestax='<option value="Null">Seleccione Opción</option>';
            
            
                for(var i=0; i< data.length;i++){
                   
                    opcionestax+='<option value="'+data[i].id_nivel_bloom+'">'+data[i].nombre_nivel_bloom+'</option>'; 
                  
                }
                
                
                
                
                $("#taxbloom"+id).html(opcionestax);
                 var opcionestax2='<div class="checktax"  id="divnivelbtax'+id+'"></div>';
                $("#tax"+id).append(opcionestax2);
                 //alert(idtax);
             }
        });
         $('.bloom').live('change',function(e){ 
             //alert($(this).val());
             idtax=$(this).attr('name');
             var ajax7 = $.ajax({
             type: "GET",
             url: "getverbosbloom/id/" + $(this).val(),
             async: true,
             dataType: "json",
             beforeSend: function(x) {
                if(x && x.overrideMimeType) {
                   x.overrideMimeType("application/j-son;charset=UTF-8");
                }
             },
             success: function(data){
                 // [Object object]
            //$("#muestraform").html(data);
             console.log(data);
            var opcionestax='<ol>';
            
                for(var i=0; i< data.length;i++){
                   
                   // opcionestax+='<option value="'+data[i].id_verbo_bloom+'">'+data[i].nombre_verbo+'</option>'; 
                  opcionestax+='<li style="list-style:none" class=checkmarz><input type="checkbox" value="'+data[i].id_verbo_bloom+'" id="inputtax'+data[i].id_verbo_bloom+'">'+data[i].nombre_verbo+'</li>'; 
                }
                opcionestax+='</ol>';
                
                //console.log(opcionestax);
                $("#divnivelb"+idtax).html(opcionestax);
                 //alert(idtax);
                 idtax='';
             }
             });
            e.stopImmediatePropagation();

        });
        
        
     }  
     
     
     /*****************************************************************************/
    
    
 
