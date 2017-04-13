<?php

namespace Nlocascio\Mindbody;

use Nlocascio\Mindbody\Traits\ProvidesMindbodyCredentials;
use Nlocascio\Mindbody\Traits\ProvidesSoapClient;
use Nlocascio\Mindbody\Traits\ValidatesApiResponses;

class Mindbody
{
    use ValidatesApiResponses, ProvidesSoapClient, ProvidesMindbodyCredentials;

    private $connection;
    private $settings;

    /**
     * @param $connection
     * @param array $settings
     */
    public function __construct($connection, array $settings)
    {
        $this->connection = $connection;
        $this->settings = $settings;
    }

    /**
     * Fluent setter for the connection.
     *
     * @param null $connection
     * @return mixed
     */
    public function connection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @param $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        $this->validateSettings(
            $this->getSettings($this->connection)
        );

        $this->validateResponse(
            $response = $this->callMindbodyApi($method, $parameters[0] ?? [])
        );

        return $response;
    }

    /**
     * @param $methodName
     * @param $request
     * @return mixed
     */
    private function callMindbodyApi($methodName, array $request = [])
    {
        $client = $this->getSoapClientForMethod($methodName);

        $response = $client->$methodName([
            'Request' => array_merge($this->getCredentials(), $request)
        ])->{$methodName . 'Result'};

        return $response;
    }

    /**
     * @param $connection
     * @return mixed
     */
    private function getSettings($connection)
    {
        return $this->settings[$connection];
    }

    /**
     * @param $settings
     * @return bool
     */
    private function validateSettings($settings)
    {
        if ( ! isset(
            $settings['site_ids'],
            $settings['source_credentials']['username'],
            $settings['source_credentials']['password']
        )
        ) {
            throw new \InvalidArgumentException('Please set MINDBODY_SITEIDS, MINDBODY_SOURCENAME, MINDBODY_SOURCEPASSWORD environment variables.');
        }

        return true;
    }

}