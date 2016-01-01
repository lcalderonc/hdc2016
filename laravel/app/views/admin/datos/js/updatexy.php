<script type="text/javascript">
    //Default coords
    var xactu = -77.030855;
    var yactu = -12.046292;

    var updMapProps = {
        center: new google.maps.LatLng(yactu, xactu),
        zoom: 15,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    };

    var updMapTools = {};

    var updatemap = null;

    var updstreet = null;
    
    var updbounds = null;
    
    var boundsArray = [];

    var marker = new google.maps.Marker({
        position: new google.maps.LatLng(yactu, xactu),
        map: updatemap,
        title: 'Hello World!'
    });

    var markers = [];
    var polylines = [];
    var polygons = [];

    $(document).ready(function() {
        //Página con menú oculto
        $("[data-toggle='offcanvas']").click();

        //Construye mapa
        updatemap = doObjMap('update_map', updMapProps, updMapTools);
        doSearchBox();

        //Map Events
        doMapEvent();

        //Cronstruye Street View
        updstreet = geoStreetView(yactu, xactu, 'update_sv');

        //Street Events
        doStreetEvent();

        //Busqueda
        $("#btn_personalizado").click(function(event) {
            event.preventDefault();
            doBuscaOrden(
                    $("#slct_criterio").val(),
                    $("#txt_buscar").val()
                    );
        });

        $("#txt_buscar").keydown(function(e) {
            if (e.keyCode == 13) {
                doBuscaOrden(
                        $("#slct_criterio").val(),
                        $("#txt_buscar").val()
                        );
            }
        });

        //Ocultar listas FFTT
        $(".stb").hide();
        $(".catv").hide();
    });

    function actualizarUbicacion() {

        var doUpd = true;

        //Validaciones
        if ($.trim($("#txt_chk_actu").val()) == '')
        {
            doUpd = false;

            Psi.mensaje('danger', 'Debe buscar una orden', 6000);
        }

        if ($.trim($("#txt_upd_x").val()) == '')
        {
            doUpd = false;

            Psi.mensaje('danger', 'Click en el mapa para capturar coordenadas', 6000);
        }

        if (doUpd == true)
        {
            $.ajax({
                url: 'gestion/actualizaxy',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: 'actu='
                        + $("#txt_chk_actu").val()
                        + '&lat='
                        + $("#txt_upd_y").val()
                        + '&lng='
                        + $("#txt_upd_x").val(),
                beforeSend: function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function(obj) {
                    $(".overlay,.loading-img").remove();
                    if (obj.rst == 1) {

                        Psi.mensaje('success', obj.msj, 6000);
                    }
                },
                error: function() {
                    $(".overlay,.loading-img").remove();

                    Psi.mensaje('success', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
                }
            });
        }

    }

    /**
     * Crea marcador
     * @type Arguments
     */
    function doMarker() {
        //Marcador
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(yactu, xactu),
            map: updatemap,
            title: 'Hello World!'
        });

        return marker;
    }

    function doStreetEvent() {
        google.maps.event.addListener(updstreet, 'position_changed', function() {
            $("#txt_upd_x").val(updstreet.getPosition().lng());
            $("#txt_upd_y").val(updstreet.getPosition().lat());

            updatemap.setCenter(
                    new google.maps.LatLng(
                            updstreet.getPosition().lat(),
                            updstreet.getPosition().lng()
                            )
                    );

            marker.setMap(null);
            marker.setPosition(
                    new google.maps.LatLng(
                            updstreet.getPosition().lat(),
                            updstreet.getPosition().lng()
                            )
                    );
            marker.setMap(updatemap);
        });
    }

    function doMapEvent() {
        google.maps.event.addListener(updatemap, 'click', function(evento) {

            yactu = evento.latLng.lat();
            xactu = evento.latLng.lng();

            $("#txt_upd_x").val(xactu);
            $("#txt_upd_y").val(yactu);

            marker.setMap(null);
            marker.setPosition(
                    new google.maps.LatLng(yactu, xactu)
                    );
            marker.setMap(updatemap);

            //Street view
            updstreet = geoStreetView(yactu, xactu, 'update_sv');
            doStreetEvent();

        });
    }

    function doBuscaOrden(tipo, buscar) {

        var doSearch = true;

        //Validaciones
        if ($.trim($("#txt_buscar").val()) == '')
        {
            doSearch = false;

            Psi.mensaje('danger', 'Debe buscar una orden', 6000);
        }

        if (doSearch == true)
        {
            $.ajax({
                url: 'gestion/cargar',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: 'tipo=' + tipo + '&buscar=' + buscar,
                beforeSend: function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function(obj) {
                    $(".overlay,.loading-img").remove();

                    $("#txt_chk_actu").val(buscar);

                    if (obj.rst == 1)
                    {
                        //Mapa y Street View
                        var data = obj.datos[0];
                        xactu = data.coord_x;
                        yactu = data.coord_y;

                        //Coordenadas de la orden a input
                        $("#txt_upd_x").val(xactu);
                        $("#txt_upd_y").val(yactu);

                        //Mostrar controles FFTT
                        var is_catv = data.tipo_averia.indexOf('catv');
                        var tipo_negocio = null;
                        
                        $("#txt_data_fftt").val(data.fftt);
                        $("#txt_dir_inst").val(data.direccion_instalacion);

                        if (is_catv != -1)
                        {
                            $(".stb").hide();
                            $(".catv").show();
                            tipo_negocio = 'catv';
                        } else {
                            $(".stb").show();
                            $(".catv").hide();
                            tipo_negocio = 'stb';
                        }

                        //Tipo de negocio
                        $("#txt_chk_tipo").val(tipo_negocio);

                        //Carga MDF/NODO
                        load_mdf_nodo(tipo_negocio);

                        //Reiniciar mapa y street view
                        updMapProps.center = new google.maps.LatLng(yactu, xactu);
                        updatemap.setCenter(new google.maps.LatLng(yactu, xactu));
                        updatemap.setZoom(16);
                        updstreet = geoStreetView(yactu, xactu, 'update_sv');
                        doStreetEvent();

                    }
                },
                error: function() {
                    $(".overlay,.loading-img").remove();

                    Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
                }
            });
        }
    }

    function doSearchBox() {
        // Create the search box and link it to the UI element.
        var input = /** @type {HTMLInputElement} */(
                document.getElementById('pac-input')
                );
        updatemap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        var searchBox = new google.maps.places.SearchBox(
                /** @type {HTMLInputElement} */(input));

        // [START region_getplaces]
        // Listen for the event fired when the user selects an item from the
        // pick list. Retrieve the matching places for that item.
        google.maps.event.addListener(searchBox, 'places_changed', function() {
            var places = searchBox.getPlaces();

            if (places.length == 0) {
                return;
            }
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }

            // For each place, get the icon, place name, and location.
            markers = [];
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0, place; place = places[i]; i++) {

                // Create a marker for each place.
                marker = new google.maps.Marker({
                    map: updatemap,
                    title: place.name,
                    position: place.geometry.location
                });

                markers.push(marker);

                bounds.extend(place.geometry.location);
            }

            updatemap.fitBounds(bounds);
        });
        // [END region_getplaces]

        // Bias the SearchBox results towards places that are within the bounds of the
        // current map's viewport.
        google.maps.event.addListener(updatemap, 'bounds_changed', function() {
            var bounds = updatemap.getBounds();
            searchBox.setBounds(bounds);
        });
    }

    function load_mdf_nodo(tipo) {

        if (tipo == 'catv')
        {
            tipo = 'rutina-catv-pais';
        } else {
            tipo = 'rutina-bas-pais';
        }

        $("#mdf_nodo").html('');
        $("#mdf_nodo").append('<option value=\"\">.:: MDF/NODO ::.</option>');

        $("#troba").html('');
        $("#troba").append('<option value=\"\">.:: TROBA ::.</option>');

        $("#amp").html('');
        $("#amp").append('<option value=\"\">.:: AMPLIFICADOR ::.</option>');

        $("#tap").html('');
        $("#tap").append('<option value=\"\">.:: AMPLIFICADOR ::.</option>');

        $.ajax({
            url: 'mdf/listar',
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: 'tipo=' + tipo,
            beforeSend: function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function(obj) {
                $(".overlay,.loading-img").remove();
                if (obj.rst == 1) {

                    $.each(obj.datos, function(id, val) {
                        $("#mdf_nodo").append(
                                "<option value=\""
                                + val.nombre
                                + "\">"
                                + val.nombre
                                + "</option>"
                                );
                    });

                    //Psi.mensaje('success', obj.msj, 6000);
                }
            },
            error: function() {
                $(".overlay,.loading-img").remove();

                Psi.mensaje('success', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    }

    function getTroba() {
        var mdf_nodo = $.trim($("#mdf_nodo").val());

        if (mdf_nodo == '')
        {
            Psi.mensaje('danger', 'Seleccione MDF/NODO', 5000);
        } else {

            $("#troba").html('');
            $("#troba").append('<option value=\"\">.:: TROBA ::.</option>');

            $("#amp").html('');
            $("#amp").append('<option value=\"\">.:: AMPLIFICADOR ::.</option>');

            $("#tap").html('');
            $("#tap").append('<option value=\"\">.:: TAP ::.</option>');
            
            var parent_fftt = 'nodo';
            if( $("#txt_chk_tipo").val() == 'stb' )
            {
                parent_fftt = 'mdf';
            }

            $.ajax({
                url: 'datos/updatexylistatroba',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: parent_fftt + '=' + mdf_nodo,
                success: function(obj) {
                    if (obj.rst == 1) {
                        $.each(obj.datos, function(id, val) {
                            $("#troba").append(
                                    "<option value=\""
                                    + val.nombre
                                    + "_" + val.coord_x + "_" + val.coord_y
                                    + "\">"
                                    + val.nombre
                                    + "</option>"
                                    );
                        });
                        
                        //Coordenadas
                        var poli_array = Psigeo.objToLatLngArray(obj.coords);
                        
                        var color = Psi.color_aleatorio();
                        
                        //Dibujar poligono
                        var poligono = Psigeo.poligono(poli_array, color, 3,  color, 0.25);
                        poligono.setMap(updatemap);
                        
                        polygons.push(poligono);
                        
                        //Limites del mapa
                        updbounds = new google.maps.LatLngBounds();
                        
                        $.each(poli_array, function(){
                            updbounds.extend(this);
                        });
                        updatemap.fitBounds(updbounds);
                        
                        //Cambiar estilos del boton
                        var estilo = $("#btn_show_polygon").attr('class');

                        if ( estilo.indexOf('btn-default') > -1 )
                        {
                            $("#btn_show_polygon").removeClass('btn-default');
                            $("#btn_show_polygon").addClass('btn-success');
                        }
                        
                    }
                }
            });
        }
    }

    function getAmplificador() {
        var mdf_nodo = $.trim($("#mdf_nodo").val());
        var troba = $.trim($("#troba").val());

        if (mdf_nodo == '')
        {
            Psi.mensaje('danger', 'Seleccione MDF/NODO', 5000);
            return false;
        }

        if (troba == '')
        {
            Psi.mensaje('danger', 'Seleccione TROBA', 5000);
            return false;
        }

        var mdfArray = mdf_nodo.split('_');
        var trobaArray = troba.split('_');

        $("#amp").html('');
        $("#amp").append('<option value=\"\">.:: AMPLIFICADOR ::.</option>');

        $("#tap").html('');
        $("#tap").append('<option value=\"\">.:: TAP ::.</option>');

        $.ajax({
            url: 'datos/updatexylistaamp',
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: 'nodo=' + mdfArray[0] + '&troba=' + trobaArray[0],
            success: function(obj) {
                if (obj.rst == 1) {
                    $.each(obj.datos, function(id, val) {
                        $("#amp").append(
                                "<option value=\""
                                + val.nombre
                                + "_" + val.coord_x + "_" + val.coord_y
                                + "\">"
                                + val.nombre
                                + "</option>"
                                );
                    });

                    //Cambio de ubicación
                    changeLocation(trobaArray[1], trobaArray[2]);
                    
                    //Poligono
                    var poli_array = Psigeo.objToLatLngArray(obj.coords);
                    var color = Psi.color_aleatorio();

                    //Dibujar poligono
                    var poligono = Psigeo.poligono(poli_array, color, 3,  color, 0.25);
                    poligono.setMap(updatemap);
                    
                    polygons.push(poligono);

                    //Limites del mapa
                    updbounds = new google.maps.LatLngBounds();
                    $.each(poli_array, function(){
                        updbounds.extend(this);
                    });
                    updatemap.fitBounds(updbounds);
                    
                    //Cambiar estilos del boton
                    var estilo = $("#btn_show_polygon").attr('class');

                    if ( estilo.indexOf('btn-default') > -1 )
                    {
                        $("#btn_show_polygon").removeClass('btn-default');
                        $("#btn_show_polygon").addClass('btn-success');
                    }
                }
            }
        });
    }

    function getTap() {
        var mdf_nodo = $.trim($("#mdf_nodo").val());
        var troba = $.trim($("#troba").val());
        var amplificador = $.trim($("#amp").val());

        if (mdf_nodo == '')
        {
            Psi.mensaje('danger', 'Seleccione MDF/NODO', 5000);
            return false;
        }

        if (troba == '')
        {
            Psi.mensaje('danger', 'Seleccione TROBA', 5000);
            return false;
        }

        if (amplificador == '')
        {
            Psi.mensaje('danger', 'Seleccione AMPLIFICADOR', 5000);
            return false;
        }

        var mdfArray = mdf_nodo.split('_');
        var trobaArray = troba.split('_');
        var ampArray = amplificador.split('_');

        $("#tap").html('');
        $("#tap").append('<option value=\"\">.:: TAP ::.</option>');

        $.ajax({
            url: 'lista/tap',
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: 'nodo='
                    + mdfArray[0]
                    + '&troba='
                    + trobaArray[0]
                    + '&amplificador='
                    + ampArray[0],
            success: function(obj) {
                if (obj.rst == 1) {
                    $.each(obj.datos, function(id, val) {
                        $("#tap").append(
                                "<option value=\""
                                + val.nombre
                                + "_" + val.coord_x + "_" + val.coord_y
                                + "\">"
                                + val.nombre
                                + "</option>"
                                );
                    });

                    //Cambio de ubicación
                    changeLocation(ampArray[1], ampArray[2]);
                }
            }
        });
    }

    function getTapCoord() {
        var tap = $.trim($("#tap").val());

        if (tap == '')
        {
            Psi.mensaje('danger', 'Seleccione TAP', 5000);
            return false;
        }

        var tapArray = tap.split('_');

        changeLocation(tapArray[1], tapArray[2]);

    }

    function changeLocation(coord_x, coord_y) {
        //Cambio de ubicación
        xactu = coord_x;
        yactu = coord_y;

        //Coordenadas de la orden a input
        $("#txt_upd_x").val(xactu);
        $("#txt_upd_y").val(yactu);

        //Reiniciar mapa y street view
        updMapProps.center = new google.maps.LatLng(yactu, xactu);
        updatemap.setCenter(new google.maps.LatLng(yactu, xactu));
        updatemap.setZoom(16);
        updstreet = geoStreetView(yactu, xactu, 'update_sv');
        doStreetEvent();
    }

    function getCableArmario() {
        var tipo_red = $.trim($("#tipo_red").val());

        if (tipo_red == '')
        {
            Psi.mensaje('danger', 'Seleccione TIPO DE RED', 5000);
            return false;
        }

        if (tipo_red == 'cable')
        {
            getCable();
        }

        if (tipo_red == 'armario')
        {
            getArmario();
        }
    }

    function getCable() {
        var mdf_nodo = $.trim($("#mdf_nodo").val());

        if (mdf_nodo == '')
        {
            Psi.mensaje('danger', 'Seleccione MDF/NODO', 5000);
        } else {

            $("#cable_armario").html('');
            $("#cable_armario").append('<option value=\"\">.:: CABLE/ARMARIO ::.</option>');

            $("#terminal").html('');
            $("#terminal").append('<option value=\"\">.:: TERMINAL ::.</option>');

            $.ajax({
                url: 'lista/cable',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: 'mdf=' + mdf_nodo,
                success: function(obj) {
                    if (obj.rst == 1) {
                        $.each(obj.datos, function(id, val) {
                            $("#cable_armario").append(
                                    "<option value=\""
                                    + val.nombre
                                    + "_" + val.coord_x + "_" + val.coord_y
                                    + "\">"
                                    + val.nombre
                                    + "</option>"
                                    );
                        });
                    }
                }
            });
        }
    }

    function getArmario() {
        var mdf_nodo = $.trim($("#mdf_nodo").val());

        if (mdf_nodo == '')
        {
            Psi.mensaje('danger', 'Seleccione MDF/NODO', 5000);
        } else {

            $("#cable_armario").html('');
            $("#cable_armario").append('<option value=\"\">.:: CABLE/ARMARIO ::.</option>');

            $("#terminal").html('');
            $("#terminal").append('<option value=\"\">.:: TERMINAL ::.</option>');

            $.ajax({
                url: 'lista/armario',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: 'mdf=' + mdf_nodo,
                success: function(obj) {
                    if (obj.rst == 1) {
                        $.each(obj.datos, function(id, val) {
                            $("#cable_armario").append(
                                    "<option value=\""
                                    + val.nombre
                                    + "_" + val.coord_x + "_" + val.coord_y
                                    + "\">"
                                    + val.nombre
                                    + "</option>"
                                    );
                        });
                    }
                }
            });
        }
    }

    function getTerminal() {
        var mdf_nodo = $.trim($("#mdf_nodo").val());
        var tipo_red = $.trim($("#tipo_red").val());
        var cable_armario = $.trim($("#cable_armario").val());

        if (mdf_nodo == '')
        {
            Psi.mensaje('danger', 'Seleccione MDF/NODO', 5000);
            return false;
        }

        if (tipo_red == '')
        {
            Psi.mensaje('danger', 'Seleccione TIPO DE RED', 5000);
            return false;
        }

        if (cable_armario == '')
        {
            Psi.mensaje('danger', 'Seleccione CABLE/ARMARIO', 5000);
            return false;
        }

        var mdfArray = mdf_nodo.split('_');
        var cable_armarioArray = cable_armario.split('_');


        $("#terminal").html('');
        $("#terminal").append('<option value=\"\">.:: TERMINAL ::.</option>');

        $.ajax({
            url: 'datos/updatexylistaterminal',
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: 'mdf=' + mdf_nodo
                    + '&' + tipo_red + '=' + cable_armarioArray[0],
            success: function(obj) {
                if (obj.rst == 1) {
                    $.each(obj.datos, function(id, val) {
                        $("#terminal").append(
                                "<option value=\""
                                + val.nombre
                                + "_" + val.coord_x + "_" + val.coord_y
                                + "\">"
                                + val.nombre
                                + "</option>"
                                );
                    });console.log(cable_armarioArray);

                    //Cambio de ubicación
                    changeLocation(
                            cable_armarioArray[1],
                            cable_armarioArray[2]
                            );
                    
                    if (tipo_red == 'armario')
                    {
                        //Poligono
                        var poli_array = Psigeo.objToLatLngArray(obj.coords);
                        var color = Psi.color_aleatorio();

                        //Dibujar poligono
                        var poligono = Psigeo.poligono(poli_array, color, 3,  color, 0.25);
                        poligono.setMap(updatemap);

                        polygons.push(poligono);

                        //Limites del mapa
                        updbounds = new google.maps.LatLngBounds();
                        $.each(poli_array, function(){
                            updbounds.extend(this);
                        });
                        updatemap.fitBounds(updbounds);

                        //Cambiar estilos del boton
                        var estilo = $("#btn_show_polygon").attr('class');

                        if ( estilo.indexOf('btn-default') > -1 )
                        {
                            $("#btn_show_polygon").removeClass('btn-default');
                            $("#btn_show_polygon").addClass('btn-success');
                        }
                    }
                    
                }
            }
        });
    }

    function getTerminalCoord() {
        var terminal = $.trim($("#terminal").val());

        if (terminal == '')
        {
            Psi.mensaje('danger', 'Seleccione TERMINAL', 5000);
            return false;
        }

        var terminalArray = terminal.split('_');

        changeLocation(terminalArray[1], terminalArray[2]);

    }

    function getBuscarCalle() {
        var calle = $.trim($("#txt_calle_nombre").val());
        var numero = $.trim($("#txt_calle_numero").val());

        if (calle == '')
        {
            Psi.mensaje('danger', 'Ingrese nombre de calle, Av., Jr.', 5000);
            return false;
        }
        
        //Limpiar mapa
        $.each(markers, function (){
            this.setMap(null);
        });
        
        $.each(polylines, function (){
            this.setMap(null);
        })
        
        markers = [];
        polylines = [];

        $.ajax({
            url: 'tramo/buscar',
            type: 'POST',
            cache: false,
            dataType: 'json',
            data: 'calle=' + calle
                    + '&numero=' + numero
                    + '&distrito=' + $("#calle_distrito").val(),
            beforeSend: function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function(obj) {
                $(".overlay,.loading-img").remove();
                if (obj.rst == 1) {
                    addressToMap(
                            obj.datos,
                            $("#calle_distrito option:selected").text(),
                            $("#txt_calle_nombre").val(),
                            $("#txt_calle_numero").val(),
                            false
                            );
                }
            },
            error: function() {
                $(".overlay,.loading-img").remove();

                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });

    }

    function addressToMap(data, coddis, calle, ndir, masivo) {

        var neach = 1;
        var newCoordIni;
        var newCoordEnd;
        var xDir;
        var yDir;
        var pt;
        var addressData = [];
        var marker;
        var markTextColor;
        var N = Number(ndir);
        var bounds = new google.maps.LatLngBounds();
        var zIndexItem = 1;

        $.each(data, function() {
            polyLineCoords = [];

            //Coordenadas inicio y fin
            newCoordIni = new google.maps.LatLng(this.YA, this.XA);
            newCoordEnd = new google.maps.LatLng(this.YB, this.XB);

            //Punto indicador de direccion
            xDir = Number(this.XB) + ((-Number(this.NUM_IZQ_FI) + N) * (-Number(this.XB) + Number(this.XA)) / (-Number(this.NUM_IZQ_FI) + Number(this.NUM_DER_IN)));
            yDir = Number(this.YB) + ((-Number(this.NUM_IZQ_FI) + N) * (-Number(this.YB) + Number(this.YA)) / (-Number(this.NUM_IZQ_FI) + Number(this.NUM_DER_IN)));


            pt = new google.maps.LatLng(this.YA, this.XA);
            bounds.extend(pt);
            pt = new google.maps.LatLng(this.YB, this.XB);
            bounds.extend(pt);

            //Marcador solo si el numero es valido
            if (N > 0)
            {
                addressData = [];
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(yDir, xDir),
                    map: updatemap,
                    animation: google.maps.Animation.DROP
                });
                markers.push(marker);

                if (coddis === '')
                {
                    coddis = this.COD_DPTO + this.COD_PROV + this.COD_DIST;
                }
                if (calle === '')
                {
                    calle = this.NOM_VIA_TR;
                }

                addressData.push(coddis);
                addressData.push(calle);
                addressData.push(N);
                addressData.push(xDir);
                addressData.push(yDir);
                google.maps.event.addListener(marker, "click", function(event) {
                    addressInfoWindow(addressData, event);
                });
            }

            //Orden visibilidad
            zIndexItem++;

            //Mostrar o no Inicio y Fin del tramo
            if ($("#showPathMarker").prop("checked") === true)
            {
                //Marcadores inicio y fin de recta
                markTextColor = textByColor("#"
                        + $("#pickerAddressLine")
                        .val())
                        .substring(1);

                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(this.YA, this.XA),
                    map: updatemap,
                    icon: googleMarktxt
                            + "I|"
                            + $("#pickerAddressLine").val()
                            + "|"
                            + markTextColor,
                    animation: google.maps.Animation.DROP,
                    zIndex: zIndexItem
                });
                markers.push(marker);

                google.maps.event.addListener(marker, "click", function(event) {
                    markerPathInfoWindow(event);
                });

                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(this.YB, this.XB),
                    map: updatemap,
                    icon: googleMarktxt
                            + "F|"
                            + $("#pickerAddressLine").val()
                            + "|"
                            + markTextColor,
                    animation: google.maps.Animation.DROP,
                    zIndex: zIndexItem
                });
                markers.push(marker);

                google.maps.event.addListener(marker, "click", function(event) {
                    markerPathInfoWindow(event);
                });
            }


            //Creando path
            polyLineCoords.push(newCoordIni);
            polyLineCoords.push(newCoordEnd);

            var addressPath = new google.maps.Polyline({
                path: polyLineCoords,
                geodesic: true,
                strokeColor: '#0000FF',
                strokeOpacity: 1.0,
                strokeWeight: 4,
                zIndex: zIndexItem
            });
            polylines.push(addressPath);

            var lineData = this;

            //Infowindow del tramo
            google.maps.event.addListener(addressPath, "click", function(event) {
                lineInfoWindow(lineData, event);
            });

            addressPath.setMap(updatemap);             

            neach++;
        });

        updatemap.fitBounds(bounds);

    }

    function lineInfoWindow(polyLine, event) {
        var content = "";

        content += '<table>';
        content += '<tr><td>COD DEP</td><td>' + polyLine.COD_DPTO + '</td></tr>';
        content += '<tr><td>COD PRO</td><td>' + polyLine.COD_PROV + '</td></tr>';
        content += '<tr><td>COD DIS</td><td>' + polyLine.COD_DIST + '</td></tr>';
        content += '<tr><td>NOM VIA</td><td>' + polyLine.NOM_VIA_TR + '</td></tr>';
        content += '<tr><td>NUM DER IN</td><td>' + polyLine.NUM_DER_IN + '</td></tr>';
        content += '<tr><td>NUM DER FI</td><td>' + polyLine.NUM_DER_FI + '</td></tr>';
        content += '<tr><td>NUM IZQ IN</td><td>' + polyLine.NUM_IZQ_IN + '</td></tr>';
        content += '<tr><td>NUM IZQ FI</td><td>' + polyLine.NUM_IZQ_FI + '</td></tr>';
        content += '<tr><td>XA</td><td>' + polyLine.XA + '</td></tr>';
        content += '<tr><td>YA</td><td>' + polyLine.YA + '</td></tr>';
        content += '<tr><td>XB</td><td>' + polyLine.XB + '</td></tr>';
        content += '<tr><td>YB</td><td>' + polyLine.YB + '</td></tr>';
        content += '</table>';

        infowindow.setPosition(event.latLng);
        infowindow.setContent(content);
        infowindow.open(self.map);
    }

    function addressInfoWindow(data, event) {
        var content = "";
        var href = '<a href="http://maps.google.com?q='
                + data[4]
                + ','
                + data[3]
                + '" target="_blank">Go</a>';

        content += '<table>';
        content += '<tr><td>Distrito</td><td>' + data[0] + '</td></tr>';
        content += '<tr><td>Calle</td><td>' + data[1] + '</td></tr>';
        content += '<tr><td>Numero</td><td>' + data[2] + '</td></tr>';
        content += '<tr><td>X</td><td>' + data[3] + '</td></tr>';
        content += '<tr><td>Y</td><td>' + data[4] + '</td></tr>';
        content += '<tr><td>Ubicacion</td><td>' + href + '</td></tr>';
        content += '</table>';

        infowindow.setPosition(event.latLng);
        infowindow.setContent(content);
        infowindow.open(self.map);
    }

    function markerPathInfoWindow(event) {
        var content = "";
        var href = '<a href="http://maps.google.com?q='
                + event.latLng.lat()
                + ','
                + event.latLng.lng()
                + '" target="_blank">Go</a>';

        content += '<table>';
        content += '<tr><td>X</td><td>' + event.latLng.lng() + '</td></tr>';
        content += '<tr><td>Y</td><td>' + event.latLng.lat() + '</td></tr>';
        content += '<tr><td>Ubicacion</td><td>' + href + '</td></tr>';
        content += '</table>';

        infowindow.setPosition(event.latLng);
        infowindow.setContent(content);
        infowindow.open(self.map);
    }
    
    /**
     * Limpia objetos del mapa
     * 
     * @returns 
     */
    function limpiarUbicacion(){
        
        $.each(markers, function(){
            this.setMap(null);
        });
        
        $.each(polylines, function(){
            this.setMap(null);
        });
        
        $.each(polygons, function(){
            this.setMap(null);
        });
        
        markers = [];
        polylines = [];
        polygons = [];
        
        //Cambiar estilos del boton
        var estilo = $("#btn_show_polygon").attr('class');

        if ( estilo.indexOf('btn-success') > -1 )
        {
            $("#btn_show_polygon").removeClass('btn-success');
            $("#btn_show_polygon").addClass('btn-default');
        }
    }

    /**
     * Muestra/Oculta poligonos
     * 
     * @param Bool show_hide: indica si muestra o no los poligonos
     * @returns {undefined}
     */
    function upd_show_polygon()
    {
        if (polygons.length > 0)
        {
            $.each(polygons, function(){
                if (this.map == null)
                {
                    //Mostrar en mapa
                    this.setMap(updatemap);
                } else {
                    //Ocultar en mapa
                    this.setMap(null);
                }

            });
            
            //Cambiar estilos del boton
            var estilo = $("#btn_show_polygon").attr('class');

            if ( estilo.indexOf('btn-default') > -1 )
            {
                $("#btn_show_polygon").removeClass('btn-default');
                $("#btn_show_polygon").addClass('btn-success');
            } else {
                $("#btn_show_polygon").removeClass('btn-success');
                $("#btn_show_polygon").addClass('btn-default');
            }
        }
    }
</script>