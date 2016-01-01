<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">

        @section('autor')
        <meta name="author" content="Jorge Salcedo (Shevchenko)">
        @show

        <link rel="shortcut icon" href="favicon.ico">

        @section('descripcion')
        <meta name="description" content="">
        @show
        <title>
            @section('titulo')
                PSI 2.0
            @show
        </title>

        @section('includes')
            <?php echo HTML::style('lib/bootstrap-3.3.1/css/bootstrap.min.css'); ?>
            <?php echo HTML::style('lib/font-awesome-4.2.0/css/font-awesome.min.css'); ?>

            {{ HTML::script('lib/jquery-2.1.3.min.js') }}
            {{ HTML::script('lib/jquery-ui-1.11.2/jquery-ui.min.js') }}
            {{ HTML::script('lib/bootstrap-3.3.1/js/bootstrap.min.js') }}
            <!-- //code.ionicframework.com/ionicons/1.5.2/css/ionicons.min.css -->
            <?php echo HTML::style('css/master/ionicons.min.css'); ?>
            <?php echo HTML::style('lib/datatables-1.10.4/media/css/dataTables.bootstrap.css'); ?>
            <?php echo HTML::style('css/admin/admin.css'); ?>
            <?php echo HTML::style('css/admin/horarios.css'); ?>

            {{ HTML::script('lib/datatables-1.10.4/media/js/jquery.dataTables.js') }}
            {{ HTML::script('lib/datatables-1.10.4/media/js/dataTables.bootstrap.js') }}
            {{ HTML::script('js/psi.js') }}
            @include( 'admin.js.app' )
        @show
    </head>

    <body class="skin-blue">
    <div id="msj" class="msjAlert"> </div>
        @include( 'layouts.admin_head' )

        <div class="wrapper row-offcanvas row-offcanvas-left">
            @include( 'layouts.admin_left' )

            <aside class="right-side">
            @yield('contenido')
            </aside><!-- /.right-side -->

        </div><!-- ./wrapper -->

       @yield('formulario')
    </body>

<?php 
    echo '<script type="text/javascript">';
    echo "var envioValida=0;
            var agregarG='';
            var editarG='';
            var eliminarG='';";
    echo     "$('ul.sidebar-menu li').each(function(indice, elemento) {
                htm=$(elemento).html();
                if(htm.split('<a href=".'"'.$valida_ruta_url.'"'."').length>1){
                    $(elemento).addClass('active');
                    poshtm=$(elemento).find('a[href=".'"'.$valida_ruta_url.'"'."]');
                    var accesosG=poshtm.attr('data-accesos');
                    var hash=poshtm.attr('data-clave');
                    agregarG=accesosG.substr(0,1);
                    editarG=accesosG.substr(1,1);
                    eliminarG=accesosG.substr(2,1);

                    var datos={agregarG:agregarG,editarG:editarG,eliminarG:eliminarG,hash:hash};
                    
                    if(envioValida==0){
                        $.ajax({
                            url         : 'usuario/validaacceso',
                            type        : 'POST',
                            cache       : false,
                            dataType    : 'json',
                            async        : false,
                            data        : datos,
                            beforeSend : function() {                 
                            },
                            success : function(obj) {
                                envioValida++;
                                if(obj.rst!='1'){
                                    alert('Ud no cuenta con permisos para acceder; Ingrese nuevamente');
                                    window.location='salir';
                                }
                            },
                            error: function(){
                            }
                        });

                    }
                }
            });

            ";
    echo '</script>';

    
    //$hash= hash('256', Config::get('wpsi.permisos.key').);
?>
</html>