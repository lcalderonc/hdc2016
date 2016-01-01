<script type="text/javascript">
var map;
var markers = [];
var cliente_x;
var cliente_y;
var cliente_xy_insert = 0;

$(document).ready(function() {

    $('#officetrackModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var modal = $(this); //captura el modal

        variables={ task_id:button.data('id') };
        //tareas
        Tarea.show(variables,"officetrack_tareas");
    });

    $('#officetrackModal').on('hide.bs.modal', function (event) {
      var modal = $(this); //captura el modal

    });

});


</script>
