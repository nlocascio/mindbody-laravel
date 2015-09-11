<?php

namespace Nlocascio\Mindbody\Services;

use BadMethodCallException;
use Config;
use InvalidArgumentException;
use Nlocascio\Mindbody\Services\MindbodyAPI;

class MindbodyService {

    public function __construct(array $siteIds = null, array $sourceCredentials = ['username' => null, 'password' => null], array $userCredentials = ['username' => null, 'password' => null])
    {
        // You can either pass in your MINDBODY siteIds as an array, OR set a default in Config/Env.
        $this->siteIds = (isset($siteIds)) ? $siteIds : array_map('intval', config('mindbody-laravel.siteids'));

        $this->sourceUsername = $sourceCredentials['username'] ?: config('mindbody-laravel.source.username');
        $this->sourcePassword = $sourceCredentials['password'] ?: config('mindbody-laravel.source.password');

        $this->userUsername = $userCredentials['username'] ?: $sourceCredentials['username'] ? '_'.$sourceCredentials['username'] : config('mindbody-laravel.user.username');
        $this->userPassword = $userCredentials['password'] ?: $sourceCredentials['password'] ?: config('mindbody-laravel.user.password');

        if ( ! $this->siteIds || ! $this->sourceUsername || ! $this->sourcePassword || ! $this->userUsername || ! $this->userPassword)
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
    public function __call($method, $args)
    {
        if (is_callable([$this->mindbodyAPI, $method]))
        {
            return call_user_func_array([$this->mindbodyAPI, $method], $args);
        } else {
            throw new BadMethodCallException("Method $method does not exist.");
        }
    }
}