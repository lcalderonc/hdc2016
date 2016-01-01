<script type="text/javascript">

$(document).ready(function() {

    //$("#bandejaModal").attr("onkeyup","return enterGlobal(event,'btn_gestion_modal')");

    $('#toaModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // captura al boton
        var modal = $(this); //captura el modal

        $('#form_toa [data-toggle="tooltip"]').css("display","none");

        Bandeja.CargarBandeja('M',CargandoToa,variables); //variables viene de Bandeja Modal
    });

    $('#toaModal').on('hide.bs.modal', function (event) {
        var modal = $(this); //captura el modal

    });


});

CargandoToa=function(obj){
    $("#txt_codactu_t_modal").val(obj[0].codactu);
    $("#txt_quiebre_t_modal").val(obj[0].quiebre);
    $("#txt_actividad_t_modal").val(obj[0].actividad);
    $("#txt_empresa_t_modal").val(obj[0].empresa);
    alert(".::Abriendo Toa Modal::.");
}


</script>
