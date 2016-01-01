<script type="text/javascript">
$(document).ready(function() {
    $( "#form_motivos" ).submit(function( event ) {
        event.preventDefault();
    });
    Motivos.CargarMotivos(activarTabla);

    $('#myTab a').click(function (e) {
          e.preventDefault();
          $(this).tab('show');
          //cuadno hace click en lista actualizar los datos , si hace click en tab TABLA reiniar filtros
    });

    $('#motivoModal').on('show.bs.modal', function (event) {
      var button = $(event.relatedTarget); // captura al boton
      var titulo = button.data('titulo'); // extrae del atributo data-
      var motivo_id = button.data('id'); //extrae el id del atributo data
      // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
      // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
      var modal = $(this); //captura el modal
      modal.find('.modal-title').text(titulo+' Motivo');
      $('#form_motivos [data-toggle="tooltip"]').css("display","none");
      $("#form_motivos input[type='hidden']").remove();

        if(titulo=='Nuevo') {
            modal.find('.modal-footer .btn-primary').text('Guardar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Agregar();');
            $('#form_motivos #slct_estado').val(1); 
            $('#form_motivos #txt_nombre').focus();
            $('#form_motivos #slct_estado').show();
            $('#form_motivos .n_estado').remove();
        }
        else {
            modal.find('.modal-footer .btn-primary').text('Actualizar');
            modal.find('.modal-footer .btn-primary').attr('onClick','Editar();');
            $('#form_motivos #txt_nombre').val( cuposObj[motivo_id].nombre );
            //PRIVILEGIO DESACTIVAR EN LA OPCION DE EDITAR
            if(eliminarG == 0) {
                $('#form_motivos .n_estado').remove();
                $("#form_motivos").append('<div class="n_estado"></div>');
                $('#form_motivos #slct_estado').hide();
                $('#form_motivos .n_estado').show();
                var est = cuposObj[motivo_id].estado;
                if(est == 1) est ='Activo';
                else est = 'Inactivo';
                $("#form_motivos .n_estado").text( est );
            }
            $('#form_motivos #slct_estado').val( cuposObj[motivo_id].estado );
            
            //$('#form_motivos #txt_nombre').val( $('#t_motivos #nombre_'+button.data('id') ).text() );
            //$('#form_motivos #slct_estado').val( $('#t_motivos #estado_'+button.data('id') ).attr("data-estado") );
            $("#form_motivos").append("<input type='hidden' value='"+cuposObj[motivo_id].id+"' name='id'>");
        }

    });

    $('#motivoModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal
      modal.find('.modal-body input').val(''); // busca un input para copiarle texto
    });
});

//desactiva la tecla Enter en los formularios
disableKeyPress=function(e){
         var key;      
         if(window.event)
              key = window.event.keyCode; //IE
         else
              key = e.which; //firefox      
         return (key != 13);
};


activar=function(id){
    Motivos.CambiarEstadoMotivos(id,1);
};

desactivar=function(id){
    Motivos.CambiarEstadoMotivos(id,0);
};

activarTabla=function(){
    var html="", estadohtml="";
    //PRIVILEGIO AGREGAR
    if(agregarG == 0) { 
        $('.nuevo').remove();  
    }  
    $.each(cuposObj,function(index,data){
        estadohtml='<span id="'+data.id+'" onClick="activar('+data.id+')" class="btn btn-danger btn-xs">Inactivo</span>';
        if(data.estado==1){
            estadohtml='<span id="'+data.id+'" onClick="desactivar('+data.id+')" class="btn btn-success btn-xs">Activo</span>';
        }
        //PRIVILEGIO DESACTIVAR
        if(eliminarG == 0) {
            estadohtml='<span class="">Inactivo</span>';
            if(data.estado==1){
                estadohtml='<span class="">Activo</span>';
            }
        }    
        html+="<tr>"+
            "<td>"+data.nombre+"</td>"+
            "<td>"+estadohtml+"</td>";
         //PRIVILEGIO EDITAR
        if(editarG == 1) { 
            html+='<td><a class="btn btn-primary btn-xs" data-toggle="modal" data-target="#motivoModal" data-id="'+index+'" data-titulo="Editar"><i class="fa fa-edit fa-xs"></i> </a></td>';
        } else {
            html+='<td class="editarG"></td>';
        } 
        html+="</tr>";
    });
    $("#tb_motivos").html(html);
    if(editarG == 0) $('.editarG').hide(); 
    $("#t_motivos").dataTable({
         "language": {
            "decimal":        "",
            "emptyTable":     "No data available in table",
            "info":           "Mostrando _START_ de _END_ de _TOTAL_ entradas",
            "infoEmpty":      "Mostrando 0 de 0 de 0 entradas",
            "infoFiltered":   "(filtrado de _MAX_ entradas totales)",
            "infoPostFix":    "",
            "thousands":      ",",
            "lengthMenu":     "Mostrar _MENU_ entradas",
            "loadingRecords": "Cargando...",
            "processing":     "Processing...",
            "search":         "Buscar:",
            "zeroRecords":    "No matching records found",
            "paginate": {
                "first":      "Primero",
                "last":       "Último",
                "next":       "Siguiente",
                "previous":   "Anterior"
            },
            "aria": {
                "sortAscending":  ": activate to sort column ascending",
                "sortDescending": ": activate to sort column descending"
            }
        }
    });
};

Editar=function(){
    if(validaMotivos()){
        Motivos.AgregarEditarMotivos(1);
    }
};

Agregar=function(){
    if(validaMotivos()){
        Motivos.AgregarEditarMotivos(0);
    }
};

validaMotivos=function(){
    $('#form_motivos [data-toggle="tooltip"]').css("display","none");
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
</script>