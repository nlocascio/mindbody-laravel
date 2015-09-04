<?php

namespace Nlocascio\Mindbody\Services;

use BadMethodCallException;
use Config;
use InvalidArgumentException;
use Nlocascio\Mindbody\Services\MindbodyAPI;

class MindbodyService {

    public function __construct()
    {
        $this->siteIds = array_map('intval', config('mindbody-laravel.siteids'));

        $this->sourceUsername = config('mindbody-laravel.source.username');
        $this->sourcePassword = config('mindbody-laravel.source.password');

        $this->userUsername = config('mindbody-laravel.user.username');
        $this->userPassword = config('mindbody-laravel.user.password');

        if ( ! $this->siteIds || ! $this->sourceUsername || $this->sourcePassword || $this->userUsername || $this->userPassword)
        {
            throw new InvalidArgumentException('Please set MINDBODY_SITEIDS, MINDBODY_SOURCE_USERNAME, MINDBODY_SOURCE_PASSWORD, MINDBODY_USER_USERNAME, MINDBODY_USER_PASSWORD environment variables.');
        }

        $this->mindbodyAPI = new MindbodyAPI($this->siteIds);
        $this->mindbodyAPI->setSourceCredentials($this->sourceUsername, $this->sourcePassword);
        $this->mindbodyAPI->setUserCredentials($this->userUsername, $this->userPassword);
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     */
    public function call($method, $args)
    {
        if (is_callable([$this->mindbodyAPI, $method]))
        {
            return call_user_func_array([$this->mindbodyAPI, $method], $args);
        } else {
            throw new BadMethodCallException("Method $method does not exist.");
        }
    }
}