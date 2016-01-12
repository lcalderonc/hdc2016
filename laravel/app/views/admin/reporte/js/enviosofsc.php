<script type="text/javascript">
$(document).ready(function() {
     
    $('#fecha_recepcion_ini').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: true
    });
    $('#fecha_recepcion_fin').daterangepicker({
        format: 'YYYY-MM-DD',
        singleDatePicker: true
    });

     $('#t_enviosofs').DataTable( {
        "columnDefs": [
            {
                "targets": [ 5 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 6 ],
                "visible": false
            } 
        ]
    } );

    $("#mostrar").click(validarInicio);

    $('#t_enviosofs tbody ').on('click', 'button', function (event) {
   //alert("str");
       var table = $('#t_enviosofs').DataTable();

        var row = $(this).closest("tr").get(0);
        var aData = table.row(row).data();


         $('#ItemPopup').modal('show');
         $("#resultadoOfsc").html(
                            '<strong>Enviado:</strong><br/>' + aData[5] + '<br/><br/>' +
                            '<strong>Respuesta:</strong><br/>' + aData[6]  
                        );

        event.stopImmediatePropagation();  //prvents the other on click from firing that fires up the inline editor
    });  
});

$.extend( true, $.fn.dataTable.defaults, {
    "language": {
        "lengthMenu": "Mostrar _MENU_ Registros por página",
        "zeroRecords": "Registros No encontrados",
        "info": "Mostrando página _PAGE_ de _PAGES_",
        "infoEmpty": "No hay Registros disponibles",
        "search":         "Buscar:",
        "infoFiltered": "(Filtrado desde _MAX_ total de registros)",
        "paginate": {
        "first":      "Primero",
        "last":       "Ultimo",
        "next":       "Siguiente",
        "previous":   "Anterior"
    },
    }
    
} );

HTMLCargarEnviosOfsc=function(datos){
    var html="";
    $('#t_enviosofs').dataTable().fnDestroy();

    $.each(datos,function(index,data){
   
        if(data.enviado.length > 50) enviadoFinal = data.enviado.substring(0, 50) + '...'; else enviadoFinal = data.enviado;
        if(data.respuesta.length > 50) respuestaFinal = data.respuesta.substring(0, 50) + '...'; else respuestaFinal = data.respuesta;
 
 var valor = data.respuesta;

         html+="<tr>"+
            "<td>"+data.accion+"</td>"+
            "<td>"+data.created_at+"</td>"+
            "<td>"+data.usuario+"</td>"+
            "<td>"+enviadoFinal+"</td>"+              
            "<td>"+respuestaFinal+"</td>"+              
            "<td>"+data.enviado+"</td>"+  
            "<td>"+data.respuesta+"</td>"+           
         

            "<td><button type='button' class='btn btn-default btn-xs'><span class='glyphicon glyphicon-search'></span></button></td>"; 
        html+="</tr>";
    });

    $("#tb_enviosofs").html(html);
  
   

    $('#t_enviosofs').DataTable( {
        "columnDefs": [
            {
                "targets": [ 5 ],
                "visible": false,
                "searchable": false
            },
            {
                "targets": [ 6 ],
                "visible": false
            } 
        ]
    } );


};

validarInicio = function () {
    if ($.trim($("#fecha_recepcion_ini").val()) === '' || $.trim($("#fecha_recepcion_fin").val()) === '') {
        alert('Seleccione la Fecha');
        
        if($.trim($("#fecha_recepcion_ini").val()) == '' && $.trim($("#fecha_recepcion_fin").val()) == '') $("#fecha_recepcion_ini").focus();
        if($.trim($("#fecha_recepcion_ini").val()) == '' && $.trim($("#fecha_recepcion_fin").val()) != '') $("#fecha_recepcion_ini").focus();
        if($.trim($("#fecha_recepcion_ini").val()) != '' && $.trim($("#fecha_recepcion_fin").val()) == '') $("#fecha_recepcion_fin").focus();

    } else {
        EnviosOfsc.CargarEnviosOfsc();            
    }
};
</script>