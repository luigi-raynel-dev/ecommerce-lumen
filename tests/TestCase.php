<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{

    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * Authenticate before testing
     *
     * @return array
     */
    public function authenticate_before()
    {
        $this->post('/oauth/token',[
            "grant_type" => "password",
            "client_id" => $_ENV['CLIENT_ID'],
            "client_secret" => $_ENV['CLIENT_SECRET'],
            "username" => $_ENV['USER'],
            "password" => $_ENV['PASS'],
            "scope" => "*"
        ]);

        return ['HTTP_Authorization' => 'Bearer '.$this->getResponseDecode()->access_token];
    }

    /**
     * Return decoded response
     *
     * @return array
     */
    public function getResponseDecode()
    {
        return json_decode($this->response->getContent());   
    }

}
