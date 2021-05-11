<?php
/**
 * @package        Auth0 Extension (joomla 3.x)
 * @copyright    Copyright (C) - http://www.auth0.com. All rights reserved.
 * @license        http://www.gnu.org/licenses/gpl-2.0-standalone.html
 * @author        Germán Lena
 * @download URL    http://www.auth0.com
 */
defined('_JEXEC') or die('Restricted access');

class Auth0Connect {

    protected $domain;
    protected $clientId;
    protected $clientSecret;
    protected $redirectURL;
    protected $http;

    public function __construct($domain, $clientId, $clientSecret, $redirectURL) {

        $this->domain = $domain;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectURL = $redirectURL;
        $this->http = new JHttp();

    }

    public function getAccessToken ($code) {

        $body = array(
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectURL,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code'
        );

        $headers = array(
            'content-type' => 'application/x-www-form-urlencoded'
        );

        $response = $this->http->post($this->domain . '/oauth/token', $body, $headers);

        $data = json_decode( $response->body );

        if (isset($data->access_token)) {
            return $data->access_token;
        }

        throw new Exception($data->error_description);
    }

    public function getUserInfo($accessToken) {

        $userData = $this->http->get($this->domain . '/userinfo/?access_token=' . $accessToken);
        $userInfo = json_decode( $userData->body );

        return $userInfo;

    }



    public function getToken($grantType = 'client_credentials')
    {
        $body = array(
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => $grantType,
            'audience' => 'https://truemetrics.auth0.com/api/v2/',
        );
        $headers = array(
            'content-type' => 'application/json'
        );

        $response = $this->http->post($this->domain . '/oauth/token', json_encode($body), $headers);

        $data = json_decode( $response->body );

        if (isset($data->access_token)) {
            return $data->access_token;
        }

        throw new Exception($data ? $data->error_description : 'Invalid headers');
    }

}
