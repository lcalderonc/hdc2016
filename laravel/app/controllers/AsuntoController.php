<?php

use Ofsc\Test;

class AsuntoController extends \BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        /*$asuntos = Asunto::where('estado', 1)->get();
 
        return Response::json(array(
            'error' => false,
            'asuntos' => $asuntos->toArray()),
            200
        );*/
        //$conversion = Test::conversion2('USD','PEN');
        /*return Response::json(array(
            $conversion),
            200
        );*/
        $test = new Test;
        $conversion = $test->conversion('USD','PEN');

       // $rst = Test::conversion($test,'USD','PEN');
        // $test->conversion('USD','PEN');

        return Response::json(array(
            $conversion),
            200
        );
        
        //return Asunto::server();
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        $asunto = new Asunto;
        $asunto['param1'] = Request::get('param1');
        $asunto['param2'] = Request::get('param2');
        $asunto['param3'] = Request::get('param3');
        $asunto['param4'] = Request::get('param4');
        $asunto['param5'] = Request::get('param5');
        $asunto['param6'] = Request::get('param5');
        $asunto['estado'] = 1;
        $asunto['usuario_created_at'] = Auth::user()->id;
        //$asunto->user_id = Auth::user()->id;

        // Validation and Filtering is sorely needed!!
        // Seriously, I'm a bad person for leaving that out.

        $asunto->save();

        return Response::json(
            array(
            'error' => false,
            'asuntos' => $asunto->toArray()),
            200
        );
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        // Make sure current user owns the requested resource
        $asunto = Asunto::where('estado', 1)
            ->where('id', $id)
            ->take(1)
            ->get();

        return Response::json(array(
            'error' => false,
            'asuntos' => $asunto->toArray()),
            200
        );
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        $asunto = Asunto::where('estado', 1)->find($id);

        if ( Request::get('param1') )
        {
            $asunto->param1 = Request::get('param1');
        }
        if ( Request::get('param2') )
        {
            $asunto->param2 = Request::get('param2');
        }
        if ( Request::get('param3') )
        {
            $asunto->param3 = Request::get('param3');
        }
        if ( Request::get('param4') )
        {
            $asunto->param4 = Request::get('param4');
        }
        if ( Request::get('param5') )
        {
            $asunto->param5 = Request::get('param5');
        }
        if ( Request::get('param6') )
        {
            $asunto->param6 = Request::get('param6');
        }
        if ( Request::get('estado') )
        {
            $asunto->estado = Request::get('estado');
        }

        $asunto->usuario_updated_at = Auth::user()->id;

        $asunto->save();

        return Response::json(array(
            'error' => false,
            'message' => 'Asunto updated'),
            200
        );
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $asunto = Asunto::where('estado', 1)->find($id);

        $asunto->delete();

        return Response::json(array(
            'error' => false,
            'message' => 'asunto deleted'),
            200
            );
    }


}
