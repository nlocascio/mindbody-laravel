<?php

namespace Nlocascio\Mindbody;

use Nlocascio\Mindbody\Services\Mindbody;
use Nlocascio\Mindbody\Services\ValidatesApiResponses;

class Manager
{
    use ValidatesApiResponses;

    private $mindbodyService;
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
    public function __call($method, array $parameters = [])
    {
        $this->validateResponse(
            $response = $this->mindbodyService()->callMindbodyApi($method, $parameters[0] ?? [])
        );

        return $response;
    }

    /**
     * @return Mindbody
     */
    private function mindbodyService()
    {
        if ( ! $this->mindbodyService) {
            $this->mindbodyService = $this->setupMindbodyService();
        }

        return $this->mindbodyService;
    }

    /**
     * @return Mindbody
     */
    private function setupMindbodyService()
    {
        $settings = $this->getSettings();

        if (! $this->validateSettings($settings)) {
            throw new \InvalidArgumentException('Please set MINDBODY_SITEIDS, MINDBODY_SOURCENAME, MINDBODY_SOURCEPASSWORD environment variables.');
        };

        return new Mindbody($settings);
    }

    /**
     * @return mixed
     */
    private function getSettings()
    {
        return $this->settings[$this->connection];
    }

    /**
     * @param $settings
     * @return bool
     */
    private function validateSettings($settings)
    {
        return isset(
            $settings['site_ids'],
            $settings['source_credentials']['username'],
            $settings['source_credentials']['password']
        );
    }


}