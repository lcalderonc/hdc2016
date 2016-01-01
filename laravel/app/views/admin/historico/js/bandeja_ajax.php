<script type="text/javascript">
var Bandeja={
    guardarMovimiento:function(){
        var datos=$("#form_bandeja").serialize().split("txt_").join("").split("slct_").join("").split("chk_").join("").split("_modal").join("");
        //var accion="gestion_movimiento/crear";
        var accion="bandeja/recepccion";
        $.ajax({
            url         : accion,
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos + "&cliente_xy_insert=" + cliente_xy_insert,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    variables=  {   buscar:obj.codactu,
                                    tipo:'gd.averia'
                                };

                    Bandeja.CargarBandeja('M',HTMLCargarBandeja,variables);

                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#bandejaModal .modal-footer [data-dismiss="modal"]').click();
                }
                else{
                    alert(obj.msj);
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
    guardarObservacion:function(){
        var datos=$("#form_observacion").serialize().split("txt_").join("").split("slct_").join("").split("chk_").join("").split("_modal").join("");
        var accion="gestion_movimiento/crearobs";

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
                    variables=  {   buscar:obj.codactu,
                                    tipo:'gd.averia'
                                };

                    Bandeja.CargarBandeja('M',HTMLCargarBandeja,variables);
                    $("#txt_observacion_o_modal").val("");
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b>'+obj.msj+'</b>'+
                                    '</div>');
                    $('#observacionModal .modal-footer [data-dismiss="modal"]').click();
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
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },
    CargarBandeja:function(PG,evento,variables){
        var datos="";
        var fondo=[];
        if(PG=="P" || PG=="G"){

            $('#t_bandeja').dataTable().fnDestroy();
            $('#t_bandeja')
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
                  var trsimple;
                    for(i=0;i<fondo.length;i++){
                        trsimple=$("#tb_bandeja tr td").filter(function() {
                            return $(this).text() == fondo[i];
                        }).parent('tr');
                        $(trsimple).find("td:eq(0)").css("background-color", "#DA8F66");
                    }
                    $(".overlay,.loading-img").remove();
                    $('#t_bandeja').removeAttr("style");
                },
                "ajax": {
                    "url": "gestion/cargar",
                    "type": "POST",
                    //"async": false,

                    "data": function(d){
                        //d.datos = datos;
                        var contador=0;
                        if(PG=="P"){// Filtro Personalizado
                            datos=$("#form_Personalizado").serialize().split("txt_").join("").split("slct_").join("").split("%5B%5D").join("[]").split("&");
                        }
                        else if(PG=="G"){ // Filtro General
                            datos=$("#form_General").serialize().split("txt_").join("").split("slct_").join("").split("%5B%5D").join("[]").split("+").join(" ").split("%7C").join("|").split("&");
                        }

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
                "columnDefs": [
                    {
                        "targets": 0,
                        "data": function ( row, type, val, meta ) {
                            var vid=row.id;
                                if(row.id==''){
                                    temporalBandeja++;
                                    vid=  "T_"+temporalBandeja;
                                }
                                if(row.existe*1!=1){
                                    fondo.push(vid);
                                }
                                return vid;
                        },
                        "defaultContent": '',
                        "name": "id"
                    },
                    {
                        "targets": 1,
                        "data": "codactu",
                        "name": "codactu"
                    },
                    {
                        "targets": 2,
                        "data": "fecha_registro",
                        "name": "fecha_registro"
                    },
                    {
                        "targets": 3,
                        "data": "actividad",
                        "name": "actividad"
                    },
                    {
                        "targets": 4,
                        "data": "quiebre",
                        "name": "quiebre"
                    },
                    {
                        "targets": 5,
                        "data": "empresa",
                        "name": "empresa"
                    },
                    {
                        "targets": 6,
                        "data": "mdf",
                        "name": "mdf"
                    },
                    {
                        "targets": 7,
                        "data": "fh_agenda",
                        "name": "fh_agenda"
                    },
                    {
                        "targets": 8,
                        "data": "tecnico",
                        "name": "tecnico"
                    },
                    {
                        "targets": 9,
                        "data": function ( row, type, val, meta) {
                            return row.estado+"<br><font color='#327CA7'>"+row.cierre_estado+"</font>";
                        },
                        "defaultContent": '',
                        "name": "estado"
                    },
                    {
                        "targets": 10,
                        "orderable":false,
                        "searchable": false,
                        "data": function ( row, type, val, meta ) {
                            var officetrackColor='';var officetrackImage='';var officetrackbotton='';
                            if(row.transmision!=null && row.transmision!=0){
                                officetrackColor='btn-default';
                                if(row.transmision!=1 && row.transmision.split("-").length>1 && row.transmision.split("-")[1].substr(0,6).toLowerCase()=='inicio' ){
                                    officetrackColor='btn-success';
                                }
                                else if(row.transmision!=1 && row.transmision.split("-").length>1 && row.transmision.split("-")[1].substr(0,11).toLowerCase()=='supervision' ){
                                    officetrackColor='btn-warning';
                                }
                                else if(row.transmision!=1 && row.transmision.split("-").length>1 && row.transmision.split("-")[1].substr(0,6).toLowerCase()=='cierre' ){
                                    officetrackColor='btn-primary';
                                }

                                if(officetrackColor!='btn-default'){
                                    officetrackbotton='data-toggle="modal" data-target="#officetrackModal" data-id="'+row.id+'"';
                                }
                                officetrackImage=' &nbsp; <a class="btn '+officetrackColor+' btn-sm" '+officetrackbotton+'><i class="fa fa-mobile fa-3x"></i> </a>';
                            }

                            return  '<a class="btn bg-navy btn-sm" data-toggle="modal" data-target="#bandejaModal" data-codactu="'+row.codactu+'">'+
                                    '   <i class="fa fa-desktop fa-lg"></i>'+
                                    '</a>'+
                                    officetrackImage;
                        },
                        "defaultContent": ''
                    },
                ]
            } );

        }
        else {

            if(PG=="M"){ //Modal
                datos=variables;
            }

            $.ajax({
                url         : 'gestion/cargar',
                type        : 'POST',
                cache       : false,
                dataType    : 'json',
                data        : datos,
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success : function(obj) {
                    if(obj.rst==1){
                        evento(obj.datos);
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

        }
    },
    CargarGestionMovimiento:function(datos,evento,objant){
        $.ajax({
            url         : 'gestion_movimiento/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    evento(obj.datos,objant);
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
    validaEstado:function(datos,evento,datosanteriores){
        $.ajax({
            url         : 'estado/validar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    evento(obj.datos,datosanteriores);
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
    extraerXY:function(datos){

        $.ajax({
            url         : 'bandeja/extraerxy',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    cliente_x = obj.coord.x;
                    cliente_y = obj.coord.y;
                    cliente_xy_insert = obj.ins;
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
    CargarComponentes:function(evento,datos){
        $.ajax({
            url         : 'cat_componente/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    evento(obj.datos);
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
    UpdateLatLng:function (actu, lat, lng){
        $.ajax({
            url         : 'gestion/actualizaxy',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : 'actu=' + actu + '&lat=' + lat + '&lng=' + lng,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    $("#msj").html('<div class="alert alert-dismissable alert-success">'+
                                        '<i class="fa fa-check"></i>'+
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
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },
    ValidaPermisoToa:function(datos,permiso){
        //var permiso=9;
        $.ajax({
            url         : 'toa/validagestion',
            type        : 'POST',
            cache       : false,
            async       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    permiso=obj.permiso;
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
        
        return permiso;
    }
}
</script>
