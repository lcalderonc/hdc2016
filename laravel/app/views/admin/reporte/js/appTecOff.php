<script type="text/javascript">
$(function(){

    //temmplates
    Templates = {};
    //  TEMPLATE DEL ARCHIVO TEMPLATES.PHP
    //USO DE PLANTILLAS HANDLERBARS.JS
    Templates.trTareas          =Handlebars.compile($("#tarea-tr").html());
    Templates.trTareasDetalle   =Handlebars.compile($("#tarea-tr-detalle").html());
    //Templates.trTareasVs        =Handlebars.compile($("#tareaDetalleVs").html());
    //Templates.detalleGrupo      =Handlebars.compile($("#detalleGrupo").html());

    //helpers

    Handlebars.registerHelper('StringToClass', function(string) {
        if(string === null || string === undefined){ //validar si no se envia valor
            string = "";
        }
        return string.trim().split(" ").join("-").toLowerCase();
    });

    OT = {};
    OT.controllers = {};
    OT.models = {};
    OT.views = {};

    window.OT = OT;
    window.Templates;
});

</script>