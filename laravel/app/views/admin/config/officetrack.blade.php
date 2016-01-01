<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
@parent
{{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
{{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}

{{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
{{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
{{ HTML::script('lib/input-mask/js/jquery.inputmask.js') }}
{{ HTML::script('lib/input-mask/js/jquery.inputmask.date.extensions.js') }}



@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<div class="box box-default">
    <div class="box-header with-border">
        <div class="box-title">Configuraci&oacute;n Officetrack</div>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool btn-sm" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div><!-- /.box-tools -->
    </div><!-- /.box-header -->
    <div class="box-body">
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Tab 1</a></li>
                <li class=""><a href="#tab_2" data-toggle="tab">Tab 2</a></li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="tab_1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="box box-danger">
                                <div class="box-header">
                                    <i class="fa fa-arrows-h"></i>
                                    <h3 class="box-title">Distancia</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            Distancia (metros)
                                        </div>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control" min="0" max="1000" step="10" placeholder="0 - 1000" style="width: 100%">
                                        </div>
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->

                        <div class="col-md-6">
                            <div class="box box-info">
                                <div class="box-header">
                                    <i class="fa fa-bullhorn"></i>
                                    <h3 class="box-title">Callouts</h3>
                                </div><!-- /.box-header -->
                                <div class="box-body">
                                    <div class="callout callout-danger">
                                        <h4>I am a danger callout!</h4>
                                        <p>There is a problem that we need to fix. A wonderful serenity has taken possession of my entire soul, like these sweet mornings of spring which I enjoy with my whole heart.</p>
                                    </div>
                                    <div class="callout callout-info">
                                        <h4>I am an info callout!</h4>
                                        <p>Follow the steps to continue to payment.</p>
                                    </div>
                                    <div class="callout callout-warning">
                                        <h4>I am a warning callout!</h4>
                                        <p>This is a yellow callout.</p>
                                    </div>
                                </div><!-- /.box-body -->
                            </div><!-- /.box -->
                        </div><!-- /.col -->
                    </div>
                    
                </div><!-- /.tab-pane -->
                <div class="tab-pane" id="tab_2">
                    The European languages are members of the same family. Their separate existence is a myth.
                    For science, music, sport, etc, Europe uses the same vocabulary. The languages only differ
                    in their grammar, their pronunciation and their most common words. Everyone realizes why a
                    new common language would be desirable: one could refuse to pay expensive translators. To
                    achieve this, it would be necessary to have uniform grammar, pronunciation and more common
                    words. If several languages coalesce, the grammar of the resulting language is more simple
                    and regular than that of the individual languages.
                </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
        </div>
    </div><!-- /.box-body -->
</div><!-- /.box -->

@stop

