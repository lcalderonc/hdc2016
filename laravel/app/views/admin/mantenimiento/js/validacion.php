<script type="text/javascript">
    var inputsOriginal = new Array();
    var inputs = new Array();
    $(document).ready(function() {
        $("#btnGuardar").click(function(){
            var html = "";
            $.each( inputsOriginal, function( i, val ) {
                if(!($.inArray( val , inputs ) > -1)){
                    var tabla = $("#inputValor"+val).data("tabla");
                    var tipo = $("#inputVisible"+val).data("tipo");
                    html += "<input type='hidden' name='idValidacion["+tabla+"]' value='"+val+"'>";
                    html += "<input type='hidden' name='slct_"+tabla+"[]' value=''>";
                    html += "<input type='hidden' name='tipoQuery["+tabla+"]' value='"+tipo+"'>";
                }
            });
            $("#resultadosVacios").html(html);
            var data = $("#formValidaciones").serialize();
            var id = $("#idTabla").val();
            var nombre = $("#nombreTabla").text();
            var tabla = {id:id,nombre:nombre};
            Validacion.SendValues(data,tabla);
        });
        $("#valoresConfiguracion").hide();
        Validacion.CargarValidaciones();
        $("#btnActualizar").click(function(){
            var select = "";
            $( "#t_validacionDetalle input:checked.visible" ).each(function( index ) {
                var id = $( this ).data('id');
                var tabla = $(this).data('tabla');
                if(tabla == 'estados'){
                    tabla = 'estado';
                }
                var tipo = $(this).data('tipo');
                select += '<div class="col-md-4"><input type="hidden" name="tipoQuery['+tabla+']" value="'+tipo+'"><input type="hidden" name="idValidacion['+tabla+']" value="'+id+'"><label>'+tabla+':</label><select class="form-control hidden" name="slct_'+tabla+'[]" id="slct_'+tabla+'" multiple>'+
                                '<option value="">.::Seleccione::.</option>'+
                            '</select></div>';
            });
            $("#resultados").html(select);
            $("#resultadosVacios").html("");
            $( "#t_validacionDetalle input:checked.visible" ).each(function( index ) {
                var id = $( this ).data('id');
                var tabla = $(this).data('tabla');
                if(tabla == 'estados'){
                    tabla = 'estado';
                }
                $.ajax({
                    url         : 'configuracion/getvalores',
                    type        : 'POST',
                    cache       : false,
                    dataType    : 'json',
                    data        : {id:id},
                    success : function(obj) {
                        var data = {estado:1};
                        slctGlobal.listarSlct(obj.controlador,'slct_'+tabla,'multiple',obj.datos,data);
                    },
                    error: function(){
                        $(".overlay,.loading-img").remove();
                        Psi.mensaje('danger', 'Ocurrio una interrupción en el proceso,Favor de intentar nuevamente.', 6000);
                    }
                });
                $("#slct_"+tabla).show();
            });
            //slctGlobal.listarSlct('configuracion','slct_zonal','multiple',ids,data);
            //console.log(data);
        });
    });

    HTMLCargarValidacion=function(datos){
        var html="";
        $('#t_validacion').dataTable().fnDestroy();

        $.each(datos,function(index,data){
            html+="<tr>"+
                "<td>"+data.nombre+"</td>"+
                '<td><span id="'+data.id+'" onClick="verDetalle('+data.id+',\''+data.nombre+'\')" class="btn btn-primary"><i class="glyphicon glyphicon-eye-open"></i></span></td>';

            html+="</tr>";
        });
        $("#tb_validacion").html(html);
        activarTabla();
    };
    activarTabla=function(){
        $("#t_validacion").dataTable();
    };

    activarTablaDetalle=function(){
        $("#t_validacionDetalle").dataTable();
    };

    verDetalle = function(id,name){
        Validacion.CargaDetalleValidaciones(id,name);
    };

    HTMLCargarDetalleValidacion=function(datos){
        var html ="";
        $('#t_validacionDetalle').dataTable().fnDestroy();
        $.each(datos,function(index,data){
            var inputValor = '<input data-tabla="'+data.tabla+'"  type="checkbox" name="'+data.tabla+index+'"';
            var inputVisible = '<input data-tabla="'+data.tabla+'" class="visible" type="checkbox" name="'+data.tabla+index+'"';
            if(data.config_tabla_id){
                if(data.estado === 1) {
                    inputs.push(data.config_tabla_id);
                    inputValor += " id='inputValor" + data.config_tabla_id + "' onclick='checkValue(" + data.config_tabla_id + ");' checked >";
                    inputVisible += ' data-tipo="updateActive" id="inputVisible'+data.config_tabla_id+'" data-id="'+data.config_tabla_id+'" onclick="checkInputValue('+data.config_tabla_id+')">';
                }else{
                    inputValor += " id='inputValor" + data.config_tabla_id + "' onclick='checkValue(" + data.config_tabla_id + ");'>";
                    inputVisible += ' data-tipo="updateInactive" id="inputVisible'+data.config_tabla_id+'" data-id="'+data.config_tabla_id+'" onclick="checkInputValue('+data.config_tabla_id+')">';
                }

            }else{
                inputValor += " id='inputValor"+data.id+"' onclick='checkValue("+data.id+");'>";
                inputVisible += ' data-tipo="new" id="inputVisible'+data.id+'" data-id="'+data.id+'" onclick="checkInputValue('+data.id+')">';
            }
            html+="<tr>"+
                "<td>"+data.tabla+"</td>"+
                '<td>'+inputValor+'</td>'+
                '<td>'+inputVisible+'</td>';

            html+="</tr>";
        });
        $("#tb_validacionDetalle").html(html);
        activarTablaDetalle();
        $("#valoresConfiguracion").show();
        inputsOriginal = inputs.slice();
    };

    getValues = function(id){
        html = Validacion.GetValue(id);
    };

    checkValue=function(id){
        var index =inputs.indexOf(id);

        if(index > -1)
        {
            inputs.splice(index, 1);
            if($('#inputVisible' + id).prop('checked')){
                $('#inputVisible' + id).prop('checked', false);
            }

        }else{
            $('#inputVisible' + id).prop('checked', true);
            inputs.push(id);
        }
    };

    checkInputValue=function(id){
        var index =inputsOriginal.indexOf(id);
        if(!($('#inputValor' + id).is(":checked"))){
            $('#inputValor' + id).prop('checked', true);
        }else{
            if(!(index >-1)){
                $('#inputValor' + id).prop('checked', false);
            }
        }
    }
</script>