<script type="text/javascript">
var filtro_fecha, filtro_averia, fecha_ini, fecha_fin, file;
$(document).ready(function() {
    $('#fecha').daterangepicker({
        format: 'YYYY-MM-DD'
    });
    $("#generar_cruce").click(function (){
        mostrarCruce();
    });

});

validaParametros=function(){
    var fecha = $("#fecha").val();
    fecha_ini = fecha.substring(0,10);
    fecha_fin = fecha.substring(13);
    
    if (fecha_ini==="" && fecha_fin===""){
        alert("Indique la Fecha Registro correctamente");
        return false;
    }
    return true;
};
mostrarCruce=function(){
    var envio=validaParametros();
    
    if( envio ){
        $("#form_cruce").append("<input type='hidden' value='"+fecha_ini+"' name='fecha_ini' id='fecha_ini'>");
        $("#form_cruce").append("<input type='hidden' value='"+fecha_fin+"' name='fecha_fin' id='fecha_fin'>");
        $("#form_cruce").submit();
    }

};

</script>
