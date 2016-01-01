<script type="text/javascript">
var quiebre_id, quiebreObj;
var Quiebres={
    AgregarEditarQuiebre:function(AE){
        var datos=$("#form_quiebres").serialize().split("txt_").join("").split("slct_").join("");
        var accion="quiebre/crear";
        if(AE==1){
            accion="quiebre/editar";
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
                    $('#t_quiebres').dataTable().fnDestroy();

                    Quiebres.CargarQuiebres();
                    $('#quiebreModal .modal-footer [data-dismiss="modal"]').click();
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
    CargarQuiebres:function(){
        $.ajax({
            url         : 'quiebre/cargar',
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
                    HTMLCargarQuiebre(obj.datos);
                    quiebreObj=obj.datos;
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoQuiebres:function(id,AD){
        $("#form_quiebres").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_quiebres").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_quiebres").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'quiebre/cambiarestado',
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
                    $('#t_quiebres').dataTable().fnDestroy();
                    Quiebres.CargarQuiebres();
                    $('#quiebreModal .modal-footer [data-dismiss="modal"]').click();
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
    }
};
</script>