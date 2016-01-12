<script type="text/javascript">
    var Errores = 
    {                
        BuscarErrores: (function () {
            var datos="";
            var columnDefs = [
                {
                    "targets": 0,
                    "data": "id",
                    "name": "id",
                    "visible": false
                },
                {
                    "targets": 1,
                    "data": "nombre",
                    "name": "nombre"
                },
                {
                    "targets": 2,
                    "data": "code",
                    "name": "code"
                },
                {
                    "targets": 3,
                    "data": "file",
                    "name": "file"
                },
                {
                    "targets": 4,
                    "data": "date",
                    "name": "date",
                    "orderable": false
                },
                {
                    "targets": 5,
                    "data": "comentario",
                    "name": "comentario",
                    "visible": false
                },
                {
                    "targets": 6,
                    "data": "estado",
                    "name": "estado",
                    "orderable": false,
                    "render": function(data, type, full) { // Devuelve el contenido personalizado
                        if(data == 0){
                            return '<span class="btn btn-success" data-toggle="modal" data-target="#errorComentarioModal"> Reparado</span>';
                        } else {
                            return '<span class="btn btn-danger" data-toggle="modal" data-target="#errorComentarioModal"> Diligencia</span>';
                        }
                    }
                },
                {
                    "targets": 7,
                    "data": "id",
                    "name": "id",
                    "orderable": false,
                    "render": function(data, type, full) { // Devuelve el contenido personalizado
                        return '<button class="btn btn-primary" data-toggle="modal" data-target="#myModal" data-whatever="Detalle" onclick="Errores.MostrarDetalleError(' + data + ');"><i class="fa fa-eye"></i></button>';
                    }
                }
            ];
            
            $('#t_errores').dataTable().fnDestroy();
            var dt = $('#t_errores')
                .on('page.dt', function () {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                })
                .on('search.dt', function () {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                })
                .on('order.dt', function () {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                })
                .DataTable({
                    "processing": true,
                    "serverSide": true,
                    "stateSave": true,
                    "stateLoadCallback": function (settings) {
                        $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                    },
                    "stateSaveCallback": function (settings) {
                        $(".overlay,.loading-img").remove();
                    },
                    "ajax": {
                        url: "errores/buscar",
                        type: "POST",
                        cache: false,
                        "data": function(d){
                            datos=$("#form_errores").serialize().split("txt_").join("").split("slct_").join("").split("%5B%5D").join("[]").split("+").join(" ").split("%7C").join("|").split("&");

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
                    columnDefs
                });
                return dt;
        }),
        
        CambiarEstadoErrores: (function (id, estado) {
            $.ajax({
                type: 'POST',
                url: "errores/cambiarestado",
                cache: false,
                data: "id=" + id + "&estado=" + estado + "&comentario=" + $('#txt_comentario').val() ,
                beforeSend : function() {
                    $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                },
                success: function () {
                    $(".overlay,.loading-img").remove();
                    Errores.BuscarErrores();
                }
            });
        }),
            
        MostrarDetalleError: (function (id) {
            $('#myModal').on('show.bs.modal', function () {
                $("#myModal").find('.modal-body textarea').val("");
            });       
            $.ajax({
                type: 'POST',
                url: "errores/detalle",
                data: "id=" + id,
                dataType: 'json',
                cache: false,
                success: function (obj) {
                    $.each(obj.data, function (index, error){
                        $("#myModal").find('.modal-title').text("Detalle Mensaje");
                        $("#myModal").find('.modal-body textarea').val(error.message);
                    });
                }
            });
        })
           
    };
</script>