<script type="text/javascript">
var Reporte={
    AgendamiendoMesCA:function(evento){
        var datos=$("#form_reporte_demo").serialize().split("txt_").join("").split("slct_").join("").split("chk_").join("");
        $.ajax({
            url         : 'reporte/agendamientomesca',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    evento(obj.datos,obj.cabecera);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                        '<i class="fa fa-ban"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },
    AgendamiendoMesCAPag:function(){
        var columnDefs=[
                        {
                            "targets": 0,
                            "data": "codactu",
                            "name": "codactu",
                        },
                        {
                            "targets": 1,
                            "data": "fecha_registro",
                            "name": "fecha_registro",
                        },
                        {
                            "targets": 2,
                            "data": "fecha_psi",
                            "name": "fecha_psi"
                        },
                        {
                            "targets": 3,
                            "data": "total",
                            "name": "total"
                        },
                        ];

        var cabecera=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Setiembre','Octubre','Noviembre','Diciembre'];

        var fechas=$("#fecha_agenda").val().split(" - ");
        var targets=3;
        var nrocolumnas=0;

        $("#t_reporte_pag .eliminar").remove(); // Limpiando datos generados
        for(i=(fechas[0].split("-")[1]-1); i<fechas[1].split("-")[1]; i++){
            targets++;
            nrocolumnas++;
            columnDefs.push({
                                "targets": targets,
                                "data": "f"+nrocolumnas,
                                "name": "f"+nrocolumnas
                            });

        $("#t_reporte_pag>tfoot>tr,#t_reporte_pag>thead>tr").append('<th class="eliminar">'+cabecera[i]+'</th>');
        }
        targets++;
        columnDefs.push({
                            "targets": targets,
                            "data": "total_programados",
                            "name": "total_programados"
                        });
        
        $("#t_reporte_pag>tfoot>tr,#t_reporte_pag>thead>tr").append('<th class="eliminar">Programados Toales</th>');
        
        $('#t_reporte_pag').dataTable().fnDestroy();
        $('#t_reporte_pag')
            .on( 'page.dt',   function () { $("body").append('<div class="overlay"></div><div class="loading-img"></div>'); } )
            .on( 'search.dt', function () { $("body").append('<div class="overlay"></div><div class="loading-img"></div>'); } )
            .on( 'order.dt',  function () { $("body").append('<div class="overlay"></div><div class="loading-img"></div>'); } )
            .DataTable( {
                "processing": true,
                "serverSide": true,
                "stateSave": true,
                "stateLoadCallback": function (settings) {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                "stateSaveCallback": function (settings) { // Cuando finaliza el ajax
                    $(".overlay,.loading-img").remove();
                },
                "ajax": {
                        "url": "reporte/agendamientomescapag",
                        "type": "POST",
                        //"async": false,
                            "data": function(d){
                                //d.datos = datos;
                                datos=$("#form_reporte_demo").serialize().split("txt_").join("").split("slct_").join("").split("%5B%5D").join("[]").split("+").join(" ").split("%7C").join("|").split("&");
                                
                                for (var i = datos.length - 1; i >= 0; i--) {
                                    if( datos[i].split("[]").length>1 ){
                                        d[ datos[i].split("[]").join("["+contador+"]").split("=")[0] ] = datos[i].split("=")[1];
                                        contador++;
                                    }
                                    else{
                                        d[ datos[i].split("=")[0] ] = datos[i].split("=")[1];
                                    }
                                };
                            },
                        },
                columnDefs
            } );
    }
};
</script>
