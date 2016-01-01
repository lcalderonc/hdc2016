<script type="text/javascript">
    var coord_x, coord_y;
    var marker;
    $(document).ready(function() {
        CambioDireccion.CargarCambios(activarTabla);
        //modal
        $('#cambioDireccionModal').on('shown.bs.modal', function (e) {
            var x= coord_x;
            var y= coord_y;
            var fenway = {lat: y, lng: x};

            //mapa
           var myLatlng = new google.maps.LatLng(y,x);
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: myLatlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });
            //marcador
            var pos = new google.maps.LatLng(y, x);
            marker = new google.maps.Marker({
                draggable: true,
                position: pos,
                map: map,
            });
            //eventos
            google.maps.event.addListener(map, 'click', function(evento) {
                var latitud = evento.latLng.lat();
                var longitud = evento.latLng.lng();
                Psigeo.geoStreetView(latitud,longitud,'pano');
                SetXY(longitud, latitud);
                marker.setMap(null);

                var pos = new google.maps.LatLng(latitud, longitud);
                marker = new google.maps.Marker({
                    draggable: true,
                    position: pos,
                    map: map,
                });
                marker.setPosition(pos);

                google.maps.event.addListener(marker, 'click', function() {
                    var pos2 = marker.getPosition();
                    SetXY(pos2.lng(), pos2.lat());
                    Psigeo.geoStreetView(pos2.lat(),pos2.lng(),'pano');
                });
                google.maps.event.addListener(marker, 'dragend', function() {
                    var pos2 = marker.getPosition();
                    SetXY(pos2.lng(), pos2.lat());
                    Psigeo.geoStreetView(pos2.lat(),pos2.lng(),'pano');
                });
            });
            Psigeo.geoStreetView(y,x,'pano');

        });
        $('#cambioDireccionModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // captura al boton
            var titulo = button.data('titulo'); // extrae del atributo data-
            var id = button.data('id');
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this); //captura el modal
            modal.find('.modal-title').text(titulo+' Validar cambio de dirección');
            $('#form_cambiodir [data-toggle="tooltip"]').css("display","none");
            $("#form_cambiodir input[type='hidden']").remove();

            if(titulo=='Nuevo'){
                modal.find('.modal-footer .btn-primary').text('Guardar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
                $('#form_cambiodir #slct_estado').val(1);
                $('#form_cambiodir #txt_nombre').focus();
            }
            else{
                var codactu=CambiosObj[id].codactu;
                coord_x=CambiosObj[id].coord_x;
                coord_y=CambiosObj[id].coord_y;
                var estado=CambiosObj[id].estado;
                var fecha_registro=CambiosObj[id].fecha_registro;
                var gestion_id=CambiosObj[id].gestion_id;
                var nombreCliente=CambiosObj[id].nombre_cliente;
                var nombre_usuario=CambiosObj[id].nombre_usuario;
                var quiebre=CambiosObj[id].quiebre;
                var tipo_usuario=CambiosObj[id].tipo_usuario;
                var direccion=CambiosObj[id].direccion;
                var referencia=CambiosObj[id].referencia;
                var validacion=CambiosObj[id].validacion;
                var observacion=CambiosObj[id].observacion;

                modal.find('.modal-footer .btn-primary').text('Actualizar');
                modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
                $('#form_cambiodir #txt_nombrecliente').val( nombreCliente );
                $('#form_cambiodir #txt_direccion').val( direccion );
                $('#form_cambiodir #txt_referencia').val( referencia );
                $('#form_cambiodir #txt_latitud').val( coord_y );
                $('#form_cambiodir #txt_longitud').val( coord_x );
                $('#form_cambiodir #txt_observacion').val( observacion );
                $('#form_cambiodir #slct_validacion').val( CambiosObj[id].validacion );
                $("#form_cambiodir").append("<input type='hidden' value='"+CambiosObj[id].id+"' name='id'>");

            }
            $("#slct_validacion").on('change',function() {
                var val = $(this).val();
                if ( val == 2) {
                    $('#form_cambiodir #txt_observacion').css('display','block');
                } else {
                    $('#form_cambiodir #txt_observacion').css('display','none');
                }
            });
            $('#slct_validacion').trigger('change');
        });
        $('#cambioDireccionModal').on('hide.bs.modal', function (event) {
            $('#map').html('');
            $('#pano').html('');
            var modal = $(this); //captura el modal
            modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        });
    });
    HTMLCargarCambios = function(datos) {
        var html = "";
        $('#t_cambios_direcciones').dataTable().fnDestroy();
        $.each(datos, function(index, data) {
            estadohtml = '<span id="' + data.id + '" onClick="activar(' + data.id + ')" class="btn btn-danger">Inactivo</span>';
            if (data.estado == 1) {
                estadohtml = '<span id="' + data.id + '" onClick="desactivar(' + data.id + ')" class="btn btn-success">Activo</span>';
            }
            if (data.validacion==0) {
                validacion='Registrado';
            } else if (data.validacion==1) {
                validacion='Aprobado';

            } else if (data.validacion==2) {
                validacion='Observado';

            }
            html += "<tr>" +
                    "<td>" + data.codactu + "</td>" +
                    "<td>" + data.nombre_cliente + "</td>" +
                    "<td>" + data.nombre_usuario + "</td>" +
                    "<td>" + data.tipo_usuario + "</td>" +
                    "<td>" + validacion + "</td>" +
                    "<td>" + data.fecha_registro + "</td>" +
                    "<td>" + estadohtml + "</td>" +
                    '<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-target="#cambioDireccionModal" data-id="' + index + '" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a>';
            html += "</tr>";
        });
        $("#tb_cambios_direcciones").html(html);
    };
    activar=function(id){
        CambioDireccion.CambiarEstado(id,1);
    };
    desactivar=function(id){
        CambioDireccion.CambiarEstado(id,0);
    };
    activarTabla=function(){
        $("#t_cambios_direcciones").dataTable(); // inicializo el datatable
    };
    Editar=function(){
        var rst = Validar();
        if (rst==true) {
            CambioDireccion.AgregarEditar(1);
        } else {
            Psi.mensaje('danger', 'Ingrese observación', 6000);
        }
    };
    Agregar=function(){
        if (Validar) {
            CambioDireccion.AgregarEditar(0);
        } else {
            Psi.mensaje('danger', 'Ingrese observación', 6000);
        }
    };
    SetXY=function(coord_x, coord_y ){
        $('#txt_longitud').val(coord_x);
        $('#txt_latitud').val(coord_y);
    };
    Validar=function(){
        var direccion = $.trim($('#form_cambiodir #txt_observacion').val());
        var val = $("#slct_validacion").val();
        if (val==2) {
            if ( direccion !== '') {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    };
</script>
