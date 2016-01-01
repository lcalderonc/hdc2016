<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        {{ HTML::script('lib/jquery-2.1.3.min.js') }}
        {{ HTML::script('http://maps.google.com/maps/api/js?sensor=false&libraries=drawing') }}
        {{ HTML::script('js/geo/geo.functions.js') }}
        {{ HTML::script('js/utils.js') }}
        
        <script>
            /**
     * Muestra infowindow para un marcador
     * 
     * @param {type} thisMap Mapa donde se mostrar marcador
     * @param {type} element Marcador (marker)
     * @param {type} content Contenido (html) del infowindow
     */
    function doInfoWindow(thisMap, element, content) {
        google.maps.event.addListener(element, 'click', (
                function(marker, infocontent, infowindow) {
                    return function() {
                        infowindow.setContent(infocontent);
                        infowindow.open(thisMap, marker);
                    };
                })(element, content, infowindow));
    }
            $(document).ready(function() {
                var mapTools = {};
                var bounds = new google.maps.LatLngBounds();
                                
                //Iniciar mapa
                objMap = doObjMap("ordentecnico", objMapProps, mapTools);
                
                var agendaIcon = "http://psiweb.ddns.net:7020/img/icons/visorgps/cal_0233c4.png";
                <?php
                foreach ($data as $key=>$val) {
                    ?>
                        var infocontent = "";
                        bounds.extend(new google.maps.LatLng(<?php echo $val['y'];?>, <?php echo $val['x'];?>));
                        marker = new google.maps.Marker({
                            position: new google.maps.LatLng(<?php echo $val['y'];?>, <?php echo $val['x'];?>),
                            map: objMap,
                            icon: agendaIcon,
                            zIndex: zIndex++,
                            codactu: '<?php echo $val['codactu'];?>',
                            tipoactu: '<?php echo $val['tipoactu'];?>',
                            actividad: '<?php echo $val['actividad'];?>',
                            idgestion: <?php echo $val['id'];?>,
                            estado: '<?php echo $val['estado'];?>',
                            coordinado: '<?php echo $val['coordinado'];?>',
                            fecha_agenda: '<?php echo $val['fecha_agenda'];?>'
                        });
                        
                        infocontent += "<div class=\"infow\" style=\"overflow: auto; text-align: left\"\">" +
                            "<div class=\"detalle_actu\">" +
                            '<?php echo $val['tipoactu'];?>' + "<br>" +
                            '<?php echo $val['nombre_cliente_critico'];?>' + "<br>" +
                            '<?php echo $val['fh_agenda'];?>' + " / " +
                            '<?php echo $val['direccion_instalacion'];?>' + "<br>" +
                            '<?php echo $val['codactu'];?>' + "<br>" +
                            '<?php echo $val['fftt'];?>' + "<br>" +
                            '<?php echo $val['inscripcion'];?>' + "<br>" +
                            '<?php echo $val['id_atc'];?>' + "<br>" +
                            '<?php echo $val['quiebre'];?>' + "<br>" +
                            "</div>";
                    
                        doInfoWindow(objMap, marker, infocontent);
                    <?php
                }
                ?>
                objMap.fitBounds(bounds);
            });
        </script>

        <style>
            body, html {
                margin: 0px;
            }
            #ordentecnico {
                width: 100%;
                height: 600px;
            }
        </style>
    </head>
    <body>
        <h3>Orden / Ruta</h3>
        <div id="ordentecnico"></div>
    </body>
</html>
