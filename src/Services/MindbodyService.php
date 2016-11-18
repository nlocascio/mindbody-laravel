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
    public function __construct(array $config)
    {
        if (!$config['site_ids'] || !$config['source_username'] || !$config['source_password'] ) {
            throw new InvalidArgumentException('Please set MINDBODY_SITEIDS, MINDBODY_SOURCE_USERNAME, MINDBODY_SOURCE_PASSWORD environment variables.');
        }

        $this->mindbodyAPI = new MindbodyAPI($config['site_ids'], $config['source_username'], $config['source_password'], $config['user_username'], $config['user_password']);
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