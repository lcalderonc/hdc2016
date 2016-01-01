<script type="text/javascript">

    $(document).ready(function() {
        $('#fecha_agenda').daterangepicker(
                {
                    format: 'YYYY-MM-DD'
                });

        $("#mostrar").click(mostrarReporte);
        $("#t_reporte").dataTable();
    });

    mostrarReporte = function() {

        if ($.trim($("#fecha_agenda").val()) === '') {
            alert('Seleccione Rango de Fecha agenda');
        }
        else{
            Reporte.AgendamiendoMesCA(AgendamiendoMesCAHTML);
        }
    };

    AgendamiendoMesCAHTML = function(datos, cabecera) {
        $('#t_reporte').dataTable().fnDestroy();

        var htmlcab = "<tr><th>Cod Actu</th><th>Fecha Registro</th><th>Fecha Psi</th><th>Total Gestión</th>";

        for (var i = 0; i < cabecera.length; i++) {
            htmlcab += "<th>" + cabecera[i] + "</th>";
        }
        htmlcab += "<th>TOTAL</th>";

        htmlcab += "</tr>";

        var html = "";
        var html2="";

        $("#t_reporte thead,#t_reporte tfoot").html(htmlcab);

        var contadordet = 0;
        var detalle = "";
        var iconx = 0;
        var detalle2="";

        $.each(datos, function(index, data) {
            contadordet++;
            html += "<tr>";

            html += "<td>" + data.codactu + "</td>";
            html += "<td>" + data.fecha_registro + "</td>";
            html += "<td>" + data.fecha_psi + "</td>";
            html += "<td>" + data.total + "</td>";

            for (var i = 1; i <= cabecera.length; i++) {
                    if (data["f" + i] == null || data["f" + i] === '' || data["f" + i] == 0) {
                        data["f" + i] = "<font color='#1BE137'>" + 0 + "</font></b>";
                    }
                    else if (data["f" + i] * 1 <= 3) {
                        data["f" + i] = "<b>" + data["f" + i] + "</b>";
                    }
                    else {
                        data["f" + i] = "<b><font color='#E9102F'>" + data["f" + i] + "</font></b>";
                    }
            html += "<td>" + data['f' + i] + "</td>";
            }

            if ( data.total == null || data.total === '' ) {
                data.total = 0;
            }
            //codactuModal
            html += "<td>" + data.total_programados + "</td>";
            html += "</tr>";

        });

        $("#t_reporte tbody").html(html);
        $("#t_reporte").dataTable();
    };
</script>
