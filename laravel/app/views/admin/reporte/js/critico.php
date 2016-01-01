<script type="text/javascript">
var fecha_ini, fecha_fin;
$(document).ready(function() {

    //$("#slct_reporte").change(ValidaTipo);
    $('#fecha').daterangepicker({
        format: 'YYYY-MM-DD'
    });

    //Mostrar 
    $("#generar_critico").click(function (){
        mostrarCritico();
    });

});
descargaAverias=function(){
    $("input[type='hidden']").remove();
    $("#form_critico").append("<input type='hidden' value='1' name='averia' id='averia'>");
    //$("#form_critico").append("<input type='hidden' value='' name='provision' id='provision'>");

    $("#form_critico").submit();
};
descargaProvision=function(){
    $("input[type='hidden']").remove();
    //$("#form_critico").append("<input type='hidden' value='' name='averia' id='averia'>");
    $("#form_critico").append("<input type='hidden' value='1' name='provision' id='provision'>");

    $("#form_critico").submit();
};
validaParametros=function(){

    var fecha = $("#fecha").val();
    var actividad = $("#actividad").val();

    if (actividad==='') {
        alert("Indique la actividad");
        return false;
    }

    fecha_ini = fecha.substring(0,10);
    fecha_fin = fecha.substring(13);
    
    if (fecha_ini==="" && fecha_fin===""){
        alert("Indique la Fecha Registro correctamente");
        return false;
    }
    
    return true;
};
mostrarCritico=function(){
    var envio=validaParametros();
    $("input[type='hidden']").remove();
    if( envio ){
        $("#form_critico").append("<input type='hidden' value='"+fecha_ini+"' name='fecha_ini' id='fecha_ini'>");
        $("#form_critico").append("<input type='hidden' value='"+fecha_fin+"' name='fecha_fin' id='fecha_fin'>");

        $("#form_critico").submit();
        
    }

};

</script>
