<script type="text/javascript">
    var Errores = {
        CargarErrores: (function () {
                $.ajax({
                    type: 'POST',
                    url: "errores/listar",
                    cache: false,
                    dataType: 'json',
                    beforeSend : function() {
                        $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
                    },
                    success: function (obj) {
                        $(".overlay,.loading-img").remove();
                        var html = "";
                        var estadoHtml = "";
                        $.each(obj.data, function (index, errores) {
                            estadoHtml = '<span id="' + errores.id + '" onClick="activar(' + errores.id + ')" class="btn btn-success">Reparado</span>';
                            if (errores.estado == 1){
                            estadoHtml = '<span id="' + errores.id + '" onClick="desactivar(' + errores.id + ')" class="btn btn-danger">Diligencia</span>';
                            }

                            html += "<tr>" +
                                    "<td>" + errores.nombre + "</td>" +
                                    "<td>" + errores.code + "</td>" +
                                    "<td>" + "Linea: " + errores.line+ " : " + errores.file + "</td>" +
                                    "<td>" + errores.message + "</td>" +
                                    "<td>" + errores.date + "</td>" +
                                    "<td>" + estadoHtml + "</td>";
                            html += "</tr>";
                        })

                        $("#t_errores").dataTable().fnDestroy();
                        $("#tb_errores").html(html);
                        $("#t_errores").dataTable();
                    }
                });
                }),
        
        CambiarEstadoErrores: (function (id, estado) {
                $.ajax({
                    type: 'POST',
                    url: "errores/cambiarestado",
                    cache: false,
                    data: "id=" + id + "&estado=" + estado,
                    success: function () {
                        Errores.CargarErrores();
                    }
                });
            }),
        };
</script>