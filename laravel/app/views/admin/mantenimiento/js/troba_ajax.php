<script type="text/javascript">
var troba_id, TrobaObj;
var Trobas={
    AgregarEditarTroba:function(AE){
        var datos=$("#form_trobas").serialize().split("txt_").join("").split("slct_").join("");
        var accion="dig_troba/crear";
        if(AE==1){
            accion="dig_troba/editar";
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
                    $('#t_trobas').dataTable().fnDestroy();

                    Trobas.CargarTrobas(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#trobaModal .modal-footer [data-dismiss="modal"]').click();
                } else if(obj.rst===0){
                    alert(obj.msj);
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
    CargarTrobas:function(evento){
        $.ajax({
            url         : 'dig_troba/listar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    HTMLCargarTrobas(obj.datos);
                    TrobaObj=obj.datos;
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
    ListarEmpresas:function( accion,empresa_id,contrata_zona){
        $.ajax({
            url         : 'empresa/listar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            success : function(obj) {
                if(obj.rst==1){
                    //empresa
                    $('#slct_empresa_id').html('');
                    $('#slct_contrata_zona').html('');
                    //$('#slct_contrata_reparto').html('');
                    $.each(obj.datos,function(index,data){
                        $('#slct_empresa_id').append('<option value='+data.id+'>'+data.nombre+'</option>');
                        $('#slct_contrata_zona').append('<option value='+data.id+'>'+data.nombre+'</option>');
                        //$('#slct_contrata_reparto').append('<option value='+data.id+'>'+data.nombre+'</option>');
                    });
                    if (accion=='nuevo'){
                        $('#slct_empresa_id').append("<option selected style='display:none;'>--- Elige Empresa ---</option>");
                        $('#slct_contrata_zona').append("<option selected style='display:none;'>--- Elige Empresa ---</option>");
                        $('#slct_contrata_reparto').append("<option selected style='display:none;'>--- Elige Empresa ---</option>");
                    }
                    else {
                       $('#slct_empresa_id').val( empresa_id );
                       $('#slct_contrata_zona').val( contrata_zona );
                       //$('#slct_contrata_reparto').val( contrata_reparto );
                    }
                }
            }
        });
    },
    cargarZonal:function(objeto,accion,zonal_id){
        $.ajax({
            url         : 'dig_troba/listarzonal',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            success : function(obj) {
                if(obj.rst==1){
                    $(objeto).html('');
                    if (accion=='nuevo'){
                        $.each(obj.datos,function(index,data){
                            $(objeto).append('<option value='+data.zonal+'>'+data.zonal+'</option>');
                        });
                        $(objeto).append("<option value='0' selected>- Zonal -</option>");
                    }
                    else{
                        $(objeto).append('<option value='+zonal_id+'>'+zonal_id+'</option>');
                        $(objeto).val( zonal_id );
                    }
                } 
            }
        });
    },
    cargarNodo:function(objeto,accion, nodo_id, zonal_id){
        $.ajax({
            url         : 'dig_troba/listarnodo',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {zonal_id:zonal_id},
            success : function(obj) {
                if(obj.rst==1){
                    $(objeto).html('');
                    if (accion=='nuevo'){
                        $.each(obj.datos,function(index,data){
                            $(objeto).append('<option value='+data.nodo+'>'+data.nodo+'</option>');
                        });
                        $(objeto).append("<option value='0' selected>- Nodo -</option>");
                    }
                    else{
                        $(objeto).append('<option value='+nodo_id+'>'+nodo_id+'</option>');
                        $(objeto).val( nodo_id );
                    }
                } 
            }
        });
    },
    cargarTroba:function(objeto,accion,troba_id, nodo_id){
        $.ajax({
            url         : 'dig_troba/listartroba',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {nodo_id:nodo_id},
            success : function(obj) {
                if(obj.rst==1){
                    $(objeto).html('');
                    if (accion=='nuevo'){
                        $.each(obj.datos,function(index,data){
                            $(objeto).append('<option value='+data.troba+'>'+data.troba+'</option>');
                        });
                        $(objeto).append("<option value='0' selected>- Troba -</option>");
                    }
                    else{
                        $(objeto).append('<option value='+troba_id+'>'+troba_id+'</option>');
                        $(objeto).val( troba_id );
                    }
                } 
            }
        });
    },
    CambiarEstadoTrobas:function(id,AD){
        $("#form_trobas").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_trobas").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_trobas").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'dig_troba/cambiarestado',
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
                    $('#t_trobas').dataTable().fnDestroy();
                    Trobas.CargarTrobas(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#usuarioModal .modal-footer [data-dismiss="modal"]').click();
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