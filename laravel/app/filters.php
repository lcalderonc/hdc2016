<?php

App::before(
    function($request)
    {
    Lang::setLocale(Session::get('language_id'));
    }
);


App::after(
    function($request, $response)
    {
    //
    }
);
Route::filter(
    'auth.toa', function()
    {
        
    }
);
Route::filter(
    'auth', function()
    {
    if (Auth::guest()) return Redirect::guest('/');
    }
);


Route::filter(
    'auth.basic', function()
    {
    return Auth::basic("usuario");
    }
);

Route::filter(
    'hash', function()
    {
        $acceso="\$PSI20\$";
        $clave="\$1st3m@\$";
        $gestion_id=Input::get('gestion_id','');
        //$hash = Hash::make($acceso.$clave.$gestion_id);
        $hash = hash('sha256', $acceso.$clave.$gestion_id);
        $hashg=Input::get('hashg');
        //Input::flash();
        if($hash!=$hashg){
            //return Response::json(array('not found'), 404);
            //return Redirect::to('/');
            return $hash;
        }
    }
);

Route::filter(
    'guest', function()
    {
    if (Auth::check()) return Redirect::to('/');
    }
);

Route::filter(
    'csrf', function()
    {
    if (Session::token() !== Input::get('_token'))
    {
        throw new Illuminate\Session\TokenMismatchException;
    }
    }
);

Route::filter(
    'cumpleanios', function(){
    if(date("d/m")=="16/12"){
        return "Feliz Cumpleaños";
    }
    }
);

Route::filter(
    'csrf_token', function () {
        if (Input::has('token')) {
            if (Session::get('s_token') !== Input::get('token')) {
                die('Token NO Valido... !!!');
            }
        } else {
            die('Token NO Existente... !!!');
        }
    }
);

