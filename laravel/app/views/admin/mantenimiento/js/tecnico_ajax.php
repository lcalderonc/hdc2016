<script type="text/javascript">
var celulas_selec=[], tecnicosObj=[], ninguno;
var Tecnicos={
    AgregarEditarTecnico:function(AE){
        $("#form_tecnicos input[name='celulas_selec']").remove();
        $("#form_tecnicos input[name='ninguno']").remove();
        $("#form_tecnicos").append("<input type='hidden' value='"+celulas_selec+"' name='celulas_selec'>");
        $("#form_tecnicos").append("<input type='hidden' value='"+ninguno+"' name='ninguno'>");
        
        var datos=$("#form_tecnicos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="tecnico/crear";
        if(AE==1){
            accion="tecnico/editar";
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
                    $('#t_tecnicos').dataTable().fnDestroy();

                    Tecnicos.CargarTecnicos(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#tecnicoModal .modal-footer [data-dismiss="modal"]').click();
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
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente. Si el problema persiste favor de comunicarse a ubicame@puedesencontrar.com</b>'+
                                '</div>');
            }
        });
    },
    CargarCelulas:function(tecnico_id){
        $.ajax({
            url         : 'tecnico/cargarcelulas',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {'tecnico_id':tecnico_id},
            success : function(obj) {
                if(obj.rst==1){
                    HTMLListarSlct(obj);
                }
            }
        });
    },
    CargarTecnicos:function(evento){
        $.ajax({
            url         : 'tecnico/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                //slctGlobal.listarSlct('celula','slct_celula','multiple',[],0,1);
                //slctGlobal.listarSlct('celula','slct_celula','simple');
                var data = {usuario: 1};
                slctGlobal.listarSlct('celula','slct_celula','simple',[],0,1);
                slctGlobal.listarSlct('empresa','slct_empresa','simple',[],data,0,'#slct_celula','E');
            },
            success : function(obj) {
                if(obj.rst==1){
                    tecnicosObj=obj.datos;
                    evento();
                } else
                    $("#tb_tecnicos").html('');
                $(".overlay,.loading-img").remove();
            },
            error: function(){
            }
        });
    },
    CambiarEstadoTecnicos:function(id,AD){
        $("#form_tecnicos").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_tecnicos").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_tecnicos").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'tecnico/cambiarestado',
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
                    $('#t_tecnicos').dataTable().fnDestroy();
                    Tecnicos.CargarTecnicos(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#tecnicoModal .modal-footer [data-dismiss="modal"]').click();
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
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>Ocurrio una interrupción en el proceso,Favor de intentar nuevamente. Si el problema persiste favor de comunicarse a ubicame@puedesencontrar.com</b>'+
                                '</div>');
            }
        });
    }
};
</script>