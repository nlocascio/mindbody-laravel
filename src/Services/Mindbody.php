<?php

namespace Nlocascio\Mindbody\Services;

use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;
use SoapClient;

class Mindbody {
    use ProvidesSoapClient, ProvidesMindbodyCredentials;

    private $settings;

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @param $methodName
     * @param $request
     * @return mixed
     */
    public function callMindbodyApi($methodName, array $request = [])
    {
        $client = $this->getSoapClientForMethod($methodName);

        $response = $client->$methodName([
            'Request' => array_merge($this->getCredentials(), $request)
        ])->{$methodName . 'Result'};

        return $response;
    }

}