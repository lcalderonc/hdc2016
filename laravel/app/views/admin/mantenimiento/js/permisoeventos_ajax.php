<script type="text/javascript">
var PermisoeventoObj, Permisoevento_id;
var Permisoeventos={
    Cargar:function(tipo_persona){
        $.ajax({
            url         : 'permisoeventos/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        :{tipo_persona:tipo_persona},
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                var html="";
                var estadohtml="";
                if(obj.rst==1){
                    HTMLCargarPersona(obj.datos);
                    PermisoeventoObj=obj.datos;
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
            }
        });
    },
    EditarPermisoEventos:function(){

        var datos=$("#form_permisoeventos").serialize().split("txt_").join("").split("slct_").join("");

        $.ajax({
            url         : 'permisoeventos/editar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    $('#t_personas').dataTable().fnDestroy();

                    var tipo_persona = $('#slct_tipo_persona').val();
                    if (isNaN(tipo_persona) || tipo_persona==='') {
                    Psi.mensaje('danger', 'Seleccione Tipo persona', 6000);
                    } else {
                    Permisoeventos.Cargar(tipo_persona);
                    }

                    $('#permisoeventosModal .modal-footer [data-dismiss="modal"]').click();
                    Psi.mensaje('success', obj.msj, 6000);
                }
                else{ 
                    $.each(obj.msj,function(index,datos){
                        $("#error_"+index).attr("data-original-title",datos);
                        $('#error_'+index).css('display','');
                    });
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    }
};
</script>
