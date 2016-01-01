<script type="text/javascript">
var fecha_agenda="";
var horario_agenda="";
var dia_agenda="";
var hora_agenda="";

var Agenda={

    /**
     * Mostrar rorario de tecnico a gestionar
     * @param  array  parametros, compuesto de zona y empresa
     * 
     * @return html
     * 
     * ejemplo: Agenda.show({zona:'8',empresa:'4'});
     * en la vista <div id="html" class="box-body table-responsive"></div>
     */
    show:function(parametros){
        $.ajax({
            url         : 'agenda/libre',
            type        : 'POST',
            cache       : false,
            dataType    : 'json',
            data        : parametros,
            beforeSend : function() {
                $("body").append('<div class="overlay"></div><div class="loading-img"></div>');
            },
            success : function(obj) {
                if(obj.rst==1){
                    $('#html').html(obj.html);
                    $('#html').after(Agenda.after);
                }
                $(".overlay,.loading-img").remove();
            },
            error: function(){
                $(".overlay,.loading-img").remove();
                $("#msj").html('<div class="alert alert-dismissable alert-danger">'+
                                        '<i class="fa fa-ban"></i>'+
                                        '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
                                        '<b><?php echo trans("greetings.mensaje_error"); ?></b>'+
                                    '</div>');
            }
        });
    },

    /**
     * ejecutar al seleccionar horario, se establecen variables globales
     * fecha_agenda "2015-03-09"
     * horario_agenda "3"
     * dia_agenda "09"
     * hora_agenda "14pm - 16pm"
     */
    after:function(){

        $("#horario td").click(function(){

            var color = $(this).css("background-color");

            //para IE8 ya que toma el color como lo pone
            if(color.indexOf('#')!=-1){
                color = color;
            }else{
                color = hexcolor(color);
            }

            id_celda = $(this).attr("title");

            horario_celda = document.getElementById("horario").getElementsByTagName("td")[id_celda];
            totales = horario_celda.getAttribute("data-total");

            if(color!="#ff0000" && color!="#f0e535" && color!="#49afcd" && totales>0){

                $("#horario td").each(function(){
                    color = $(this).css("background-color");
                    if(color.indexOf('#')!=-1){
                        color = color;
                    }else{
                        color = hexcolor(color);
                    }
                    if(color!="#ff0000" && color!="#f0e535" && color!="#49afcd"){
                        $(this).css({"background":"","color":""});
                    }
                });

                $(this).css({"background":"#5cb85c","color":"#fff"});
                //variables globales
                fecha_agenda=horario_celda.getAttribute("data-fec");
                horario_agenda=horario_celda.getAttribute("data-horario");
                dia_agenda=horario_celda.getAttribute("data-dia");
                hora_agenda=horario_celda.getAttribute("data-hora");

                $(".horario .help-inline").css("display","none");
                $(".fecha_error").html("").css("display","none");

            }
        });

        hexcolor=function(colorval) {
            var parts = colorval.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
            delete(parts[0]);
            for (var i = 1; i <= 3; ++i) {
                parts[i] = parseInt(parts[i]).toString(16);
                if (parts[i].length == 1) parts[i] = '0' + parts[i];
            }
            color = '#' + parts.join('');
            return color;
        };
        
    }
};
</script>
