<script type="text/javascript">
/*variables globales*/
var empresa, celula, estados, agendamiento, recepcion_ini, recepcion_fin, objTecnicosOT;
var dataAjax ={};
dataAjax.officetrack = {};
dataAjax.pendientes = {};
var tablaOfficetrack = "#ListaTecnicosOfficetrack";
var tablaPendientes = "#ListaTecnicosPendientes";
var url = 'reporte/estadosot';
var RutaTecnico = 'http://psiweb.ddns.net:7020/publicmap/rutatecnico/';
var OrdenTecnico ='http://psiweb.ddns.net:7020/publicmap/ordentecnico/';
var EstadosOT={
    ObtenerTecnicosOT:function(){
        $.ajax({
            url         : url,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {accion:'tecnicosofficetrack'},
            success : function(obj) {
                if(obj.rst==1){
                    objTecnicosOT=obj.datos;
                }
            }
        });
    },
    ListarTecnicosOT:function(empresa,celula,fechaIni,fechaFin){
    //LISTADO DE TAREAS ENVIADAS A OFFICETRACK
            $.ajax({
                type    : "POST",
                url     : url,
                dataType : "json",
                data    : {
                    accion      :"tecnicosot",
                    empresaId   :empresa,
                    celulaId    :celula,
                    fechaIni    :fechaIni,
                    fechaFin    :fechaFin
                },
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function (obj) {
                    $(".overlay,.loading-img").remove();
                    if (obj.rst==1) {
                        dataAjax.officetrack = obj.datos;
                        mostrarTabla(obj.datos);
                    }

                },
                error: function(){
                    $(".overlay,.loading-img").remove();
                    $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                        '<i class="fa fa-ban"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente. Si el problema persiste favor de comunicarse a ubicame@puedesencontrar.com</b>'+
                                    '</div>');
                }
            });
    },
    Pendientes:function(empresa,celula,estados,fecha_agen,carnets){
    //LISTADO DE TAREAS PENDIENTES DEL TECNICO
        $.ajax({
            type: "POST",
            url: url,
            dataType : "json",
            async:false ,
            data: {
                accion:"pendientes",
                empresaId:empresa,
                celulaId:celula,
                estados:estados,
                fecha_agen:fecha_agen,
                carnets : carnets
            },
            beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
            success: function (obj) {
                $(".overlay,.loading-img").remove();
                if (obj.rst==1) {
                    dataAjax.pendientes = obj.datos;
                    mostrarTablaPendientes(obj.datos);
                }
            },
            error: function () {
                $(tablaPendientes + " .row-tec").remove();
                alert("No se encontraron datos a mostrar en Pendientes");

            }
        });

    }
};
</script>