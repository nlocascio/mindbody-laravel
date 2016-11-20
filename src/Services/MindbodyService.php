<?php

namespace Nlocascio\Mindbody\Services;

use BadMethodCallException;
use Config;
use InvalidArgumentException;
use Nlocascio\Mindbody\Services\MindbodyAPI;

class MindbodyService
{

    protected $mindbodyAPI;

    /**
     * MindbodyService constructor.
     * @param array $config
     */
    public function __construct($config)
    {
        if ( ! $config['site_ids'] || ! $config['source_credentials']['username'] || ! $config['source_credentials']['password'] ) {
            throw new InvalidArgumentException('Please set MINDBODY_SITEIDS, MINDBODY_SOURCE_USERNAME, MINDBODY_SOURCE_PASSWORD environment variables.');
        }

        $this->mindbodyAPI = new Mindbody(
            $config['wsdls'],
            $config['site_ids'],
            $config['source_credentials']['username'],
            $config['source_credentials']['password'],
            $config['user_credentials']['username'],
            $config['user_credentials']['password']
        );
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (is_callable([$this->mindbodyAPI, $method])) {
            return call_user_func_array([$this->mindbodyAPI, $method], $args);
        } else {
            throw new BadMethodCallException("Method $method does not exist.");
        }
    }
}