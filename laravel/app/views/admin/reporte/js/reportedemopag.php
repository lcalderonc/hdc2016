<script type="text/javascript">

    $(document).ready(function() {
        $("#mostrarpag").click(mostrarReportePag);
        $("#t_reporte_pag").dataTable();
    });

    mostrarReportePag = function() {

        if ($.trim($("#fecha_agenda").val()) === '') {
            alert('Seleccione Rango de Fecha agenda');
        }
        else{
            Reporte.AgendamiendoMesCAPag();
        }

    };
</script>
