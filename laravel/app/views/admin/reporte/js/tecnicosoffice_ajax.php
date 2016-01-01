<script type="text/javascript">
var Tecnicos={
    ReporteOfficetrack:function(tipo_repo,tecnicos,fecha){
        $.ajax({
            url         : 'reporte/tecnicoofficetrack',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {'tipo_repo':tipo_repo,tecnicos:tecnicos,fecha:fecha},
            success : function(obj) {
                if(obj.rst==1){
                    //HTMLListarSlct(obj,accion);
                    var html=obj.datos;
                    //$('#t_reporte').dataTable().fnDestroy();
                    /*$.each(datos,function(index,data){

                    });*/

                    $("#t_reporte").html(html);
                    if (html==='') {
                        alert('No se encontraron registros para estas opciones');
                    } else 
                        activarTabla();
                }
            }
        });
    }
};
</script>