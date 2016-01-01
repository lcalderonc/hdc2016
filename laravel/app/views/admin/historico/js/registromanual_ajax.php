<script type="text/javascript">
var map, edificios=[];
var markerEstructura=[];
var markerCliente=[];
var markerHelp=[];
var Registromanual={
    Edificios:function(y,x,cant){
        $.ajax({
            url : 'edificio_cableado/listar',
            type : 'POST',
            cache : false,
            data : {coord_y:y,coord_x:x, cantidad:cant},
            dataType: 'json',
            success : function(obj){
                if(obj.rst==1){
                    //edificios
                    infowindow = new google.maps.InfoWindow();
                    //limpiando mapa
                    for (var i = 0; i < edificios.length; i++) {
                        edificios[i].setMap(null);
                    }
                    edificios=[];
                    //añadir marcadores
                    $.each(obj.datos,function(index,data){
                        //dibujar marcador
                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(data.coord_y, data.coord_x),
                            draggable: false,
                            icon: 'img/icons/office-building.png',
                            map: map
                        });
                        edificios.push(marker);
                        infocontent = "Nombre: <strong>"+data.nombre +"</strong>"+
                                 " <br> Distancia:<strong>"+
                                 data.distance+" metros</strong>"+
                                 "<br>Dirección:<strong>"+
                                 data.direccion_obra+"</strong>"+
                                 "<br><a href=\"javascript:closeInfoWindow()\">Cerrar</a>";
                        doInfoWindow(map, marker, infocontent);
                    });
                    htmlListarSlct(obj,'slct_edificio','simple');
                } else {
                    Psi.mensaje('success', "No se encontraron edificios alrededor", 6000);
                }
            }
        });
    },
    Crear:function(){
        var datos=$("#frm_criticos").serialize().split("txt_").join("").split("slct_").join("").split("chk_").join("").split("_modal").join("");
        var accion="registro_manual/crear";
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
                    limpiar();
                    $('#frm_criticos .modal-footer [data-dismiss="modal"]').click();
                    Psi.mensaje('success', obj.msj, 6000);
                } else { 
                    $(".overlay,.loading-img").remove();
                    $("#valida_codactu").removeClass("has-info").addClass("has-warning");
                    $("#valida_codactu label").html('<i class="fa fa-warning"></i> Ocurrio algo inesperado con el servidor, favor intenta nuevamente');
                    $("#btn_limpiar_todo").css("display","");
                    Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    },
    BuscaCliente:function(parametro){
        $.ajax({
            url : 'registro_manual/buscacliente',
            type : 'POST',
            cache : false,
            data : parametro,
            dataType: 'json',
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj){
                limpiar();
                if(obj.rst>0){//devuelde mayor a cero siemrpe que hay registros
                    //y 2 latitud, x 1 longitud 
                    var datos = obj.datos;
                    var longitud = obj.datos[1];
                    var latitud = obj.datos[2];
                    var cantidad = datos.length;
                    var ids, data;
                    var estado = obj.estado;
                    var listado="",i;
                    if (estado=='gestionado' || estado=='temporal') {
                        for ( i = 0; i < cantidad; i ++ ) {
                            listado+="<tr>";
                            listado+="<td>"+datos[i].telefono+"</td>";
                            listado+="<td>"+datos[i].codservcms+"</td>";
                            listado+="<td>"+datos[i].direccion+"</td>";
                            listado+="<td>"+datos[i].codclie+"</td>";
                            listado+="<td>"+datos[i].codclicms+"</td>";
                            listado+="<td>"+estado+"</td>";
                            listado+="<td><a href='"+datos[i].codactu+"' class='bandeja'>"+datos[i].codactu+"</a></td>";
                            listado+="</tr>";
                        }
                        $('#dialog-servicios').modal('show');
                        $("#tb_consulta_servicios").html('');
                        $('#t_consulta_servicios').dataTable().fnDestroy();
                        $("#tb_consulta_servicios").html(listado);
                        $("#t_consulta_servicios").dataTable();
                        $(".bandeja").click(function (event){
                            event.preventDefault();
                            var codactu = $(this).attr("href");
                            var url = "admin.historico.bandeja";
                            newWin=window.open(url,'myWindow');
                            newWin.onload=function(){
                                this.$('#slct_tipo').val('gd.averia');
                                this.$('#txt_buscar').val(codactu);
                                this.$('#btn_personalizado').trigger('click');
                                this.$('#bandejaModal').modal('show');
                                variables={ buscar:codactu,
                                            tipo:'gd.averia'
                                          };
                                this.Bandeja.CargarBandeja('M',this.verificaDataModal,variables);
                            };
                            
                        });
                    }else if (obj.estado.length>0) {//esta en pendientes,liquidados,maestro

                        arrays=0;
                        for (i = 0; i < obj.estado.length; i++) {
                            estado=obj.estado[i];
                            arrcliente=datos[estado];
                            arrays+=Number(arrcliente.length);
                            for (var j = 0; j < arrcliente.length; j++) {
                                //arrcliente[0];
                                listado+="<tr>";
                                listado+="<td>"+arrcliente[j].telefono+"</td>";
                                listado+="<td>"+arrcliente[j].codservcms+"</td>";
                                listado+="<td>"+arrcliente[j].direccion+"</td>";
                                listado+="<td>"+arrcliente[j].codclie+"</td>";
                                listado+="<td>"+arrcliente[j].codclicms+"</td>";
                                listado+="<td>"+estado+"</td>";
                                listado+="<td><a href='"+arrcliente[j].codclie+"' class='registro'>"+arrcliente[j].codclie+"</a></td>";
                                listado+="</tr>";
                            }
                        }
                        if (arrays>1) {//mostrar modal
                            $('#dialog-servicios').modal('show');
                            $("#tb_consulta_servicios").html('');
                            $('#t_consulta_servicios').dataTable().fnDestroy();
                            $("#tb_consulta_servicios").html(listado);
                            $("#t_consulta_servicios").dataTable();
                            $(".registro").click(function (event){
                                event.preventDefault();
                                var codclie = $(this).attr("href");
                                $("#f_telefonoCliente").val('');
                                $("#f_codigoClienteATIS").val(codclie);
                                $("#f_codigoServicioCMS").val('');
                                $("#f_codigoClienteCMS").val('');
                                $("#btn_busqueda").trigger( "click" );
                                $('#dialog-servicios').modal('hide');
                            });
                        } else {
                            //enviar el estado y la data
                            estado = obj.estado[0];
                            cargarDatos(estado, obj.datos[estado][0]);
                        }
                    }
                } else {
                    initialize(-12.046374,-77.0427934);
                    Psi.mensaje('success', "No se encontraron registros", 6000);
                    //alert(obj.datos);
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    },
    ValidarTexto:function(valor){
        $.ajax({
            url         : 'gestion/validacodigo',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {codactu: $.trim(valor)},
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    $("#valida_codactu").removeClass("has-info").addClass("has-success");
                    $("#valida_codactu label").html('<i class="fa fa-check"></i> Código permitido');
                }
                else{
                    $("#valida_codactu").removeClass("has-info").addClass("has-warning");
                    $("#valida_codactu label").html('<i class="fa fa-warning"></i> Código vacio ó Existente');
                }
                $("#btn_limpiar_todo").css("display","");
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#valida_codactu").removeClass("has-info").addClass("has-warning");
                $("#valida_codactu label").html('<i class="fa fa-warning"></i> Ocurrio algo inesperado con el servidor, favor intenta nuevamente');
                $("#btn_limpiar_todo").css("display","");
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    },
    cargarMdf:function(id, data, datos){
        $.ajax({
            url         : 'mdf/listar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    var cantidad = obj.datos.length;
                    if(cantidad>0){
                        //ids son valores que se seleccionaran
                        HTMLListar(obj.datos,id,'mdf');
                        //aqui deberia cargarse losiguiente a mdf
                        var mdf = $('#mdf').val();
                        if (mdf!=='') {
                            var arrData = mdf.split("___");
                            mdf = arrData[0];
                            //seleccionar troba o cable, segun servicio
                            var tipo_registro = $("#slct_tipo_averia").val();
                            var buscar = tipo_registro.indexOf("catv");
                            if (buscar >= 0) {//existe catv
                                //programar para el caso de catv
                                var troba = datos.troba;
                                ids=[troba];
                                data = {nodo:mdf};
                                $("#troba").multiselect('destroy');
                                Registromanual.cargarTroba(ids,data,datos);
                            } else {//adsl, stb
                                if (datos.armario!=='' && datos.armario !=undefined && datos.armario !=null) {
                                    //verificar que no tengan formato de cable
                                    var armario =datos.armario;
                                    var n=armario.search("/");
                                    if (n>0) {//si existe es cable
                                        ids = [armario];
                                        data = {mdf:mdf};
                                        $("#cable").multiselect('destroy');
                                        //llamar funcion que cargue cable
                                        Registromanual.cargarCable(ids,data,datos);
                                    } else {
                                        ids = [datos.armario];
                                        data = {mdf:mdf};
                                        $("#cable").multiselect('destroy');
                                        //llamar funcion que carge armario
                                        Registromanual.cargarArmario(ids,data,datos);
                                    }
                                //asignarle cable
                                } else if (datos.cable) {
                                    ids = [datos.cable];
                                    data = {mdf:mdf};
                                    $("#cable").multiselect('destroy');
                                    //llamar funcion que cargue cable
                                    Registromanual.cargarCable(ids,data,datos);
                                } else {
                                    $("#cable").multiselect('destroy');
                                    $("#cable option").remove();
                                    $("#cable").append("<option value=''>Seleccione</option>");
                                    slctGlobalHtml('cable','simple');
                                }
                            }
                        } else {
                            //$("#mdf").multiselect('destroy');
                            $("#mdf option").remove();
                            $("#mdf").append("<option value=''>Seleccione</option>");
                            slctGlobalHtml('mdf','simple');
                        }
                    } else {
                        $("#mdf").multiselect('destroy');
                        $("#mdf option").remove();
                        $("#mdf").append("<option value=''>Seleccione</option>");
                        slctGlobalHtml('mdf','simple');
                    }
                }
            }
        });
    },
    cargarArmario:function(id, data, datos){
        $.ajax({
            url         : 'lista/armario',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    var cantidad = obj.datos.length;
                    if(cantidad>0){
                        //ids son valores que se seleccionaran
                        HTMLListar(obj.datos,id,'cable');
                        var mdf = datos.mdf;
                        var armario = datos.armario;
                        data = {mdf:mdf, armario:armario};
                        ids = datos.terminal;
                        //seleccionar
                        if (datos.terminal!=='') {
                            $("#terminal").multiselect('destroy');
                            Registromanual.cargarTerminal(ids,data,datos);
                        } else {
                            $("#terminal").multiselect('destroy');
                            $("#terminal option").remove();
                            $("#terminal").append("<option value=''>Seleccione</option>");
                            slctGlobalHtml('terminal','simple');
                        }
                    } else {
                        //si no hay datos de armario
                        $("#armario").multiselect('destroy');
                        $("#armario option").remove();
                        $("#armario").append("<option value=''>Seleccione</option>");
                        slctGlobalHtml('armario','simple');
                    }
                }
            }
        });
    },
    cargarCable:function(id, data, datos){
        $.ajax({
            url         : 'lista/cable',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    var cantidad = obj.datos.length;
                    if(cantidad>0){
                        //ids son valores que se seleccionaran
                        HTMLListar(obj.datos,id,'cable');
                        var mdf = datos.mdf;
                        var cable = datos.cable;
                        if (cable==='' || cable ==undefined) {
                            cable=datos.armario;//armario con formato de cable
                        }
                        data = {mdf:mdf, cable:cable};
                        ids = datos.terminal;
                        if (datos.terminal!=='') {
                            //seleccionar
                            $("#terminal").multiselect('destroy');
                            Registromanual.cargarTerminal(ids,data,datos);
                        } else {
                            $("#terminal").multiselect('destroy');
                            $("#terminal option").remove();
                            $("#terminal").append("<option value=''>Seleccione</option>");
                            slctGlobalHtml('terminal','simple');
                        }
                    } else {
                        //si no hay datos de cable
                        $("#cable").multiselect('destroy');
                        $("#cable option").remove();
                        $("#cable").append("<option value=''>Seleccione</option>");
                        slctGlobalHtml('cable','simple');
                    }
                }
            }
        });
    },
    cargarTerminal:function(id, data, datos){
        $.ajax({
            url         : 'lista/terminal',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    var cantidad = obj.datos.length;
                    if(cantidad>0){
                        HTMLListar(obj.datos,id,'terminal');
                    } else {
                        //si no hay datos de terminal
                        $("#terminal").multiselect('destroy');
                        $("#terminal option").remove();
                        $("#terminal").append("<option value=''>Seleccione</option>");
                        slctGlobalHtml('terminal','simple');
                    }
                }
            }
        });
    },
    cargarTroba:function(id, data, datos){
        $.ajax({
            url         : 'lista/troba',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    var cantidad = obj.datos.length;
                    if(cantidad>0){
                        //ids son valores que se seleccionaran
                        HTMLListar(obj.datos,id,'troba');
                        var nodo = datos.mdf;
                        var troba = datos.troba;
                        data = {nodo:nodo, troba:troba};
                        ids = datos.amplificador;
                        //seleccionar
                        if (datos.amplificador!=='') {
                            $("#amplificador").multiselect('destroy');
                            Registromanual.cargarAmp(ids,data,datos);
                        } else {
                            $("#amplificador").multiselect('destroy');
                            $("#amplificador option").remove();
                            $("#amplificador").append("<option value=''>Seleccione</option>");
                            slctGlobalHtml('amplificador','simple');
                        }
                    } else {
                        //si no hay datos de armario
                        $("#troba").multiselect('destroy');
                        $("#troba option").remove();
                        $("#troba").append("<option value=''>Seleccione</option>");
                        slctGlobalHtml('troba','simple');
                    }
                }
            }
        });
    },
    cargarAmp:function(id, data, datos){
        $.ajax({
            url         : 'lista/amplificador',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    var cantidad = obj.datos.length;
                    if(cantidad>0){
                        //ids son valores que se seleccionaran
                        HTMLListar(obj.datos,id,'amplificador');
                        var troba = datos.troba;
                        var nodo = datos.mdf;
                        var amp =datos.amplificador;
                        data = {amplificador:amp,nodo:nodo,troba:troba};
                        ids = datos.tap;
                        //seleccionar
                        if (datos.tap!=='') {
                            $("#tap").multiselect('destroy');
                            Registromanual.cargarTap(ids,data,datos);
                        } else {
                            $("#tap").multiselect('destroy');
                            $("#tap option").remove();
                            $("#tap").append("<option value=''>Seleccione</option>");
                            slctGlobalHtml('tap','simple');
                        }
                    } else {
                        //si no hay datos de armario
                        $("#amplificador").multiselect('destroy');
                        $("#amplificador option").remove();
                        $("#amplificador").append("<option value=''>Seleccione</option>");
                        slctGlobalHtml('amplificador','simple');
                    }
                }
            }
        });
    },
    cargarTap:function(id, data, datos){
        $.ajax({
            url         : 'lista/tap',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : data,
            success : function(obj) {
                if(obj.rst==1){
                    var cantidad = obj.datos.length;
                    if(cantidad>0){
                        HTMLListar(obj.datos,id,'tap');
                    } else {
                        //si no hay datos de tap
                        $("#tap").multiselect('destroy');
                        $("#tap option").remove();
                        $("#tap").append("<option value=''>Seleccione</option>");
                        slctGlobalHtml('tap','simple');
                    }
                }
            }
        });
    }
};
</script>