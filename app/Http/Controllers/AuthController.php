<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TwitchAPI;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Env;

class AuthController extends Controller
{
    public function authenticate()
    {
        $twitchAPI = new TwitchAPI(env('TWITCH_CLIENT_ID'), env('TWITCH_CLIENT_SECRET'));
        return redirect($twitchAPI->getAuthCode());
    }

    public function login()
    {
        $twitchAPI = new TwitchAPI(env('TWITCH_CLIENT_ID'), env('TWITCH_CLIENT_SECRET'));
        $response = $twitchAPI->getAccessToken($_GET['code']);

        if ($response['status'] == 'ok') {

            $twitchAPI->__set('_accessToken', $response['response_data']['access_token'])->__set('_refreshToken', $response['response_data']['refresh_token']);
            $userInfo = $twitchAPI->getUserInfo();            

            if ($userInfo['status'] == 'ok') {
                return $this->registerUser($userInfo, $twitchAPI);
            }
        }
    }

    public function registerUser($userInfo, $twitchAPI)
    {
        $user = new User();
        $username = $userInfo['response_data']['data'][0]['display_name'];
        $useremail = $userInfo['response_data']['data'][0]['email'];
        $userAccessToken = $twitchAPI->__get('_accessToken');
        $userRefreshToken = $twitchAPI->__get('_refreshToken');

        session(['username' => $username]);
        session(['access_token' => $userAccessToken]);

        $registeredUser = User::where('email', $useremail)->first();        

        if (empty($registeredUser)) {
            $user->username = $username;
            $user->email = $useremail;
            $user->accessToken = $userAccessToken;
            $user->refreshToken = $userRefreshToken;
            $user->save();
            session(['user_already_registered' => false]);
        } else {
            $registeredUser->username = $username;
            $registeredUser->accessToken = $userAccessToken;
            $registeredUser->refreshToken = $userRefreshToken;
            $registeredUser->save();
            session(['user_already_registered' => true]);            
        }
        session(['user_logged' => true]);
        
        return redirect('/home');
    }

    public function logout()
    {

        $userAccessToken = session('access_token');

        $twitchAPI = new TwitchAPI(env('TWITCH_CLIENT_ID'), env('TWITCH_CLIENT_SECRET'));
        $twitchAPI->__set('_accessToken', $userAccessToken);
        $twitchAPI->revokeAccessToken();

        session()->flush();

        return redirect('/');
    }
}
