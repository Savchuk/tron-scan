<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Library\TronTrade;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function tron()
    {

        $test = new TronTrade();

        exit;

        return view('home');
    }
}
