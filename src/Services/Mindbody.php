<?php

namespace Nlocascio\Mindbody\Services;

use ErrorException;
use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;
use SoapClient;

class Mindbody {

    private $wsdls;
    private $siteIds;
    private $sourceUsername;
    private $sourcePassword;
    private $userUsername;
    private $userPassword;

    public function __construct(array $wsdls, array $siteIds, $sourceUsername, $sourcePassword, $userUsername = null, $userPassword = null)
    {
        $this->wsdls = $wsdls;
        $this->siteIds = $siteIds;
        $this->sourceUsername = $sourceUsername;
        $this->sourcePassword = $sourcePassword;
        $this->userUsername = $userUsername;
        $this->userPassword = $userPassword;
    }

    /**
     * Magic Method to call MINDBODY API
     *
     * @param $method
     * @param $parameters
     * @return mixed
     */
    public function __call($method, array $parameters = [])
    {
        $request = empty($parameters) ? [] : $parameters[0];

        $response = $this->callMindbodyApi($method, $request);

        $this->validateResponse($response);

        return $response;
    }

    /**
     * Set the MINDBODY SiteIDs
     *
     * @param array $siteIds
     * @return MindbodyAPI
     */
    public function setSiteIds(array $siteIds)
    {
        $this->siteIds = $siteIds;

        return $this;
    }

    /**
     * Set a single MINDBODY SiteID
     *
     * @param int $siteId
     * @return $this
     */
    public function setSiteId(int $siteId)
    {
        $this->setSiteIds([$siteId]);

        return $this;
    }

    /**
     * @param $methodName
     * @param $request
     * @return mixed
     */
    private function callMindbodyApi($methodName, array $request)
    {
        $client = new SoapClient($this->getWsdlForMethod($methodName));

        $response = $client->$methodName([
            'Request' => array_merge($this->getCredentials(), $request)
        ])->{$methodName . 'Result'};

        return $response;
    }

    /**
     * @param $methodName
     * @return mixed
     * @throws ErrorException
     */
    private function getWsdlForMethod($methodName)
    {
        foreach ($this->wsdls as $wsdl) {
            $client = new SoapClient($wsdl);

            if (str_contains(implode(" ", $client->__getFunctions()), " " . $methodName . "(")) {
                return $wsdl;
            }
        }

        throw new ErrorException("Called unknown MINDBODY API Method: $methodName");
    }

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

    /**
     * Get Request Credentials
     *
     * @return mixed
     */
    private function getCredentials()
    {
        $credentials['SourceCredentials'] = $this->getSourceCredentials();

        if ($this->getUserCredentials()) {
            $credentials['UserCredentials'] = $this->getUserCredentials();
        }

        return $credentials;
    }

    /**
     * @return array
     */
    private function getSourceCredentials()
    {
        return [
            'SourceName' => $this->sourceUsername,
            'Password'   => $this->sourcePassword,
            'SiteIDs'    => $this->siteIds
        ];
    }

    /**
     * @return array|null
     */
    private function getUserCredentials()
    {
        if ( ! $this->userUsername || ! $this->userPassword) {
            return null;
        };

        return [
            'Username' => $this->userUsername,
            'Password' => $this->userPassword,
            'SiteIDs'  => $this->siteIds
        ];
    }

}