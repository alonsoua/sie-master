/*Funcion para agregar una fila*/
function agregarfila(id){
	if(id =="tablaarticulos"){
var tbody = document.getElementById
(id).getElementsByTagName("TBODY")[0];
var row = document.createElement("TR")
var td1 = document.createElement("TD")
td1.appendChild(document.createTextNode("columna 1"))
var td2 = document.createElement("TD")
td2.appendChild (document.createTextNode("columna 2"))
var td3 = document.createElement("TD")
td3.appendChild(document.createTextNode("columna 3"))
var td4 = document.createElement("TD")
td4.appendChild(document.createTextNode("columna 4"))
var td5 = document.createElement("TD")
td5.appendChild(document.createTextNode("columna 5"))
var td6= document.createElement("TD")
td6.appendChild(document.createTextNode("columna 6"))
row.appendChild(td1).innerHTML="<input type=text disabled=disabled>";
row.appendChild(td2).innerHTML="<input type=text>";
row.appendChild(td3).innerHTML="<input type=text>";
row.appendChild(td4).innerHTML="<input type=text>";
row.appendChild(td5).innerHTML="<input type=text disabled=disabled>";
row.appendChild(td6).innerHTML="<input type=button value=x fila onclick=borrarfila(this) />";
tbody.appendChild(row);
}
else{
	var tbody = document.getElementById
	(id).getElementsByTagName("TBODY")[0];
var row = document.createElement("TR")
var td1 = document.createElement("TD")
td1.appendChild(document.createTextNode("columna 1"))
var td2 = document.createElement("TD")
td2.appendChild (document.createTextNode("columna 2"))
var td3 = document.createElement("TD")
td3.appendChild(document.createTextNode("columna 3"))
var td4 = document.createElement("TD")
td4.appendChild(document.createTextNode("columna 4"))
var td5 = document.createElement("TD")
td5.appendChild(document.createTextNode("columna 5"))
var td6 = document.createElement("TD")
td6.appendChild(document.createTextNode("columna 6"))
var td7 = document.createElement("TD")
td7.appendChild(document.createTextNode("columna 7"))
var td8 = document.createElement("TD")
td8.appendChild(document.createTextNode("columna 8"))
var td9 = document.createElement("TD")
td9.appendChild(document.createTextNode("columna 9"))
var td10 = document.createElement("TD")
td10.appendChild(document.createTextNode("columna 10"))
var td11 = document.createElement("TD")
td11.appendChild(document.createTextNode("columna 11"))
row.appendChild(td1).innerHTML="<input type=text class=col_8>";
row.appendChild(td2).innerHTML="<input type=text class=col_12>";
row.appendChild(td3).innerHTML="<input type=text class=col_12>";
row.appendChild(td4).innerHTML="<input type=text class=col_11>";
row.appendChild(td5).innerHTML="<input type=text class=col_11>";
row.appendChild(td6).innerHTML="<input type=text class=col_10>";
row.appendChild(td7).innerHTML="<input type=text class=col_10>";
row.appendChild(td8).innerHTML="<input type=text class=col_12>";
row.appendChild(td9).innerHTML="<input type=text disabled=disabled class=col_12>";
row.appendChild(td10).innerHTML="<select id=select1><option value=1>1</option><option value=2>2</option><option value=3>3</option></select>";
row.appendChild(td11).innerHTML="<input type=button value=x fila onclick=borrarfila(this) />";
tbody.appendChild(row);
	
	
	}
}

/*Funcion que elimina la fila segun el td donde se ubique*/
function borrarfila(t)
    {
        var td = t.parentNode;
        var tr = td.parentNode;
        var table = tr.parentNode;
        table.removeChild(tr);
    }
/*Funcion que agrega un objeto de tipo documento y su boton eliminar*/	
	function agregaobjeto(id){
		var tbody = document.getElementById
		(id).getElementsByTagName("TBODY")[0];
		var row = document.createElement("TR")
		var td1 = document.createElement("TD")
		td1.appendChild(document.createTextNode("columna 1"))
		var td2 = document.createElement("TD")
		td2.appendChild (document.createTextNode("columna 2"))
		row.appendChild(td1).innerHTML="<input type=file>";
		row.appendChild(td2).innerHTML="<input type=button value=x fila onclick=borrarfila(this) />";
		tbody.appendChild(row);
		
		}