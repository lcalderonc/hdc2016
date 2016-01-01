<script type="text/javascript">
var Historico={
    BuscaCliente:function(parametro){
        $.ajax({
            url : 'historico/buscacliente',
            type : 'POST',
            cache : false,
            data : parametro,
            dataType: 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj){
                limpiaForm();
                if(obj.rst==1){
                    //$.each(obj, function (){
                    $.each(obj.datos,function(index,datos){
                        //console.log(this.telefono);
                        rm_telefono = this.telefono;
                        rm_inscripcion = this.inscripcio;
                        rm_codcliente = this.codclie;
                        rm_apaterno = this.appater;
                        rm_amaterno = this.apmater;
                        rm_segmento = this.segest;
                        rm_zonal = this.zonal;
                        rm_mdf = this.mdf;
                        rm_nombre = this.nombre;
                    });
                    datosClienteHTML(obj.datos);
                } else {
                    alert(obj.datos);
                }
                $(".overlay,.loading-img").remove();
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
    listarAverias:function(telefono,codservcms,codclicms){
        $.ajax({
            url : 'historico/listaraverias',
            type : 'POST',
            cache : false,
            data : {
                telefonoCliente : telefono,
                codigoServicioCMS : codservcms,
                codigoClienteCMS : codclicms,
                esCritico: esCritico
            },
            dataType: 'json',
            success : function(response){
                //$("#averias").html(response);
                //pasar la data al tab averias
                if (response.rst==1) {
                    listarAveriasHTML(response);
                } else {
                    $("#tb_resultado_averias2").html('');
                    $('#t_resultado_averias2').dataTable().fnDestroy();
                    $("#t_resultado_averias2").dataTable();
                }
            }
        });
    },
    listarProvision:function(telefono){
        $.ajax({
            url : 'historico/listarprovision',
            type : 'POST',
            cache : false,
            data : {
                telefonoCliente : telefono
            },
            dataType: 'json',
            success : function(response){
                //$("#provision").html(response);
                //pasar la data al tab provision
                if (response.rst==1) {
                    listarProvisionHTML(response.datos);
                } else {
                    $('#tb_resultado_provision').html('');
                    $('#t_resultado_provision').dataTable().fnDestroy();
                    $("#t_resultado_provision").dataTable();
                }
            }
        });
    },
    listarLlamadas:function(telefono){
        $.ajax({
            url : 'historico/listarllamadas',
            type : 'POST',
            cache : false,
            data : {
                telefonoCliente : telefono
            },
            dataType: 'json',
            success : function(response){
                //$("#llamadas").html(response);
                //pasar la data al tab llamadas
                if (response.rst==1) {
                    listarLlamadassHTML(response.datos);
                } else {
                    $('#tb_resultado_llamadas').html('');
                    $('#t_resultado_llamadas').dataTable().fnDestroy();
                    $("#t_resultado_llamadas").dataTable();
                }
            }
        });
    },
    listarCriticos:function(telefono){
        $.ajax({
            url : 'historico/listarcriticos',
            type : 'POST',
            cache : false,
            data : {
                telefonoCliente : telefono
            },
            dataType: 'json',
            success : function(response){
                //$("#criticos").html(response);
                //pasar la data al tab criticos
                if (response.rst==1) {
                    listarCriticosHTML(response.datos);
                } else {
                    $('#tb_resultado_criticos').html('');
                    $('#t_resultado_criticos').dataTable().fnDestroy();
                    $("#t_resultado_criticos").dataTable();
                }
            }
        });
    },
    verDetalle:function(tipo, negocio, actuacion){
        $.ajax({
            url : 'historico/listaraveriadetalle',
            type : 'POST',
            cache : false,
            data : {
                tipo: tipo,
                negocio: negocio,
                actuacion: actuacion
            },
            dataType: 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(response){
                $(".overlay,.loading-img").remove();
                if(response.rst==1){
                    verDetalleHTML(response.datos);
                } else
                    alert("No se encontraron detalles");
                //$("#resDetalle").html(response);
                //pasar la data al div resDetalle
                /*
                var cx = $("#txtCoordX").val();
                var cy = $("#txtCoordY").val();
                var zonal =  $('#cmb_zonales').val();
                var mdf = $('#cmb_mdfs').val();
                var tipo_red =  $('#cmb_tipored').val();
                var cable_armario = $('#cmb_cable_armario').val();
                var caja_terminal =  $('#cmb_caja_terminal').val();*/

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

    }
};
</script>