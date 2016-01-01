<script type="text/javascript">
var ids = [];
var cabecerasProvision = ["fechorreg","codreq","codcli","codnod","indvip","codordtrab","codctr","codofcadm","desdtt","coddtt","codpvc","coddpt","tipreq","codmotv","nroplano"];
var cabecerasAveria =["fechorreg","codreqatn","codcli","codnod","destipvia","desnomvia","numvia","destipurb","desurb","despis","desint","desmzn","deslot","coddtt","codordtrab","codclasrv","tipreq","codmotv","nroplano","desnomctr","codofcadm","desdtt"];
var datos = {};
var HTML;
$(document).ready(function() {
    var data = {usuario:1};
    slctGlobal.listarSlct('quiebre','slct_quiebre','simple',ids,data);
    $("#txt_archivo").on('change',function() {
        var input = $(this)[0];
        var file = input.files[0];
        if ( validarFile( input ) ) {
            //validar si selecciono router
            var quiebre = $("#slct_quiebre").val();
            if (quiebre=='24') {
                previsualizarFileQuiebre(file);
            } else {
                previsualizarFile(file);
            }

        }
    });
});
previsualizarFileQuiebre=function(file){
    var reader = new FileReader();

    reader.onload = function (event) {
        var textFile = event.target;
        var archivo = textFile.result;
        var arrayArchivo= archivo.split("\n");
        var row = [],row2 = [];
        var objData = {
              'filaExcel': [],
              'estado': true
            };
        for (var i = 0; i < arrayArchivo.length; i++) {
            if (i===0) {//cabecera
                cabecera = arrayArchivo[i].split("\t");
                col_zonal = cabecera.indexOf('Zonal');
                col_ciudad = cabecera.indexOf('Ciudad');
                col_mdf = cabecera.indexOf('Mdf');
                col_codigo_req = cabecera.indexOf('HG');
                col_nomcliente = cabecera.indexOf('Cliente');
                col_telefono = cabecera.indexOf('Telefono');
                col_contrata = cabecera.indexOf('Contrata');
                col_fecha_reg = cabecera.indexOf('Fec Inst');
                col_direccion = cabecera.indexOf('Direccion');
                col_distrito = cabecera.indexOf('Distrito');

                //observacion
                col_o = cabecera.indexOf('S/N Equipo Ant');
                col_x = cabecera.indexOf('Confirma');
                col_y = cabecera.indexOf('DNI Confirma');
                col_aq = cabecera.indexOf('Persona Contacto');

            } else {
                row = arrayArchivo[i].split("\t");
                obs=row[col_o];
                if (obs===undefined) {
                    continue;
                }
                //console.log(obs);
                /*part1 = obs.substring(0,4).split("").reverse().join("");
                part2 = obs.substring(4,8).split("").reverse().join("");
                part2 = part2.substring(0,2)+'@'+part2.substring(2,4);
                part3 = obs.substring(8,12).split("").reverse().join("");*/
                part1 = obs.substring(0,2);
                part2 = obs.substring(2,4);
                part3 = obs.substring(4,6);
                part4 = obs.substring(6,8);
                part5 = obs.substring(8,10);
                part6 = obs.substring(10,12);
                invertido='#'+part2+part1+part4+'@'+part3+part6+part5+'*';
                invertido = invertido.toUpperCase();
                observacion='MAC:'+row[col_o]+', contraseña:'+invertido+', Confirma:'+row[col_x]+', DNI Confirma:'+row[col_y]+', Persona Contacto:'+row[col_aq];
                row2 = {
                    zonal: row[col_zonal],
                    ciudad: row[col_ciudad],
                    mdf: row[col_mdf],
                    codigoReq: row[col_codigo_req],
                    nomCliente: row[col_nomcliente],
                    telefono: row[col_telefono],
                    contrata: row[col_contrata],
                    fechaReg: row[col_fecha_reg],
                    direccion:row[col_direccion],
                    distrito: row[col_distrito],
                    observacion:observacion
                };

                objData.filaExcel.push(row2);
            }
        }
        /*
        var form = new FormData();

        form.append('data', JSON.stringify(objData) );

        uploadImage(form);*/
        //alcmacenar en local storage objData
        localStorage.setItem('objData', JSON.stringify(objData));
    };
    reader.readAsText(file);
};
cargar=function(){
    var url;
    var form = new FormData();
    if( $("#data").attr("data")!=='' && $("#slct_quiebre").val()!=='' && $("#txt_archivo").val()!=='' && datos.codcli!=='' && datos.codcli!==undefined){

        url="upload/cargar";
        if(servicio=='provision'){
            form.append('tipocalle', $("#tipocalle").val() );
            form.append('nomcalle', $("#nomcalle").val() );
            form.append('numcalle', $("#numcalle").val() );
            form.append('piso', $("#piso").val() );
            form.append('interior', $("#interior").val() );
            form.append('manzana', $("#manzana").val() );
            form.append('lote', $("#lote").val() );
        }
        form.append('data', $("#data").attr("data") );
        form.append('apellidos', $("#apellidos").val() );
        form.append('nombre', $("#nombre").val() );
        form.append('quiebre', $("#slct_quiebre").val() );
        form.append('servicio',servicio );
        url="upload/cargar";
        File.cargar(form, url);

    } else if($("#slct_quiebre").val()=='24') {
        form.append('data', localStorage.getItem('objData'));
        url="upload/cargarquiebre";
        File.cargar(form,url);

    } else if($("#slct_quiebre").val()==='') {
        alert('Seleccione un quiebre');
    } else if (datos.codcli===undefined) {
        alert('Cargue archivo con un registro');
    }
    else{
        alert('Busque y seleccione un archivo');
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
//24 router
//caundo es routes no deberia previsualizar nada, solo tiene que cargar los campos tal como estan en el registro

previsualizarFile=function(file){
    var reader = new FileReader();
    datos = {};
    $("#fileOutput").html("");
    $("#actividad").html("");
    reader.onload = function (event) {
        var textFile = event.target;
        var archivo = textFile.result;
        var arrayArchivo= archivo.split("\n");
        if (arrayArchivo.length>3) {
             alert('El archivo contiene mas de un registro');
             return;
        }
        var cabecera = arrayArchivo[0].split("\t");
        var registro = arrayArchivo[1].split("\t");
        var buscador=0, index,i;
        servicio='provision';
        for (i = cabecerasProvision.length - 1; i >= 0; i--) {
            index = cabecerasProvision[i];
            buscador = cabecera.indexOf(index,0);
            if ( buscador>= 0) {
                datos[cabecerasProvision[i]] = registro[buscador];
            }
            else{
                servicio='averia';
                datos = {};
                break;
            }
        }
        if (servicio == 'averia') {
            for (i = cabecerasAveria.length - 1; i >= 0; i--) {
                index = cabecerasAveria[i];
                buscador = cabecera.indexOf(index,0);
                if (buscador >= 0) {
                    datos[cabecerasAveria[i]] = registro[buscador];
                }else{
                    servicio='';
                    datos = {};
                    break;
                }
            }
        }
        if (servicio === '') {
            alert('archivo no valido');
            return;
        }
        var codsrv=registro[cabecera.indexOf('codsrv')];
        HTMLcabecera = "";
        HTMLregistro = "";
        for(var indice in datos) {
            HTMLcabecera += "<th class='tabla_th'>"+indice+"</th>";
            HTMLregistro += "<td class='tabla_td'>"+datos[indice]+"</td>";
        }
        var form = new FormData();
        //para validar si selecciono de acuerdo al archivo que subio
        var quiebre = $("#slct_quiebre").val();
        form.append('codcli', datos.codcli);
        form.append('codsrv', codsrv);
        form.append('servicio', servicio);
        File.ConsultarQuiebre(form);
    };
    reader.readAsText(file);
};
</script>