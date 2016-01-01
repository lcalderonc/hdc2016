<script type="text/javascript">
var EstadosObj, estadodig_id;
var Estados={
    uploadFile:function(data){
        $.ajax({
            url: "reporte/estadodigitalizacion",
            type        : "POST",
            data        : data,
            cache       : false,
            dataType    : 'json',
            processData : false,
            contentType : false,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function (obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    Psi.mensaje('success', obj.msj, 6000);
                    //pintar tabla
                    //HTMLCargarEstados(obj.datos);
                } else {
                    Psi.mensaje('danger', obj.msj, 6000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'ocurrio un error en la carga>', 6000);
            }
        });
    },
    cargar:function(proyecto){
        $.ajax({
            url: "reporte/estadodigitalizacioncargar",
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {proyecto:proyecto},
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function (obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    //Psi.mensaje('success', obj.msj, 6000);
                    //pintar tabla
                    EstadosObj=obj.datos;
                    HTMLCargarEstados(obj.datos);
                } else {
                    Psi.mensaje('danger', obj.msj, 6000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'ocurrio un error en la carga>', 6000);
            }
        });
    },
    CargarGestiones:function(estado_id){
        $.ajax({
            url: "reporte/estadosdigitalizaciongestiones",
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : {estado_id:estado_id},
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function (obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    //Psi.mensaje('success', obj.msj, 6000);
                    //pintar tabla
                    //EstadosObj=obj.datos;
                    HTMLCargarGestiones(obj.datos);
                } else {
                    Psi.mensaje('danger', obj.msj, 6000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'ocurrio un error en la carga>', 6000);
            }
        });
    },
    Crear:function(){
        var datos=$("#form_digitalizacion").serialize().split("txt_").join("").split("slct_").join("");
        
        $.ajax({
            url: "reporte/estadosdigitalizacioncrear",
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : datos,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function (obj) {
                $(".overlay,.loading-img").remove();
                if(obj.rst==1){
                    Psi.mensaje('success', obj.msj, 6000);
                    var proyecto = $('#slct_proyecto2').val();
                    Estados.cargar(proyecto);
                    $('#estadosDigitalizacionModal .modal-footer [data-dismiss="modal"]').click();
                } else {
                    Psi.mensaje('danger', obj.msj, 6000);
                }
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'ocurrio un error en la carga>', 6000);
            }
        });
    }
};
</script>
