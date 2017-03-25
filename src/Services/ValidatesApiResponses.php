<?php

namespace Nlocascio\Mindbody\Services;

use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;

trait ValidatesApiResponses
{
    /**
     * @param $response
     * @throws MindbodyErrorException
     */
    private function validateResponse($response)
    {
        if ($response->ErrorCode != 200) {
            throw new MindbodyErrorException("API Error $response->ErrorCode: $response->Message", $response->ErrorCode);
        }
    }
}