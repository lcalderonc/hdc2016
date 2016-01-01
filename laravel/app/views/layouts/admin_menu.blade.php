<!-- sidebar menu: : style can be found in sidebar.less -->
                    <ul class="sidebar-menu">
                        <!--
                        <li>
                            <a href="admin.inicio">
                                <i class="fa fa-dashboard"></i> <span>{{ trans('greetings.menu_inicio') }}</span>
                            </a>
                        </li>
                        -->

                        @if (isset($menu))
                            @foreach ( $menu as $key => $val)
                                <li class="treeview">
                                    <a href="#">
                                        <i class="fa {{ $val[0]->icon }}"></i> <span>{{ $key }}</span>
                                        <i class="fa fa-angle-left pull-right"></i>
                                    </a>
                                    <ul class="treeview-menu">
                                        @foreach ( $val as $k)
                                        <?php
                                            $hash = hash('sha256', Config::get('wpsi.permisos.key').$k->agregar.$k->editar.$k->eliminar);
                                        ?>
                                            <li><a href="admin.{{ $k->path }}" data-clave="{{ $hash }}" data-accesos="{{ $k->agregar.$k->editar.$k->eliminar }} "><i class="fa fa-angle-double-right"></i>{{ $k->submodulo }} </a></li>
                                        @endforeach
                                    </ul>
                                </li>
                            @endforeach
                        @endif
                        <li class="treeview">
                            <a href="#">
                                <i class="fa fa-shield"></i> <span>{{ trans('greetings.menu_info') }}</span>
                                <i class="fa fa-angle-left pull-right"></i>
                            </a>
                            <ul class="treeview-menu">
                                <li><a href="admin.mantenimiento.misdatos"><i class="fa fa-angle-double-right"></i>{{ trans('greetings.menu_info_actualizar') }} </a></li>
                            </ul>
                        </li>
                    </ul>