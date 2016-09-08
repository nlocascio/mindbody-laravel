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
     * @param array|null $siteIds
     * @param null $sourceUsername
     * @param null $sourcePassword
     * @param null $userUsername
     * @param null $userPassword
     */
    public function __construct(array $siteIds = null, $sourceUsername = null, $sourcePassword = null, $userUsername = null, $userPassword = null)
    {
        $siteIds = $siteIds ?: array_map('intval', config('mindbody.siteids'));
        $sourceUsername = $sourceUsername ?: config('mindbody.source.username');
        $sourcePassword = $sourcePassword ?: config('mindbody.source.password');
        $userUsername = $userUsername ?: config('mindbody.user.username');
        $userPassword = $userPassword ?: config('mindbody.user.password');

        if (!$siteIds || !$sourceUsername || !$sourcePassword ) {
            throw new InvalidArgumentException('Please set MINDBODY_SITEIDS, MINDBODY_SOURCE_USERNAME, MINDBODY_SOURCE_PASSWORD environment variables.');
        }

        $this->mindbodyAPI = new MindbodyAPI($siteIds, $sourceUsername, $sourcePassword, $userUsername, $userPassword);
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