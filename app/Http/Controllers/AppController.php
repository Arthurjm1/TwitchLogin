<?php

namespace App\Http\Controllers;

use App\Services\TwitchAPI;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function getHome()
    {
        if ($this->authValidate()) 
            return view('home');        
        else
            return redirect('/');
    }

    public function authValidate()
    {

        $loggedIn = false;

        if (session('user_logged')) {
            $loggedIn = true;
        }

        return $loggedIn;
    }
}
