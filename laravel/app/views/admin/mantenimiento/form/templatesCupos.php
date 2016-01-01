</style>
<script id="horario-tr" type="text/x-handlebars-template">
    <tr data-horario_id="{{horario_id}}">
        <td>{{horario}}</td>
        <td onKeyPress="return soloNumeros(event)" class="edit-row" contentEditable>0</td>
        <td onKeyPress="return soloNumeros(event)" class="edit" data-dia="1" id="{{l_id}}" contentEditable>{{lunes}}</td>
        <td onKeyPress="return soloNumeros(event)" class="edit" data-dia="2" id="{{m_id}}" contentEditable>{{martes}}</td>
        <td onKeyPress="return soloNumeros(event)" class="edit" data-dia="3" id="{{mi_id}}" contentEditable>{{miercoles}}</td>
        <td onKeyPress="return soloNumeros(event)" class="edit" data-dia="4" id="{{j_id}}" contentEditable>{{jueves}}</td>
        <td onKeyPress="return soloNumeros(event)" class="edit" data-dia="5" id="{{v_id}}" contentEditable>{{viernes}}</td>
        <td onKeyPress="return soloNumeros(event)" class="edit" data-dia="6" id="{{s_id}}" contentEditable>{{sabado}}</td>
        <td onKeyPress="return soloNumeros(event)" class="edit" data-dia="7" id="{{d_id}}" contentEditable>{{domingo}}</td>
    </tr>
</script>