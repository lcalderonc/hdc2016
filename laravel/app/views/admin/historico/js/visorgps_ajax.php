<script type="text/javascript">
var Visorgps={
    ListarSlct:function(controlador){
        $.ajax({
            url         : controlador+'/listar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            beforeSend : function() {                
                //$("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                
                var html = "";
                
                if(obj.rst==1){                    
                    $.each(obj.datos, function (index, data){
                        /**
                         * Tablas relacionadas
                         */
                        var optValue = data.id;
                        
                        //Celula
                        if ( controlador === 'celula' )
                        {
                            optValue = data.relation + '_' + data.id;
                        }
                        
                        html += '<option value="'+optValue+'">' 
                            + data.nombre 
                            + '</option>';
                    });
                    
                    //Primera opcion por defecto
                    if ( controlador === 'empresa' )
                    {
                        $("#slct_" + controlador).prepend(
                            "<option value=\"\">-Seleccione-</option>"
                        );
                    }
                    
                    if ( controlador === 'celula' )
                    {
                        $("#slct_" + controlador).prepend(
                            "<option value=\"\">-Seleccione-</option>"
                        );
                    }
                    
                    $("#slct_" + controlador).append(html);
                    $("#slct_" + controlador).multiselect('deselectAll', false);
                    $("#slct_" + controlador).multiselect('rebuild');
                    $("#slct_" + controlador).multiselect('refresh');
                }  
                //$(".overlay,.loading-img").remove();
            },
            error: function(){
                //$(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                        '<i class="fa fa-ban"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },
    PanelCelulaTecnico:function(){
    
        bounds = new google.maps.LatLngBounds();        
    
        var data = $("form#form_visorgps").serialize().split("txt_").join("").split("slct_").join("");
    
        $.ajax({
            url         : 'visorgps/panelcelulatecnico',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            beforeSend : function() {                
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                
                //Lista de tecnicos
                doTecList(obj.tecnicos, obj.icons);
                //Lista agendas tecnicos
                doTecAgenda(obj.data, obj.icons);
              
                $(".overlay,.loading-img").remove();
                $('#divresultado').html('<label>Resultado de busqueda:   '+obj.tecnicos.length+' Técnicos / '+obj.data.length+' Objetos Mapeados</label>');
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
    DoPath:function(code, color){
        //Get coords by address                
        $.ajax({
            url     : "visorgps/codepath",
            type    : "POST",
            cache   : false,
            data    : "codePath=" + code + "&pdate=" + $("#fecha_estado").val(),
            dataType: 'json',
            beforeSend : function() {                
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            error: function(data) {
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                '</div>');
            },
            success: function(data) {
                
                //Dibujar ruta del tecnico
                drawTecPath(data, code, color, objMap);
                
                $(".overlay,.loading-img").remove();
            }
        });
        return true;
    },
    DoListaCelula:function(empresa_id){
        //Get coords by address                
        $.ajax({
            url     : "visorgps/listacelula",
            type    : "POST",
            cache   : false,
            data    : "empresa_id=" + empresa_id,
            dataType: 'json',
            beforeSend : function() {                
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            error: function(data) {
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                '</div>');
            },
            success: function(data) {
                
                $("#slct_celula").find("option").remove("option");
                $.each(data.data, function (id, field){
                    var celula_id = 'E' + field.empresa_id + '_' + field.id;
                    $("#slct_celula").append("<option value=\""+celula_id+"\">"+field.nombre+"</option>");
                });
                
                $(".overlay,.loading-img").remove();
            }
        });
        return true;
    },
    DoSaveGrupoTecnico: function (celula_id, tecnico_id, grupos){
        var data = "celula_id="
                        +celula_id
                        +"&tecnico_id="
                        +tecnico_id
                        +"&grupos="
                        +grupos;
        $.ajax({
            
            url     : "visorgps/savegrupotecnico",
            type    : "POST",
            cache   : false,
            data    : data ,
            dataType: 'json',
            beforeSend : function() {                
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            error: function(data) {
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                '</div>');
            },
            success: function(data) {                
                $(".overlay,.loading-img").remove();
            }
        });
        return true;
    },
    GetDetallePaso: function (dato){
        $.ajax({
            
            url     : "officetrack/detallepaso",
            type    : "POST",
            cache   : false,
            data    : 'dato=' +dato ,
            beforeSend : function() {                
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            error: function(data) {
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                    '<i class="fa fa-ban"></i>'+
                                    '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                    '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                '</div>');
            },
            success: function(data) {                
                $(".overlay,.loading-img").remove();
                $(".detalle_paso").html(data);
            }
        });
        return true;
    }
}
</script>