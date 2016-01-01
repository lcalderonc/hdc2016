<script type="text/javascript">
var ids = [];
var cabecerasProvision = ["fechorreg","codreq","codcli","codnod","indvip","codordtrab","codctr","codofcadm","desdtt","coddtt","codpvc","coddpt","tipreq","codmotv","nroplano"];
var cabecerasAveria =["fechorreg","codreqatn","codcli","codnod","destipvia","desnomvia","numvia","destipurb","desurb","despis","desint","desmzn","deslot","coddtt","codordtrab","codclasrv","tipreq","codmotv","nroplano","desnomctr","codofcadm","desdtt"];
var datos = {};
var HTML;
var file;
var forzar;
var usuario_id="<?php echo Auth::user()->id; ?>";
$(document).ready(function() {
    //slctGlobal.listarSlct('quiebre','slct_quiebre','simple',ids,0,1);

    Masivos.cargarQuiebres();
    //Masivos.cargarEmpresas();
    //var data ={usuario_id:usuario_id};
    slctGlobal.listarSlct('empresa','slct_empresa','simple');
    
    $("#txt_archivo").on('change',function() {
        var input = $(this)[0];
        file = input.files[0];
        validarFile( input ) ;
    });
    $('.iCheck-helper').click(function(){
        selectFiltro();
    });
    $("#lbl_masivo").click(function(){
        selectFiltro();
    });
    $("#lbl_individual").click(function(){
        selectFiltro();
    });
    selectFiltro();
});
selectFiltro=function(){
    if ($("#lbl_masivo div").attr("aria-checked")=='true') {
        $('#averia').attr('disabled', 'true');
        $('#txt_archivo').removeAttr('disabled');
    } else {
        $('#txt_archivo').attr('disabled', 'true');
        $('#averia').removeAttr('disabled');
    }
    if ($("#lbl_individual div").attr("aria-checked")=='true') {
        $('#txt_archivo').attr('disabled', 'true');
        $('#averia').removeAttr('disabled');
    } else {
        $('#averia').attr('disabled', 'true');
        $('#txt_archivo').removeAttr('disabled');
    }
    //click enforzar
    forzar=$("#chk_forzar").prop("checked"); // false or true
    if (forzar===false) {
        forzar='0';
    } else {
        forzar='1';
    }
};
validarFile=function(archivo){
    if ('files' in archivo) {
        if (archivo.files.length === 0) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
};
doCarga=function(){

    var opcion = $('input:radio[name=opcion]:checked').val();

    if (opcion==='1') {
        doCargaMasivo();
    } else
        doConsultaIndividual();

};
doConsultaIndividual=function(){

    if( $("#averia").val()!=='' &&
        ($("#slct_quiebre").val()!='--- Elige Quiebre ---' ||
        $("#slct_empresa").val()!=='') ){
        var data = new FormData();
    
        var quiebre = $("#slct_quiebre").val();//
        var empresa = $("#slct_empresa").val();//
        if ( quiebre!='--- Elige Quiebre ---' && quiebre !=='') {
            data.append('quiebre_id', quiebre);
            data.append('quiebre', $("#slct_quiebre option:selected").attr('apocope'));
        }
        data.append('averia', $("#averia").val());
        if (empresa!== '' && empresa !==null) {
            data.append('empresa_id', empresa);
            data.append('empresa', $("#slct_empresa option:selected").text());
        }
        data.append('forzar', forzar);
        Masivos.actualizaQuiebreIndividual(data);
    } else if($("#slct_quiebre").val()=='--- Elige Quiebre ---' &&
         $("#slct_empresa").val()===''){
        alert('Seleccione un quiebre y/o contrata');
    } else {
        alert('ingrese codigo de averia');
    }

};
doCargaMasivo=function(){

    if(  $("#txt_archivo").val()!=='' &&
         ($("#slct_quiebre").val()!='--- Elige Quiebre ---' ||
         $("#slct_empresa").val()!=='') ){
        var inputFile = $("#txt_archivo");

        var data = new FormData();
        var quiebre = $("#slct_quiebre").val();//
        var empresa = $("#slct_empresa").val();
        if ( quiebre!='--- Elige Quiebre ---' && quiebre !=='') {
            data.append('quiebre_id', quiebre);
            data.append('quiebre', $("#slct_quiebre option:selected").attr('apocope'));
        }
        if (empresa!== '' && empresa !==null) {
            data.append('empresa_id', empresa);
            data.append('empresa', $("#slct_empresa option:selected").text());
        }
        data.append('archivoTmp', file);
        data.append('forzar', forzar);
        Masivos.cargarFile(data);
        
    } else if($("#slct_quiebre").val()=='--- Elige Quiebre ---' &&
         $("#slct_empresa").val()===''){
        alert('Seleccione un quiebre y/o contrata');
    } else{
        alert('Busque y seleccione un archivo');
    }
};
descargarArchivo=function(contenidoEnBlob, nombreArchivo) {
    var reader = new FileReader();
    reader.onload = function (event) {
        var save = document.createElement('a');
        save.href = event.target.result;
        save.target = '_blank';
        save.download = nombreArchivo || 'archivo.dat';
        var clicEvent = new MouseEvent('click', {
            'view': window,
                'bubbles': true,
                'cancelable': true
        });
        save.dispatchEvent(clicEvent);
        (window.URL || window.webkitURL).revokeObjectURL(save.href);
    };
    reader.readAsDataURL(contenidoEnBlob);
};
generarTxt=function(txt){
    var texto = [];
    texto.push('Codigo: \t');
    texto.push('Quiebre: \t');
    texto.push('Contrata: \t');
    texto.push('Estado: \n');
    $.each(txt,function(index,data){
        texto.push(data.codigo+'\t');
        texto.push(data.quiebre+'\t');
        texto.push(data.contrata+'\t');
        texto.push(data.estado+'\n');
    });
    return new Blob(texto, {
        type: 'text/plain'
    });
};
</script>