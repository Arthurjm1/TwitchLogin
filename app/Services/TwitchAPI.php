<?php

namespace App\Services;

use Illuminate\Http\Response;

class TwitchAPI
{

    const TWITCH_ID_DOMAIN = 'https://id.twitch.tv/';
    const TWITCH_API_DOMAIN = 'https://api.twitch.tv/helix/';

    private $_clientId;
    private $_clientSecret;
    private $_accessToken;
    private $_refreshToken;

    public function __construct($clientId, $clientSecret, $accessToken = '')
    {
        $this->_clientId = $clientId;
        $this->_clientSecret = $clientSecret;
        $this->_accessToken = $accessToken;
    }

    public function __get($attr)
    {
        return $this->$attr;
    }

    public function __set($attr, $value)
    {
        $this->$attr = $value;
        return $this;
    }

    public function getAuthCode()
    {

        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/authorize?';

        $params = [
            'client_id' => $this->_clientId,
            'redirect_uri' => env('TWITCH_REDIRECT_URI'),
            'response_type' => 'code',
            'scope' => 'user:read:email'

        ];

        return $endpoint . http_build_query($params);
    }

    public function getAccessToken($code)
    {
        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/token?';

        $params = [
            'endpoint' => $endpoint,
            'type' => 'POST',
            'url_params' => [
                'client_id' => $this->_clientId,
                'client_secret' => $this->_clientSecret,
                'code' => $code,
                'grant_type' => 'authorization_code',
                'redirect_uri' => env('TWITCH_REDIRECT_URI')
            ]
        ];

        return $this->makeAPICall($params);
    }

    public function makeAPICall($params)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $params['endpoint']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if (isset($params['authorization'])) {
            curl_setopt($curl, CURLOPT_HEADER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $params['authorization']);
        }

        if ($params['type'] == 'POST') {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params['url_params']));
        } else if ($params['type'] == 'GET') {
            curl_setopt($curl, CURLOPT_URL, $params['endpoint'] . http_build_query($params['url_params']));
        }

        $response = curl_exec($curl);

        if (isset($params['authorization'])) {
            $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
            $responseBody = substr($response, $headerSize);
            $response = json_decode($responseBody, true);
        } else {
            $response = json_decode($response, true);
        }
        curl_close($curl);

        return [
            'status' => isset($response['status']) ? $response['status'] : 'ok',
            'message' => isset($response['message']) ? $response['message'] : '',
            'response_data' => $response,
            'endpoint' => $params['endpoint'],
            'url_params' => $params['url_params']
        ];
    }

    public function getAuthHeaders()
    {
        return [
            'Authorization: Bearer ' . $this->__get('_accessToken'),
            'Client-Id: ' . env('TWITCH_CLIENT_ID')
        ];
    }

    public function getUserInfo()
    {

        $endpoint = self::TWITCH_API_DOMAIN . 'users';

        $params = [
            'endpoint' => $endpoint,
            'type' => 'GET',
            'url_params' => [],
            'authorization' => $this->getAuthHeaders()
        ];

        return $this->makeAPICall($params);
    }

    public function revokeAccessToken()
    {

        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/revoke?';

        $params = [
            'endpoint' => $endpoint . http_build_query([
                'client_id' => env('TWITCH_CLIENT_ID'),
                'token' => $this->__get('_accessToken')
            ]),
            'type' => 'POST',
            'url_params' => []
        ];        

        return $this->makeAPICall($params);
    }

    public function refreshAccessToken(){

        $endpoint = self::TWITCH_ID_DOMAIN . 'oauth2/token';
        
        $params = [
            'endpoint' => $endpoint,
            'type' => 'POST',
            'url_params' => [
                'grant_type' => 'refresh_token',
                'refresh_token' => $this->_refreshToken,
                'client_id' => env('TWITCH_CLIENT_ID'),
                'client_secret' => env('TWITCH_CLIENT_SECRET')
            ]
        ];

        return $this->makeAPICall($params);
    }
}
