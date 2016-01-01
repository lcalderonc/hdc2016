<script type="text/javascript">
var File={
    ConsultarQuiebre:function(form){
        $.ajax({
            url: "upload/consultarquiebre",
            type: 'POST',
            data: form,
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function(respAjax) {
                $(".overlay,.loading-img").remove();
                if(respAjax){
                    var strDatos = JSON.stringify(datos);
                    var apellidos = (respAjax.apellidos)? respAjax.apellidos:'';
                    var nombre = (respAjax.nombre)? respAjax.nombre:'';
                    var tipocalle = (respAjax.tipocalle)? respAjax.tipocalle:'';
                    var nomcalle = (respAjax.nomcalle)? respAjax.nomcalle:'';
                    var numcalle = (respAjax.numcalle)? respAjax.numcalle:'';
                        
                    HTML = "<table class='table'><thead><tr>";
                    HTML +="<th> Apellidos y nombres </th>";
                    if (servicio=='provision')   HTML +="<th>direccion </th>";
                    HTML +=HTMLcabecera;
                    HTML +="</tr></thead>";
                    HTML +="<tbody><tr>";
                    HTML +="<td style='display: none' id='data' data='"+strDatos+"'></td>";
                    HTML +="<td class='active'>";
                    HTML +="<label>Ap.</label><input id='apellidos' class='form-control' value='"+apellidos+"'><br>";
                    HTML +="<label>Nom.</label><input id='nombre' class='form-control' value='"+nombre+"'>";
                    HTML +="</td>";
                    
                    if (servicio=='provision') {
                        HTML +="<td class='active'>";
                        HTML +="<label>Vía</label><input id='tipocalle' class='form-control' value='"+ tipocalle +"'>";
                        HTML +="<label>Nom. </label><input id='nomcalle' class='form-control' value='"+ nomcalle +"'><br>";
                        HTML +="<label>N°</label><input id='numcalle' class='form-control' value='"+ numcalle +"'>";
                        HTML +="<label>Piso</label><input id='piso' class='form-control'>";
                        HTML +="<label>Int.</label><input id='interior' class='form-control'>";
                        HTML +="<label>Mzn.</label><input id='manzana' class='form-control'>";
                        HTML +="<label>Lote</label><input id='lote' class='form-control'>";
                        HTML +="</td>";
                    }
                    HTML +=HTMLregistro;
                    HTML +="</tr></tbody></table>";
                    //output.innerHTML = HTML;
                    $("#fileOutput").html(HTML);
                    $("#actividad").html("<p class='text-center text-uppercase bg-danger' >"+servicio+"</p>");

                }
                else{
                    alert('ocurrio un error en la carga');
                }
            },
            error: function(respAjax) {
                $(".overlay,.loading-img").remove();
                alert('ocurrio un error en la carga');
            }
        });
    },
    cargar:function(form, url){
        $.ajax({
            url: url,
            type: 'POST',
            data: form,
            contentType: false,
            processData: false,
            cache: false,
            dataType: "json",
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success: function(respAjax) {
                $(".overlay,.loading-img").remove();
                if(respAjax.estado){
                    $("#fileOutput").html('');
                    Psi.mensaje('success', respAjax.msj, 6000);
                }
                else{
                    Psi.mensaje('danger', respAjax.msj, 6000);
                }
                localStorage.clear();
            },
            error: function(respAjax) {
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', 'no se cargo archivo', 6000);
                //alert('no se cargo archivo');
            }
        });
    }
};
</script>