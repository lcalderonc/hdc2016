<script type="text/javascript">
    var Geoplan = {
        SavePlan: function(datos) {
            $.ajax({
                url: "geoplan/saveplan",
                type: "POST",
                cache: false,
                data: "savePlanAll=" + datos,
                dataType: 'json',
                beforeSend: function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                error: function(data) {
                    $(".overlay,.loading-img").remove();
                    $("#msj").html('<div class="alert alert-dismissable alert-danger">' +
                            '<i class="fa fa-ban"></i>' +
                            '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
                            '<b><?php echo trans("greetings.mensaje_error"); ?></b>' +
                            '</div>');
                },
                success: function(data) {
                    $(".overlay,.loading-img").remove();
                    $.each(data, function(tipoactu, obj) {
                        $.each(obj, function(codactu, prop) {
                            //Respuesta de webpsi
                            if (prop.rst === 1) {
                                $("#psi_" + codactu).attr("src", "img/icons/circle-check-24.png");

                                //Ocultar marcadores del mapa
                                $.each(polyActu, function(index, element) {
                                    if (element.codactu === codactu)
                                    {
                                        element.setMap(null);
                                    }
                                });
                            } else {
                                $("#psi_" + codactu).attr("src", "img/icons/circle-cross-24.png");
                            }
                            //Respuesta de officetrack
                            if (prop.officetrack === "OK") {
                                $("#ot_" + codactu).attr("src", "img/icons/circle-check-24.png");
                            } else {
                                $("#ot_" + codactu).attr("src", "img/icons/circle-cross-24.png");
                            }
                        });
                    });
                }
            });
        },
        HorarioGeoPlan: function() {

            $.ajax({
                url: 'geoplan/horariogeoplan',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: 'quiebre='
                        + $("#slct_quiebre").val()
                        + '&zonal='
                        + $("#slct_zonal").val()
                        + '&empresa='
                        + $("#slct_empresa").val(),
                beforeSend: function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function(obj) {
                    $(".overlay,.loading-img").remove();
                    horarioGeoPlan = obj;
                },
                error: function() {
                    $(".overlay,.loading-img").remove();
                    $("#msj").html('<div class="alert alert-dismissable alert-danger">' +
                            '<i class="fa fa-ban"></i>' +
                            '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
                            '<b><?php echo trans("greetings.mensaje_error"); ?></b>' +
                            '</div>');
                }
            });
        },
        PanelCelulaTecnico: function() {

            bounds = new google.maps.LatLngBounds();

            var data = $("form#form_visorgps").serialize().split("txt_").join("").split("slct_").join("");

            $.ajax({
                url: 'visorgps/panelcelulatecnico',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: data,
                beforeSend: function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function(obj) {

                    //Lista de tecnicos
                    doTecList(obj.tecnicos, obj.icons);

                    //Lista agendas tecnicos
                    doTecAgenda(obj.data, obj.icons);
                    
                    //Horarios
                    horarioGeoPlan = obj.horario;

                    $(".overlay,.loading-img").remove();
                    $('#divresultadopla').html('<label>Resultado de busqueda:   '+obj.tecnicos.length+' Técnicos / '+obj.data.length+' Objetos Mapeados</label>');
                },
                error: function() {
                    $(".overlay,.loading-img").remove();
                    $("#msj").html('<div class="alert alert-dismissable alert-danger">' +
                            '<i class="fa fa-ban"></i>' +
                            '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
                            '<b><?php echo trans("greetings.mensaje_error"); ?></b>' +
                            '</div>');
                }
            });
        },

        trazarPolygon: function(map) {

            var data = $("form#form_visorgps").serialize().split("txt_").join("").split("slct_").join("");            

            $.ajax({
                url: 'mdf/filtrarcoord',
                type: 'POST',
                cache: false,
                dataType: 'json',
                data: data,
                beforeSend: function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function(obj) {
                    
                    if(obj.rst==1){
                      var colores='#0000FF';
                      $.each(obj.seleccionado, function(idx, tipo) {
                          var triangleCoords=[];                         
                          $.each(obj.datos, function(index, element) {                         
                                 if(element.id==tipo) {
                                    triangleCoords.push({"lat": parseFloat(element.lat), "lng": parseFloat(element.lng)});
                                 }
                          });
                          var bermudaTriangle = new google.maps.Polygon({
                                         paths: triangleCoords,
                                         strokeColor: colores,
                                         strokeOpacity: 0.8,
                                         strokeWeight: 3,
                                         fillColor: colores,
                                         fillOpacity: 0.35
                                     });
                                     bermudaTriangle.setMap(map);
                         
                                     bermudaTriangle.addListener('click', showArrays);  

                      });

                        
                    }

                    $(".overlay,.loading-img").remove();
                    
                },
                error: function() {
                    $(".overlay,.loading-img").remove();
                    $("#msj").html('<div class="alert alert-dismissable alert-danger">' +
                            '<i class="fa fa-ban"></i>' +
                            '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>' +
                            '<b><?php echo trans("greetings.mensaje_error"); ?></b>' +
                            '</div>');
                }
            });
        }
        
    }
</script>