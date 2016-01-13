<script type="text/javascript">
    activar = function (id) {
        Errores.CambiarEstadoErrores(id, 1);
    };

    desactivar = function (id) {
        Errores.CambiarEstadoErrores(id, 0);
    };

    $(document).ready(function () {
        $('#fecha_agenda').daterangepicker({
            format: 'YYYY-MM-DD'
        });

        $("#mostrar").click(mostrarErrores);
        $("#t_errores").dataTable();
    });
    var dataId;
    var dataEstado;
    mostrarErrores = function () {
        if ($.trim($("#fecha_agenda").val()) === '') {
            alert('Seleccione Rango de Fecha');
        } else {
            Errores.BuscarErrores();
            // Dar accion al clickear Button (Update Estado)
            $('#t_errores tbody').on('click', 'span', function () {
                var table = $('#t_errores').DataTable();
                var data = table.row($(this).parents('tr')).data();
                var comentario =  data['comentario'];
                dataId = data['id'];
                dataEstado = (data['estado'] == 0 ? 1 : 0);

                $('#errorComentarioModal').on('show.bs.modal', function () {
                    $('#txt_comentario').val(comentario);
                    if (dataEstado == 0) {
                        $('#btnErrorDiligencia').css('display', 'none');
                    } else {
                        $('#btnErrorDiligencia').css('display', 'inline-block');
                    }
                    // var modal = $(this); modal.find('.modal-footer button').text(' Actividad');
                });
                $('#errorComentarioModal').on('shown.bs.modal', function () {
                    $('#txt_comentario').focus();
                    //boton.removeAttr('disabled');
                });
//                if (data['estado'] == 0) {
//                    Errores.CambiarEstadoErrores(data['id'], 1);
//                } else {
//                    Errores.CambiarEstadoErrores(data['id'], 0);
//                }
            });

            $('#btnErrorComentario').on('click', function () {
                Errores.CambiarEstadoErrores(dataId, 0); // dataEstado
            });

            $('#btnErrorDiligencia').on('click', function () {
                Errores.CambiarEstadoErrores(dataId, 1);
            });
        }
    };
</script>