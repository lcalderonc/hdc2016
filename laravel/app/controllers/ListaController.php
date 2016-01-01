<?php
class ListaController extends \BaseController
{
    protected $_errorController;
    /**
     * Valida sesion activa
     */
    public function __construct(ErrorController $ErrorController)
    {
        $this->beforeFilter('auth');
        $this->_errorController = $ErrorController;
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/estados
     *
     * @return Response
     */
    public function postEstados()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $datos = Estado::getAll();
            return Response::json(array('rst'=>1,'datos'=>$datos));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/estadosdigitalizacionmotivo
     *
     * @return Response
     */
    public function postEstadosdigitalizacionmotivo()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $datos = EstadosDigitalizacion::getEstadosDigitalizacionMotivos();
            return Response::json(array('rst'=>1,'datos'=>$datos));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/dia
     *
     * @return Response
     */
    public function postDia()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $dias = Dia::get(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$dias));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/horario
     *
     * @return Response
     */
    public function postHorario()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $horarios = Horario::get(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$horarios));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/horario_tipo
     *
     * @return Response
     */
    public function postHorariotipo()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $horarioTipo = HorarioTipo::get(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$horarioTipo));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/troba
     *
     * @return Response
     */
    public function postTroba()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $trobas = DigTroba::getTroba(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$trobas));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/amplificador
     *
     * @return Response
     */
    public function postAmplificador()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $troba = Input::get('troba');
            $nodo = Input::get('nodo');
            $amp = DigTroba::getAmp($troba , $nodo);
            return Response::json(array('rst'=>1,'datos'=>$amp));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/tap
     *
     * @return Response
     */
    public function postTap()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $amp = Input::get('amplificador');
            $troba = Input::get('troba');
            $nodo = Input::get('nodo');
            $tap = DigTroba::getTap($amp, $troba, $nodo);
            return Response::json(array('rst'=>1,'datos'=>$tap));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/cable
     *
     * @return Response
     */
    public function postCable()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $mdf = Input::get('mdf');
            $cables = DigTroba::getCable($mdf);
            return Response::json(array('rst'=>1,'datos'=>$cables));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/armario
     *
     * @return Response
     */
    public function postArmario()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $mdf = Input::get('mdf');
            $armarios = DigTroba::getArmario($mdf);
            return Response::json(array('rst'=>1,'datos'=>$armarios));
        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /lista/terminal
     *
     * @return Response
     */
    public function postTerminal()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $mdf = Input::get('mdf');
            if (Input::has('cable')) {
                $cable = Input::get('cable');
                $terminales = DigTroba::getTerminalCable($cable, $mdf);

            } elseif (Input::has('armario')) {
                $armario = Input::get('armario');
                $terminales = DigTroba::getTerminalArmario($armario, $mdf);

            }
            return Response::json(array('rst'=>1,'datos'=>$terminales));
        }
    }
}