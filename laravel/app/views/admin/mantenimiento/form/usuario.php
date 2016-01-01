<!-- /.modal -->
<div class="modal fade" id="usuarioModal" tabindex="-1" role="dialog" aria-hidden="true">
<div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_usuarios" name="form_usuarios" action="" method="post" autocomplete="off">
          <fieldset>
            <legend>Datos personales</legend>
            <div class="row form-group">

              <div class="col-sm-12">
                <div class="col-sm-3">
                  <label class="control-label">Nombre
                      <a id="error_nombre" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Nombre">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_nombre" id="txt_nombre">
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Apellidos
                      <a id="error_apellido" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Apellidos">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Apellidos" name="txt_apellido" id="txt_apellido">
                </div>
                <div class="col-sm-3">
                  <label class="control-label">DNI
                      <a id="error_dni" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese DNI">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese DNI" name="txt_dni" id="txt_dni" maxlength="8">
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Estado:
                  </label>
                  <select class="form-control" name="slct_estado" id="slct_estado">
                      <option value='0'>Inactivo</option>
                      <option value='1' selected>Activo</option>
                  </select>
                </div>
              </div>

              <div class="col-sm-12">
                <div class="col-sm-3">
                  <label class="control-label">Usuario
                      <a id="error_usuario" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Usuario">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Usuario" name="txt_usuario" id="txt_usuario">
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Password
                      <a id="error_password" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Password">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="password" class="form-control" placeholder="Ingrese Password" name="txt_password" id="txt_password">
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Perfil:
                    <a id="error_perfil" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Perfil">
                        <i class="fa fa-exclamation"></i>
                    </a>
                  </label>
                  <select class="form-control" name="slct_perfil" id="slct_perfil">
                  </select>
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Sexo:
                      <a id="error_sexo" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Sexo">
                        <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <select class="form-control" name="slct_sexo" id="slct_sexo">
                      <option value='F'>Femenino</option>
                      <option value='M' selected>Masculino</option>
                  </select>
                </div>
              </div>
              <div class="col-sm-12">
                <div class="col-sm-3">
                  <label class="control-label">Email
                      <a id="error_email" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Email">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Email" name="txt_email" id="txt_email">
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Celular
                      <a id="error_celular" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Celular">
                          <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <input type="text" class="form-control" placeholder="Ingrese Celular" name="txt_celular" id="txt_celular">
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Empresa:
                    <a id="error_empresa" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Empresa del usuario">
                        <i class="fa fa-exclamation"></i>
                    </a>
                  </label>
                  <select class="form-control" name="slct_empresa" id="slct_empresa">
                  </select>
                </div>

                <div class="col-sm-3">
                  <label class="control-label">Area:
                      <a id="error_area" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Area">
                        <i class="fa fa-exclamation"></i>
                      </a>
                  </label>
                  <select class="form-control" name="slct_area" id="slct_area">
                  </select>
                </div>

              </div>
              <div class="col-sm-12">
                <div class="col-sm-3">
                  <label class="control-label">Empresas a gestionar:
                  </label>
                  <select class="form-control" multiple="multiple" name="slct_empresas[]" id="slct_empresas">
                  </select>
                </div>
                <div class="col-sm-3">
                  <label class="control-label">Grupo de quiebres:
                  </label>
                  <select class="form-control" multiple="multiple" name="slct_quiebregrupos[]" id="slct_quiebregrupos">
                  </select>
                </div>
              </div>
            </div>
          </fieldset>


          <fieldset id="f_quiebres_restriccion">
            <legend>Restriccion de Quiebres <input type="button" value="+" id="show_hide_grupoquiebre"></legend>
            <ul class="list-group" id="t_restriccionquiebre" style="display: none;"></ul>
          </fieldset>

          <fieldset id="f_zonales">
            <legend>Zonales: <input type="button" value="+" id="show_hide_zonales"></legend>
            <div class="row form-group">
              <div class="col-sm-12">
                <div class="col-sm-6">

                  <select class="form-control" name="slct_zonal" id="slct_zonal">
                  </select>
                  <a id="error_zonales_selec" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Seleccione Zonales">
                    <i class="fa fa-exclamation"></i>
                </a>
                </div>
                <div class="col-sm-6">
                    <button type="button" class="btn btn-success" Onclick="AgregarZonal();">
                      <i class="fa fa-plus fa-sm"></i>
                      &nbsp;Añadir
                    </button>
                </div>
              </div>
            </div>
            <ul class="list-group" id="t_zonales" style="display: none;"></ul>
          </fieldset>
          <fieldset id="f_permisos_modulos">
            <legend>Permisos y accesos a Modulos</legend>

            <div class="row form-group">
              <div class="col-sm-12">
                <div class="col-sm-6">
                  <label class="control-label">Modulos:
                  </label>
                  <select class="form-control" name="slct_modulos" id="slct_modulos">
                  </select>
                </div>
                <div class="col-sm-6">
                    <br>
                    <button type="button" class="btn btn-success" Onclick="AgregarSubmodulo();">
                      <i class="fa fa-plus fa-sm"></i>
                      &nbsp;Añadir
                    </button>
                </div>
              </div>
            </div>
            <ul class="list-group" id="t_submoduloUsuario"></ul>
          </fieldset>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->