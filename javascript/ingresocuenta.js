$(function() {
    //si cambia el select de Establecimiento
    $('#establecimiento').live('change', function(e) {
        var ajax = $.ajax({
            type: "GET",
            url: "usuarios/cargarrol/id/" + $(this).val(),
            async: true,
            dataType: "json",
            beforeSend: function(x) {
                if (x && x.overrideMimeType) {
                    x.overrideMimeType("application/j-son;charset=UTF-8");
                }
            },
            success: function(data) {
                console.log(data);
                var options = '';
                for (var i = 0; i < data.length; i++) {
                    options += '<option value="' + data[i].id + '">' + data[i].nombreRol + '</option>';
                }
                $('#cargo').html(options);
            } //fin success
        }); //fin ajax get rol
        e.stopImmediatePropagation();
    });
});
