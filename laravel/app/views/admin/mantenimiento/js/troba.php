<script type="text/javascript">
$(document).ready(function() {
    Trobas.CargarTrobas(activarTabla);
    Trobas.cargarZonal('#slct_zonal','nuevo',null);
    $( "#slct_zonal" ).change(function() {
        var zonal = $( "#slct_zonal" ).val();
        Trobas.cargarNodo("#slct_nodo",'nuevo',null, zonal);
        zonal = (zonal=='0')? '':zonal;
        $('#t_trobas').DataTable().column( 0 ).search(
            zonal,
            true,
            true
        ).draw();

    });
    $( "#slct_nodo" ).change(function() {
        var nodo = $( "#slct_nodo" ).val();
        Trobas.cargarTroba("#slct_troba",'nuevo',null, nodo);
        nodo = (nodo=='0')? '':nodo;
        $('#t_trobas').DataTable().column( 1 ).search(
            nodo,
            true,
            true
        ).draw();
    
    });
    $( "#slct_troba" ).change(function() {
        var troba = $( "#slct_troba" ).val();
        troba = (troba=='0')? '':troba;
        $('#t_trobas').DataTable().column( 2 ).search(
            troba,
            true,
            true
        ).draw();
    
    });
    $('#trobaModal').on('show.bs.modal', function (event) {
        $('#txt_fecha_inicio').daterangepicker({
            format: 'YYYY-MM-DD',
            singleDatePicker: true
        });
        $('#txt_fecha_fin').daterangepicker({
            format: 'YYYY-MM-DD',
            singleDatePicker: true
        });
        /*$('#txt_fecha_planificacion').daterangepicker({
            format: 'YYYY-MM-DD',
            singleDatePicker: true
        });*/
        var button = $(event.relatedTarget); // captura al boton
        var titulo = button.data('titulo'); // extrae del atributo data-
        var troba_id = button.data('id'); //extrae el id del atributo data
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this); //captura el modal
        modal.find('.modal-title').text(titulo+' Troba');
        $('#form_trobas [data-toggle="tooltip"]').css("display","none");
//        $("#form_trobas input[type='hidden']").remove();
        
        if(titulo=='Nuevo') {
            Trobas.ListarEmpresas('nuevo',null);

            Trobas.cargarZonal('#slct_zonal_id','nuevo',null);
            //Trobas.cargarNodo('nuevo',null);
            //Trobas.cargarTroba('nuevo',null);

            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_trobas #slct_est_seguim').val('A');
            $('#form_trobas #txt_nombre').focus();
            $('#form_trobas #slct_est_seguim').show();
            $('#form_trobas .n_estado').remove();
            $('#form_trobas #txt_token').val("<?php echo Session::get('s_token');?>");
        }
        else {
            var contrata = TrobaObj[troba_id].empresa_id;
            var contrata_zona = TrobaObj[troba_id].contrata_zona_id;
            Trobas.ListarEmpresas('editar',contrata,contrata_zona);

            Trobas.cargarZonal('#slct_zonal_id','editar',TrobaObj[troba_id].zonal);
            Trobas.cargarNodo('#slct_nodo_id','editar',TrobaObj[troba_id].nodo);
            Trobas.cargarTroba('#slct_troba_id','editar',TrobaObj[troba_id].troba);

            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_trobas #txt_zonal').val( TrobaObj[troba_id].zonal );
            $('#form_trobas #txt_nodo').val( TrobaObj[troba_id].nodo );
            $('#form_trobas #txt_troba').val( TrobaObj[troba_id].troba );
            $('#form_trobas #txt_can_clientes').val( TrobaObj[troba_id].can_clientes );
            $('#form_trobas #txt_fecha_inicio').val( TrobaObj[troba_id].fecha_inicio );
            $('#form_trobas #txt_fecha_fin').val( TrobaObj[troba_id].fecha_fin );
            //$('#form_trobas #txt_fecha_planificacion').val( TrobaObj[troba_id].fecha_planificacion );
            $('#form_trobas #txt_digitalizacion').val( TrobaObj[troba_id].digitalizacion );
            $('#form_trobas #txt_token').val("<?php echo Session::get('s_token');?>");
            //$('#form_trobas #txt_est_seguim').val( TrobaObj[troba_id].est_seguim );
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_trobas .n_estado').remove();
                $("#form_trobas label:contains('Estado:')").after('<div class="n_estado"></div>');
                $('#form_trobas #slct_est_seguim').hide();
                $('#form_trobas .n_estado').show();
                var est = TrobaObj[troba_id].est_seguim;
                if(est == 'A') est ='Activo';
                else est = 'Inactivo';
                $("#form_trobas .n_estado").text( est );
            }
            $('#form_trobas #slct_est_seguim').val( TrobaObj[troba_id].est_seguim );
            //$('#form_trobas #txt_empresa_id').val( TrobaObj[troba_id].empresa_id );
            $('#form_trobas #txt_obs').val( TrobaObj[troba_id].obs );
            $("#form_trobas").append("<input type='hidden' value='"+TrobaObj[troba_id].id+"' name='id'>");
        }
        $( "#slct_zonal_id" ).change(function() {
            var zonal_id = $( "#slct_zonal_id" ).val();
            Trobas.cargarNodo("#slct_nodo_id",'nuevo',null, zonal_id);
        });
        $( "#slct_nodo_id" ).change(function() {
            var nodo_id = $( "#slct_nodo_id" ).val();
            Trobas.cargarTroba("#slct_troba_id",'nuevo',null, nodo_id);
        });
        $( "#slct_troba_id" ).change(function() {
        });

    });

    $('#trobaModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal
        modal.find('.modal-body input').val(''); // busca un input para copiarle texto
        $('#slct_zonal_id').html('');
        $('#slct_nodo_id').html('');
        $('#slct_troba_id').html('');
        Trobas.cargarZonal('#slct_zonal','nuevo',null);
        $('#slct_nodo').html('');
        $('#slct_troba').html('');

    });

});

activarTabla=function(){
    $("#t_trobas").dataTable(); // inicializo el datatable    
};

Editar=function(){
   // if(validaTrobas()){
        Trobas.AgregarEditarTroba(1);
    //}
};
activar=function(id){
    Trobas.CambiarEstadoTrobas(id,'A');
};
desactivar=function(id){
    Trobas.CambiarEstadoTrobas(id,'I');
};
Agregar=function(){
    //if(validaTrobas()){
        Trobas.AgregarEditarTroba(0);
   // }
};
filtrarTabla=function(){

};
validaTrobas=function(){
    $('#form_trobas [data-toggle="tooltip"]').css("display","none");
    var a=[];
    a[0]=valida("txt","nombre","");
    var rpta=true;

    for(i=0;i<a.length;i++){
        if(a[i]===false){
            rpta=false;
            break;
        }
    }
    return rpta;
};

valida=function(inicial,id,v_default){
    var texto="Seleccione";
    if(inicial=="txt"){
        texto="Ingrese";
    }

    if( $.trim($("#"+inicial+"_"+id).val())==v_default ){
        $('#error_'+id).attr('data-original-title',texto+' '+id);
        $('#error_'+id).css('display','');
        return false;
    }   
};
HTMLCargarTrobas=function(datos){
    var html="";
    $('#t_trobas').dataTable().fnDestroy();
    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('#nuevo').remove();  
    } 
    $.each(datos,function(index,data){
        estadohtml='<span id="'+data.id+'" onClick="activar('+data.id+')" class="btn btn-danger">Inactivo</span>';
        if(data.est_seguim=='A'){
            estadohtml='<span id="'+data.id+'" onClick="desactivar('+data.id+')" class="btn btn-success">Activo</span>';
        }
        //PRIVILEGIO DESACTIVAR
        if(eliminarG == 0) {
            estadohtml='<span class="">Inactivo</span>';
            if(data.est_seguim=='A'){
                estadohtml='<span class="">Activo</span>';
            }
        }    
        html+="<tr>"+
            "<td>"+data.zonal+"</td>"+
            "<td>"+data.nodo+"</td>"+
            "<td>"+data.troba+"</td>"+
            "<td>"+data.contrata+"</td>"+
            "<td>"+data.contrata_zona+"</td>"+
            //"<td>"+data.contrata_reparto+"</td>"+
            "<td>"+data.can_clientes+"</td>"+
            "<td>"+data.fecha_inicio+"</td>"+
            "<td>"+data.fecha_fin+"</td>"+
            //"<td>"+data.fecha_planificacion+"</td>"+
            "<td>"+data.digitalizacion+"</td>"+
            /*"<td>"+data.est_seguim+"</td>"+*/
            "<td>"+data.obs+"</td>"+
            "<td id='estado_"+index+"' data-estado='"+data.est_seguim+"'>"+estadohtml+"</td>";
             //PRIVILEGIO EDITAR
            if(editarG == 1) { 
                html+='<td><a class="btn btn-primary btn-sm" data-toggle="modal" data-backdrop="static" data-target="#trobaModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-lg"></i> </a></td>';
            } else {
                html+='<td class="editarG"></td>';
            }
            
        html+="</tr>";
    });
    $("#tb_trobas").html(html);
    if(editarG == 0) $('.editarG').hide();  
    activarTabla();
};
</script>