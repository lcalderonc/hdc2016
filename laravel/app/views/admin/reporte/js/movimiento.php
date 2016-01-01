<script type="text/javascript">
var filtro_fecha, filtro_averia, fecha_ini, fecha_fin, file;
$(document).ready(function() {

    $("#slct_reporte").change(ValidaTipo);
    $('#fecha').daterangepicker({
        format: 'YYYY-MM-DD'
    });
    slctGlobalHtml('slct_reporte,#slct_detalle_observacion','simple');
    //Mostrar 
    $("#generar_movimientos").click(function (){
        mostrarMovimiento();
    });

    $("#file_averia").fileinput({
        maxFileSize: 100,
        showUpload: false,
        showCaption: false,
        allowedFileExtensions: ["txt"],
        previewClass: "bg-warning",
        //uploadAsync: true, //ajax
        //uploadUrl: "/reporte/movimiento"
        //showPreview: false,
    });

    //iCheck-helper

    $('.iCheck-helper').click(function(){
        selectFiltro();
    });
    $("#lbl_fecha").click(function(){
        selectFiltro();

    });
    $("#lbl_averia").click(function(){
        selectFiltro();

    });
    selectFiltro();
});
selectFiltro=function(){

    if ($("#lbl_fecha div").attr("aria-checked")==='true') {
        $('#fecha').removeAttr('disabled');
        $('#slct_reporte').multiselect('enable');
        filtro_fecha=1;
    } else {
        $('#fecha').attr('disabled', 'disabled');
        $('#slct_reporte').multiselect('disable');
        filtro_fecha=0;
    }
    if ($("#lbl_averia div").attr("aria-checked")==='true') {
        $('#file_averia').removeAttr('disabled');
        filtro_averia=1;
    } else {
        $('#file_averia').attr('disabled', 'true');
        filtro_averia=0;
    }
};
ValidaTipo=function(){
    $("#txt_buscar").val("");
    $("#txt_buscar").focus();
};
validaParametros=function(){

    if (filtro_fecha===1) {
        var fecha = $("#fecha").val();
        var tipo = $("#slct_reporte").val();
        fecha_ini = fecha.substring(0,10);
        fecha_fin = fecha.substring(13);
        
        if (tipo==='') {
            alert("Indique el tipo de reporte");
            return false;
        }
        if (fecha_ini==="" && fecha_fin===""){
            alert("Indique la Fecha Registro correctamente");
            return false;
        }
    }
    if (filtro_averia===1) {
        
        file=$("#file_averia").val();
        if (file==='') {
            alert("Busque y Seleccione un archivo");
            return false;
        }
    }
    if (filtro_fecha===0 && filtro_averia===0) {
        alert("Seleccione Filtro");
        return false;
    }
    
    return true;
};
mostrarMovimiento=function(){
    var envio=validaParametros();
    
    if( envio ){
        if (filtro_fecha===1) {
            $("#form_movimiento").append("<input type='hidden' value='"+fecha_ini+"' name='fecha_ini' id='fecha_ini'>");
            $("#form_movimiento").append("<input type='hidden' value='"+fecha_fin+"' name='fecha_fin' id='fecha_fin'>");
        }
        
        $("#form_movimiento").submit();
        
    }

};

</script>
