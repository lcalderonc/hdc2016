<script type="text/javascript">
var search2='';
var currentPage2 = 0;
var horario_id, horarioObj;
var Horarios={

    AgregarEditarHorario:function(AE){
        var datos=$("#form_horario").serialize().split("txt_").join("").split("slct_").join("");
        var accion="horario/crear";
        if(AE==1){
            accion="horario/editar";
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
                    search2=$('#t_horariotb input[type=search]').val();
                    var table2 = $('#t_horarios').DataTable();
                    var info2 = table2.page.info();
                    currentPage2 = info2.page;
                    $('#t_horarios').dataTable().fnDestroy();

                    Horarios.CargarHorarios(activarTabla5);/*
                    $('#horarioModal .modal-footer [data-dismiss="modal"]').click();
                    Psi.mensaje('success', obj.msj, 6000);*/

                    
                    $('#t_horarios').dataTable().fnPageChange(currentPage2,true);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#horarioModal .modal-footer [data-dismiss="modal"]').click();
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
    CargarHorarios:function(evento){
        $.ajax({
            url         : 'horario/cargar',
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
                    HTMLCargarHorario(obj.datos);
                    horarioObj=obj.datos;
                }
                $("#tb_horarios").html(html); 
                $(".overlay,.loading-img").remove();
                activarTabla5();//filtro de tabla
                $('input[type=search]').val(search2);
                $('input[type=search]').trigger('keyup');
                $('#t_horarios').dataTable().fnPageChange(currentPage2,true);
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoHorarios:function(id,AD){
        $("#form_horario").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_horario").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_horario").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'horario/cambiarestado',
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
                    search2=$('#t_horariotb input[type=search]').val();
                    var table = $('#t_horarios').DataTable();
                    var info = table.page.info();
                    currentPage2 = info.page;

                    $('#t_horarios').dataTable().fnDestroy();
                    Horarios.CargarHorarios(activarTabla5);
                    $('#t_horarios').dataTable().fnPageChange(currentPage2,true);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#horarioModal .modal-footer [data-dismiss="modal"]').click();

/*
                    $('#horarioModal .modal-footer [data-dismiss="modal"]').click();
                    Psi.mensaje('success', obj.msj, 6000);*/
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