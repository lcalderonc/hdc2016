<!-- Left side column. contains the logo and sidebar -->
            <aside class="left-side sidebar-offcanvas">
                <!-- sidebar: style can be found in sidebar.less -->
                <section class="sidebar">
                    <!-- Sidebar user panel -->
                    <div class="user-panel">                        
                        <div class="pull-left image" data-toggle="modal" data-target="#imagenModal">
                            @if (Auth::user()->imagen!=null)
                                <img src="img/user/<?= md5('u'.Auth::user()->id).'/'.Auth::user()->imagen; ?>" class="img-circle" alt="User Image" />
                            @else
                                <img src="" class="img-circle" alt="User Image" />
                            @endif
                        </div>
                        
                        <div class="pull-left info">
                            <p>Hola, {{ Auth::user()->usuario }}</p>

                            <a href="#"><i class="fa fa-circle text-success"></i> {{ trans('greetings.inicio_sesion') }}</a>
                        </div>
                    </div>

<!--                    <div class="btn-group user-panel">
                      <a class="btn btn-default">
                        <i class="fa fa-flag-checkered fa-fw"></i> {{ Session::get('language') }}
                      </a>
                      <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                        <span class="fa fa-caret-down"></span></a>
                      <ul class="dropdown-menu">
                        <li><a href="language/idioma?language_id=es&language=Español"><i class="fa <?php echo ( Session::get('language_id')=='es' ) ? 'fa-flag-checkered': 'fa-flag-o'; ?> fa-fw"></i> Español</a></li>
                        <li><a href="language/idioma?language_id=en&language=English"><i class="fa <?php echo ( Session::get('language_id')=='en' ) ? 'fa-flag-checkered': 'fa-flag-o'; ?> fa-fw"></i> English</a></li>
                      </ul>
                    </div>
 -->

                    @include( 'layouts.admin_menu' )
                    
                </section>
                <!-- /.sidebar -->
            </aside>

            @include( 'layouts.form.imagen' )
