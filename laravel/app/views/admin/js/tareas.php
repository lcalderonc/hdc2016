<script type="text/javascript">
var elemento_id;
var Tarea={
    /**
     * Mostrar lista de tareas segun id de gestion
     * @param  array  parametros, compuesto de gestion_id
     * @param  id       id del elemento html en donde dibujar
     *
     * @return html
     *
     * ejemplo: Tarea.show({task_id:'84275'},bandeja_tareas);
     * en la vista <div id="id" class="row form-group">
     */
     show:function(parametros,id){
        elemento_id=id;
        $.ajax({
            url         : 'officetrack/cargar',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : parametros,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    Tarea.showHtml(obj.datos);
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
            }
        });
    },
    showHtml:function(obj){
        var html='';
        html+="<div class='row form-group' style='height:250px; overflow-y:scroll; width: 102%;'><div class='col-sm-12 table-responsive'>";
        html+="<table class='table table-bordered table-striped'>";
        html+="<thead>";
        html+="<tr><th>ID</th><th>Tecnico</th><th>Paso</th><th>Fecha Recepccion</th><th>[x]</th></tr>";
        html+="</thead>";
        html+="<tbody id='tb_tarea_"+elemento_id+"'>";

        $.each(obj,function(index,data){
            html+="<tr>"+
                    "<td>"+data.task_id+"</td>"+
                    "<td>"+data.cod_tecnico+"</td>"+
                    "<td>"+data.paso+"</td>"+
                    "<td>"+data.fecha_recepcion+"</td>";
            if (data.paso.split("-").length>1)
                html+="<td><button type='button' onClick='Tarea.detalle("+data.id+","+'"'+data.paso+'"'+");' class='btn btn-sm btn-primary'><i class='fa fa-eye fa-lg'></i></button></td>";
            else
                html+="<td> &nbsp; </td>";

            html+="</tr>";
        });
        html+="</tbody>";
        html+="<tfoot>";
        html+="<tr><th>ID</th><th>Tecnico</th><th>Paso</th><th>Fecha Recepccion</th><th>[x]</th></tr>";
        html+="</tfoot>";
        html+="</table>";
        html+="</div></div>";

        html+="<div class='row form-group'><div class='col-sm-12 table-responsive'>";
        html+="<table id='t_paso_"+elemento_id+"' class='table table-bordered table-hover'>";
        html+="<thead>";
        html+="<tr><th>PASO N°</th><th> <span id='span_"+elemento_id+"'></span></th></tr>";
        html+="</thead>";
        html+="<tbody id='tb_paso_"+elemento_id+"'>";
        html+="</tbody>";
        html+="</div></div>";
        $("#"+elemento_id).html(html);
    },
    detalle:function(id,paso){
        var variables={task_id: id, paso :paso};
        $.ajax({
            url         : 'officetrack/cargardetalle',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : variables,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    Tarea.detalleHtml(obj.datos,paso);
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                Psi.mensaje('danger', '<?php echo trans("greetings.mensaje_error"); ?>', 6000);
        }
        });
    },
    detalleHtml:function(obj,paso) {
        var html="";
        var i, variables, cambio, contador;
        if( paso.split("-").length>1 && paso.split("-")[1].substr(0,6).toLowerCase()=='inicio' && obj.length>0){
            html+="<tr><td>X / Y </td><td><a href=\"https://www.google.com/maps?q="+$.trim(obj[0].y)+","+$.trim(obj[0].x)+"\" target=\"_blank\">"+$.trim(obj[0].x)+" / "+$.trim(obj[0].y)+"</td></tr>";
            html+="<tr><td>Observacion</td><td>"+$.trim(obj[0].observacion)+"</td></tr>";
            html+="<tr><td>Casa Visita</td><td>";
            var casa_img1=$.trim(obj[0].casa_img1);
            var casa_img2=$.trim(obj[0].casa_img2);
            var casa_img3=$.trim(obj[0].casa_img3);

            if( casa_img1!=='' || casa_img2!=='' || casa_img3!==''){
                if(casa_img1!==""){
                    html+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+casa_img1+'" title="Img CASA 1">';
                    html+="     <img src='data:image/jpg;base64,"+casa_img1+"' style='width:250px;height:250px;'  />";
                    html+='</a>';
                    variables={paso:1,imagen:casa_img1,url:obj[0].url,id:obj[0].id};
                    Tarea.crearImagenes(variables);
                }
                if(casa_img2!==""){
                    html+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+casa_img2+'" title="Img CASA 2">';
                    html+="     <img src='data:image/jpg;base64,"+casa_img2+"' style='width:250px;height:250px;'  />";
                    html+='</a>';
                    variables={paso:1,imagen:casa_img2,url:obj[1].url,id:obj[0].id};
                    Tarea.crearImagenes(variables);
                }
                if(casa_img3!==""){
                    html+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+casa_img3+'" title="Img CASA 3">';
                    html+="     <img src='data:image/jpg;base64,"+casa_img3+"' style='width:250px;height:250px;'  />";
                    html+='</a>';
                    variables={paso:1,imagen:casa_img3,url:obj[2].url,id:obj[0].id};
                    Tarea.crearImagenes(variables);
                }
            } else if( obj[0].url!=='' ){
                for (i = 0; i< obj.length; i++) {
                    html+='<a class="fancybox-button" rel="fancybox-button" href="img/officetrack/'+obj[i].url+'" title="Img CASA '+(i+1)+'">';
                    html+='     <img src="img/officetrack/'+obj[i].url+'" style="width:250px;height:250px;">';
                    html+='</a>';
                }
            }
            html+="</td></tr>";
        } else if(paso.split("-").length>1 && paso.split("-")[1].substr(0,6).toLowerCase()=='cierre' && obj.length>0){
            var imagen="";
            var firma="";
            var boleta="";
            var final_img1=$.trim(obj[0].final_img1);
            var final_img2=$.trim(obj[0].final_img2);
            var final_img3=$.trim(obj[0].final_img3);
            var firma_img=$.trim(obj[0].firma_img);
            var boleta_img=$.trim(obj[0].boleta_img);
            if( final_img1!=='' || final_img2!=='' || final_img3!=='' || firma_img!=='' || boleta_img!=='' ){
                if(final_img1!==""){
                    imagen+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+final_img1+'" title="Img Final 1">';
                    imagen+="   <img src='data:image/jpg;base64,"+final_img1+"' style='width:250px;height:250px;'  />";
                    imagen+='</a>';
                }
                if(final_img2!==""){
                    imagen+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+final_img2+'" title="Img Final 2">';
                    imagen+="   <img src='data:image/jpg;base64,"+final_img2+"' style='width:250px;height:250px;'  />";
                    imagen+='</a>';
                }
                if(final_img3!==""){
                   imagen+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+final_img3+'" title="Img Final 3">';
                    imagen+="   <img src='data:image/jpg;base64,"+final_img3+"' style='width:250px;height:250px;'  />";
                    imagen+='</a>';
                }
                if(firma_img!==""){
                    firma+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+firma_img+'" title="Firma">';
                    firma+="    <img src='data:image/jpg;base64,"+firma_img+"' style='width:250px;height:250px;'  />";
                    firma+='</a>';
                }
                if(boleta_img!==""){
                    boleta+='<a class="fancybox-button" rel="fancybox-button" href="data:image/jpg;base64,'+boleta_img+'" title="Boleta">';
                    boleta+="    <img src='data:image/jpg;base64,"+boleta_img+"' style='width:250px;height:250px;'  />";
                    boleta+='</a>';
                }
                // Aqui validación
                contador=0;
                cambio="";
                for (i = 0; i< obj.length; i++) {
                    if(i==0)
                        cambio=obj[i].nombre;

                    if(cambio!=obj[i].nombre)
                        contador=0;

                    contador++;
                    if('final'==obj[i].nombre.toLowerCase()) {
                        imagf='';
                        if(contador==1)
                            imagf=final_img1;
                        else if(contador==2)
                            imagf=final_img2;
                        else if(contador==3)
                            imagf=final_img3;

                        variables={paso:3,imagen:imagf,url:obj[i].url,id:obj[0].id};
                        Tarea.crearImagenes(variables);
                    }
                    if('boleta'==obj[i].nombre.toLowerCase()){
                        variables={paso:3,imagen:boleta_img,url:obj[i].url,id:obj[0].id};
                        Tarea.crearImagenes(variables);
                    }
                    if('firma'==obj[i].nombre.toLowerCase()){
                        variables={paso:3,imagen:firma_img,url:obj[i].url,id:obj[0].id};
                        Tarea.crearImagenes(variables);
                    }
                }
            } else if( obj[0].url!=='' ){
                contador=0;
                cambio="";
                for ( i = 0; i< obj.length; i++) {
                    if(i==0)
                        cambio=obj[i].nombre;

                    if(cambio!=obj[i].nombre)
                        contador=0;

                    contador++;
                    if('final'==obj[i].nombre.toLowerCase()){
                        imagen+='<a class="fancybox-button" rel="fancybox-button" href="img/officetrack/'+obj[i].url+'" title="'+obj[i].nombre+' '+(i+1)+'">';
                        imagen+='   <img src="img/officetrack/'+obj[i].url+'" style="width:250px;height:250px;">';
                        imagen+='</a>';
                    }
                    if('boleta'==obj[i].nombre.toLowerCase()){
                        boleta+='<a class="fancybox-button" rel="fancybox-button" href="img/officetrack/'+obj[i].url+'" title="'+obj[i].nombre+' '+(i+1)+'">';
                        boleta+='    <img src="img/officetrack/'+obj[i].url+'" style="width:250px;height:250px;">';
                        boleta+='</a>';
                    }
                    if('firma'==obj[i].nombre.toLowerCase()){
                        firma+='<a class="fancybox-button" rel="fancybox-button" href="img/officetrack/'+obj[i].url+'" title="'+obj[i].nombre+' '+(i+1)+'">';
                        firma+='    <img src="img/officetrack/'+obj[i].url+'" style="width:250px;height:250px;">';
                        firma+='</a>';
                    }
                }
            }
            html+="<tr><td>Estado </td><td>"+obj[0].estado+"</td></tr>";
            html+="<tr><td>Observacion</td><td>"+obj[0].observacion+"</td></tr>";
            html+="<tr><td>Imagen Final</td><td>"+imagen+"</td></tr>";
            html+="<tr><td>Boleta Final</td><td>"+boleta+"</td></tr>";
            html+="<tr><td>Firma</td><td>"+firma+"</td></tr>";
        }
        $("#span_"+elemento_id).text( (paso.split("-")[0]*1) );
        $("#tb_paso_"+elemento_id).html(html);
    },
    crearImagenes:function(variables){
        $.ajax({
            url         : 'imagen/imagen',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : variables,
            beforeSend : function() {
            },
            success : function() {
            },
            error: function(){
            }
        });
    },
};
</script>
