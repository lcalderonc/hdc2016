<script type="text/javascript">
var zonal_id, empresa_id, quiebre_grupo_id, horario_tipo_id;
var gl_capacidad, gl_capacidad_row, gl_capacidad_col;
var cuposObj=[], horariosObj=[];
var cupos;
var Cupos={
    AgregarEditarCupos:function(AE){
        var datos=$("#form_cupos").serialize().split("txt_").join("").split("slct_").join("");
        var accion="cupo/crear";
        if(AE==1){
            accion="cupo/editar";
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
                    $('#t_cupos').dataTable().fnDestroy();

                    Cupos.CargarCupos(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#cupoModal .modal-footer [data-dismiss="modal"]').click();
                } else { 
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
    CargarCupos:function(evento){
        $.ajax({
            url         : 'cupo/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    cuposObj=obj.datos;
                    evento();
                } else
                    $("#tb_cupos").html('');
            },
            error: function(){
                $("#tb_cupos").html('');
                $(".overlay,.loading-img").remove();
            }
        });
    },
    CambiarEstadoCupos:function(id,AD){
        $("#form_cupos").append("<input type='hidden' value='"+id+"' name='id'>");
        $("#form_cupos").append("<input type='hidden' value='"+AD+"' name='estado'>");
        var datos=$("#form_cupos").serialize().split("txt_").join("").split("slct_").join("");
        $.ajax({
            url         : 'cupo/cambiarestado',
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
                    $('#t_cupos').dataTable().fnDestroy();
                    Cupos.CargarCupos(activarTabla);
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#cupoModal .modal-footer [data-dismiss="modal"]').click();
                } else { 
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
    Update:function(id, data){
        //actualizar array cuposObj
        cupos= _.without(cupos, _.findWhere(cupos, {id: Number(id)}));
        cupos= _.without(cupos, _.findWhere(cupos, {id: String(id)}));
        var actualizado={
        capacidad: data.capacidad,
        capacidad_horario_id: data.capacidad_horario_id,
        //dia: "Lunes",
        dia_id: data.dia_id,
        //empresa: "CALATEL NORTE",
        //empresa_id: 1,
        //estado: 1,
        //hora_fin: "13:00:00",
        //hora_inicio: "11:00:00",
        //horario: "11:00 - 13:00",
        horario_id: data.horario_id,
        //horario_tipo_id: 1,
        guardar: 1,
        id: data.id};
        //minutos: 120,
        //quiebre_grupo: "CRITICOS",
        //quiebre_grupo_id: 1,
        //zonal: "Lima",
        //zonal_id: 8,
        //zonal_select: "LIM|8"};
        cupos.push(actualizado);

    },
    Create:function(thisObj,data){
        var nuevo={
                    capacidad: data.capacidad,
                    capacidad_horario_id: data.capacidad_horario_id,
                    //dia: "Lunes",
                    dia_id: data.dia_id,
                    //empresa: "CALATEL NORTE",
                    empresa_id: data.empresa_id,
                    //estado: 1,
                    //hora_fin: "13:00:00",
                    //hora_inicio: "11:00:00",
                    //horario: "11:00 - 13:00",
                    horario_id: data.horario_id,
                    horario_tipo_id: data.horario_tipo_id,
                    //id: 8,
                    //minutos: 120,
                    //quiebre_grupo: "CRITICOS",
                    quiebre_grupo_id: data.quiebre_grupo_id,
                    //zonal: "Lima",
                    zonal_id: data.zonal_id,
                    guardar: 1
                };
        //zonal_select: "LIM|8"};
        cupos.push(nuevo);
        
    },
    updateCupos:function(data){
        $.ajax({
            url         : 'cupo/updatecupos',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {data:data},
            beforeSend : function() {
                //horariosObj=[];
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    Cupos.CargarCupos(retornarCupos);
                    /*horariosObj=obj.datos;
                    if (horariosObj.length>0) {
                        evento(horariosObj);
                    }*/
                } else { 
                    $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b>'+obj.msj+'</b>'+
                                '</div>');
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
    cargarHorarios:function(data, evento){
        $.ajax({
            url         : 'lista/horario',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {
                horariosObj=[];
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    horariosObj=obj.datos;
                    if (horariosObj.length>0) {
                        evento(horariosObj);
                    }
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