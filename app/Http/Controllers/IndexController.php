<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        if (session('user_logged'))
            return redirect('/home');
        else
            return view('index');
    }
}
