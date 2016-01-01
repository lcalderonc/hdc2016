<script type="text/javascript">
    var temporalBandeja = 0;

    var coord_x = null;
    var coord_y = null;
    var x_inicio = null;
    var y_inicio = null;

    $(document).ready(function() {
        $('#codactuModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget); // captura al boton
            var modal = $(this); //captura el modal

            variables = {buscar_codactu: button.data('codactu'),
                tipo: 'gd.averia',
                bandeja: '1'
            };
            Bandeja.CargarBandeja('M', HTMLCargarBandeja, variables);
        });

        $('#codactuModal').on('hide.bs.modal', function(event) {
            var modal = $(this); //captura el modal
            $("#codactu_modal_mapObjectMulti1").html("");
            $("#codactu_modal_mapObjectMulti2").html("");
            $("#codactu_modal_mapObjectMulti3").html("");
        });
        //Inicializar Google Street View
        $("#mapIniActu").click(function (){
            geoStreetView(coord_y, coord_x, 'codactu_modal_mapObjectMulti1');
        });

        $("#mapIniTec").click(function (){
            geoStreetView(y_inicio, x_inicio, 'codactu_modal_mapObjectMulti2');
        });
        //Inicializar Google Maps
        $("#mapActuTec").click(function (){
            initMaps(y_inicio,x_inicio,coord_y,coord_x,'codactu_modal_mapObjectMulti3');
        });

    });
    HTMLCargarBandeja = function(datos) {
        variables={ task_id:datos[0].id };
        Tarea.show(variables,"tecnicoprogramado_tareas");
        
        var html = "";
        var gestionado = "";
        var officetrackbotton = "";
        $('#t_codactu').dataTable().fnDestroy();

        $.each(datos, function(index, data) {
            gestionado = "";
            if (data.id === '') {
                temporalBandeja++;
                data.id = "T_" + temporalBandeja;
            }
            else {
                data.id = "ID_" + data.id;
                /*gestionado='<a class="btn btn-warning btn-sm" data-toggle="modal" data-target="#movimientoModal" data-codactu="'+data.codactu+'"><i class="fa fa-search-plus fa-lg"></i> </a>'+
                 '<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#observacionModal" data-codactu="'+data.codactu+'"><i class="fa fa-comments fa-lg"></i> </a>';*/
            }

            html += "<tr>" +
                    "<td>" + data.id + "</td>" +
                    "<td>" + data.codactu + "</td>" +
                    "<td>" + data.fecha_registro + "</td>" +
                    "<td>" + data.actividad + "</td>" +
                    "<td>" + data.quiebre + "</td>" +
                    "<td>" + data.empresa + "</td>" +
                    "<td>" + data.fh_agenda + "</td>" +
                    "<td>" + data.tecnico + "</td>" +
                    "<td>" + data.estado + "</td>";

            //Coordenadas
            coord_x = data.coord_x;
            coord_y = data.coord_y;
            x_inicio = data.x_inicio;
            y_inicio = data.y_inicio;

            officetrackColor = '';
            officetrackImage = '';
            officetrackbotton = '';
            if(data.transmision!=0){
                officetrackColor='btn-default';
                if(data.transmision!=1 && data.transmision.split("-").length>1 && data.transmision.split("-")[1].substr(0,6).toLowerCase()=='inicio' ){
                    officetrackColor='btn-success';
                }
                else if(data.transmision!=1 && data.transmision.split("-").length>1 && data.transmision.split("-")[1].substr(0,11).toLowerCase()=='supervision'){
                    officetrackColor='btn-warning';
                }
                else if(data.transmision!=1 && data.transmision.split("-").length>1 && data.transmision.split("-")[1].substr(0,6).toLowerCase()=='cierre' ){
                    officetrackColor='btn-primary';
                }

                if(officetrackColor!='btn-default'){
                    officetrackbotton='data-toggle="modal" data-target="#officetrackModal" data-id="'+data.id.split("_")[1]+'"';
                }
                officetrackImage=' &nbsp; <a class="btn '+officetrackColor+' btn-sm" '+officetrackbotton+'><i class="fa fa-mobile fa-3x"></i> </a>';
            }
            html +=
                    '<td><a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#bandejaModal" onClick="eventcodactumodal();" data-codactu="' + data.codactu + '"><i class="fa fa-desktop fa-lg"></i> </a>' +
                    officetrackImage +
                    '</td>';
            html += "</tr>";

        });
        $("#t_codactu>tbody").html(html);
        $("#t_codactu").dataTable();
        if (coord_y!=='' && coord_x!=='' && coord_y != null && coord_x!=null) {
            geoStreetView(coord_y, coord_x, 'codactu_modal_mapObjectMulti1');
            
        }
        if (y_inicio!=='' && x_inicio!=='' && y_inicio != null && x_inicio!=null) {
            geoStreetView(y_inicio, x_inicio, 'codactu_modal_mapObjectMulti2');
            
        }
        if (y_inicio!=='' && x_inicio!=='' && y_inicio != null && x_inicio!=null
        && coord_y!=='' && coord_x!=='' && coord_y != null && coord_x!=null) {
            initMaps(y_inicio,x_inicio,coord_y,coord_x,'codactu_modal_mapObjectMulti3');
        }
    };
</script>
