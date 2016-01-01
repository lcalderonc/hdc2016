<script type="text/javascript">
$(document).ready(function() {
    //Celulas.CargarCelulas(activarTabla);
    var ids = []; //para seleccionar un id
    var data = {usuario_id: 0};
    slctGlobal.listarSlct('empresa','slct_empresa','simple',ids,data,0,'#slct_celula,#slct_tecnico','E');
    slctGlobal.listarSlct('celula','slct_celula','simple',ids,0,1,'#slct_tecnico','C');
    slctGlobal.listarSlct('tecnico','slct_tecnico','multiple',ids,0,1);

    $('#slct_reporte').change(function() {
        filtrarFecha( $('#slct_reporte').val() );
    });

    $("#reiniciarFiltros").click(function() {
        $('#slct_empresa').multiselect('deselectAll', false);
        $('#slct_empresa').multiselect('refresh');
        $('#slct_celula').multiselect('deselectAll', false);
        $('#slct_celula').multiselect('refresh');
        $('#slct_tecnico').multiselect('deselectAll', false);
        $('#slct_tecnico').multiselect('refresh');
        $("#slct_reporte").val('');
        filtrarFecha(0);
        $('#t_reporte').dataTable().fnDestroy();
    });
    $("#mostrarAsistencia").click(function() {

        var tipo_repo = $('#slct_reporte').val();
        var tecnicos = $('#slct_tecnico').val();
        if (tecnicos) {
            var slct_tecnicos= $('#slct_tecnico').val().join();
            var fecha=$("#fecha").val();
            if ( fecha!=="")
                Tecnicos.ReporteOfficetrack(tipo_repo,slct_tecnicos,fecha);
            else
                alert("Seleccione Fecha");
        } else
            alert("Seleccione Tecnicos");
    });
    $("#ExportExcel").click(function(){
        var tipo_repo = $('#slct_reporte').val();
        var tecnicos = $('#slct_tecnico').val();
        if (tecnicos) {
            var slct_tecnicos= $('#slct_tecnico').val().join();
            var fecha=$("#fecha").val();
            if ( fecha!=="") {
                $("input[type='hidden']").remove();
                $("#form_reporte").append("<input type='hidden' value='"+tipo_repo+"' name='tipo_repo'>");
                $("#form_reporte").append("<input type='hidden' value='"+slct_tecnicos+"' name='tecnicos'>");
                $("#form_reporte").append("<input type='hidden' value='"+fecha+"' name='fecha'>");
                $("#form_reporte").append("<input type='hidden' value='1' name='excel'>");
                $("#form_reporte").submit();
            }
            else
                alert("Seleccione Fecha");
        } else
            alert("Seleccione Tecnicos");
    });

});

filtrarFecha=function(valor){
    if ( '1' ===valor) {//por dia
        $('#fecha').daterangepicker({
            format: 'YYYY-MM-DD',
            singleDatePicker: true
        });
        $('#fecha').val('');
        $('#fecha').attr('placeholder','AAAA-MM-DD');

    } else if ('2' ===valor) { //por rango de fechas
        $('#fecha').daterangepicker({
            format: 'YYYY-MM-DD',
            singleDatePicker: false
        });
        $('#fecha').val('');
        $('#fecha').attr('placeholder','AAAA-MM-DD - AAAA-MM-DD');
    } else{
        $('#fecha').val('');
        $('#fecha').attr('placeholder','');
    }
};
activarTabla=function(){
    $("#t_reporte").dataTable(); // inicializo el datatable
};
</script>