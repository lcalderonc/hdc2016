<script type="text/javascript">
    //js
var eventos_id, eventosObj;
var Eventos={
    AgregarEditarEventos:function(AE){
        var datos=$("#form_eventos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="eventos/crear";
        if(AE==1){
            accion="eventos/editar";
        }

        $.ajax({
            url         : accion,
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
                    $('#t_eventos').dataTable().fnDestroy();

                    Eventos.CargarEventos();
                    $('#eventosModal .modal-footer [data-dismiss="modal"]').click();
                    Psi.mensaje('success', obj.msj, 6000);
                }
                else{ 
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
            }
        });
    },
    CargarEventos:function(){
        $.ajax({
            url         : 'eventos/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                var html="";
                var estadohtml="";
                if(obj.rst==1){
                    HTMLCargarEventos(obj.datos);
                    eventosObj=obj.datos;
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
        
    CambiarEstadoEvento:function(id,tipo,AD){
        
        $("#form_eventos").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_eventos").append("<input type='hidden' value='"+AD+"' name='estado'>");
        $("#form_eventos").append("<input type='hidden' value='"+tipo+"' name='tipotabla'>");
        var datos=$("#form_eventos").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'eventos/cambiarestado',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
//                //var html="";
//                //var estadohtml="";
//                if(obj.rst==1){
                    Eventos.CargarEventos();
//                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    }
};
</script>