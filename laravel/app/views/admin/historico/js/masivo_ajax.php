<script type="text/javascript">
var Masivos={
    cargarQuiebres:function(){
        $.ajax({
            url         : 'quiebre/cargarofficetrack',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            success : function(obj) {
                if(obj.rst==1){
                    $('#slct_quiebre').html('');
                    $.each(obj.datos,function(index,data){
                        $('#slct_quiebre').append('<option apocope="'+data.apocope+'" value="'+data.id+'">'+data.nombre+'</option>');
                    });
                    $('#slct_quiebre').append("<option selected style='display:none;'>--- Elige Quiebre ---</option>");
                    
                } 
            }
        });
    },
    cargarFile:function(data){
        $.ajax({
            url: "upload/actualizaquiebremasivo",
            type: 'POST',
            data: data,
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            success: function(datos) {
                if(datos.estado=='1' || datos.estado=='0'){
                    alert(datos.msj);
                    //generar archivo text datos.txt
                    
                    descargarArchivo(generarTxt(datos.txt), 'archivo.txt');
                }
                else{
                    alert('Ocurrio un error en la carga');
                }

            },
            error: function(datos) {
                alert('Ocurrio un error en la carga');
            }
        });
    },
    actualizaQuiebreIndividual:function(data){
        $.ajax({
            url: "upload/actualizaquiebreindividual",
            type: 'POST',
            data: data,
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            success: function(datos) {
                if(datos.estado=='1' || datos.estado=='0'){
                    alert(datos.msj);
                }
                else{
                    alert('Ocurrio un error en la carga');
                }

            },
            error: function(datos) {
                alert('Ocurrio un error en la carga');
            }
        });
    }
};
</script>