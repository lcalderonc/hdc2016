<script type="text/javascript">
    activar = function (id) {
        Errores.CambiarEstadoErrores(id, 1);
    };

    desactivar = function (id) {
        Errores.CambiarEstadoErrores(id, 0);
    };

    $(document).ready(function () {
        Errores.CargarErrores();
        $("#t_errores").dataTable();
    });
</script>