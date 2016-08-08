<?php

namespace Nlocascio\Mindbody\Services;

use ErrorException;
use \SoapClient;

class MindbodyAPI
{

    protected $appointmentServiceWSDL = "https://api.mindbodyonline.com/0_5/AppointmentService.asmx?WSDL";
    protected $classServiceWSDL = "https://api.mindbodyonline.com/0_5/ClassService.asmx?WSDL";
    protected $clientServiceWSDL = "https://api.mindbodyonline.com/0_5/ClientService.asmx?WSDL";
    protected $dataServiceWSDL = "https://api.mindbodyonline.com/0_5/DataService.asmx?WSDL";
    protected $finderServiceWSDL = "https://api.mindbodyonline.com/0_5/FinderService.asmx?WSDL";
    protected $saleServiceWSDL = "https://api.mindbodyonline.com/0_5/SaleService.asmx?WSDL";
    protected $siteServiceWSDL = "https://api.mindbodyonline.com/0_5/SiteService.asmx?WSDL";
    protected $staffServiceWSDL = "https://api.mindbodyonline.com/0_5/StaffService.asmx?WSDL";

    protected $apiMethods = [];
    protected $apiServices = [];

    public $soapOptions = array('soap_version' => SOAP_1_1, 'trace' => true);

    private $siteIds = [];
    private $sourceUsername;
    private $sourcePassword;
    private $userUsername;
    private $userPassword;
    private $debugSoapErrors = false;
    private $client;

    /**
     * @param array $siteIds
     * @param $sourceUsername
     * @param $sourcePassword
     * @param $userUsername
     * @param $userPassword
     * @param bool $debugSoapErrors
     */
    public function __construct(array $siteIds, $sourceUsername, $sourcePassword, $userUsername = null, $userPassword = null, $debugSoapErrors = false)
    {
        $this->siteIds = $siteIds;
        $this->sourceUsername = $sourceUsername;
        $this->sourcePassword = $sourcePassword;
        $this->userUsername = $userUsername;
        $this->userPassword = $userPassword;
        $this->debugSoapErrors = $debugSoapErrors;

        // set apiServices array with Mindbody WSDL locations
        $this->apiServices = [
            'AppointmentService' => $this->appointmentServiceWSDL,
            'ClassService'       => $this->classServiceWSDL,
            'ClientService'      => $this->clientServiceWSDL,
            'DataService'        => $this->dataServiceWSDL,
            'FinderService'      => $this->finderServiceWSDL,
            'SaleService'        => $this->saleServiceWSDL,
            'SiteService'        => $this->siteServiceWSDL,
            'StaffService'       => $this->staffServiceWSDL
        ];

        $this->setApiMethodsArray();
    }

    /**
     * magic method will search $this->apiMethods array for $name and call the
     * appropriate Mindbody API method if found
     *
     * @param $name
     * @param $arguments
     * @return bool|mixed
     * @throws ErrorException
     */
    public function __call($name, $arguments)
    {
        // check if method exists on one of mindbody's soap services
        $soapService = null;

        foreach ($this->apiMethods as $apiServiceName => $apiMethods) {
            if (in_array($name, $apiMethods)) {
                $soapService = $apiServiceName;
            }
        }

        if ($soapService == null) {
            throw new ErrorException("Called unknown MINDBODY API method: $name");
        }

        if (empty($arguments)) {
            return $this->callMindbodyService($soapService, $name);
        }

        switch (count($arguments)) {
            case 1:
                return $this->callMindbodyService($soapService, $name, $arguments[0]);
            case 2:
                return $this->callMindbodyService($soapService, $name, $arguments[0], $arguments[1]);
            case 3:
                return $this->callMindbodyService($soapService, $name, $arguments[0], $arguments[1], $arguments[2]);
        }

    }

    /**
     * return the results of a Mindbody API method
     *
     * @param $serviceName - Mindbody Soap service name
     * @param $methodName - Mindbody API method name
     * @param array $request
     * @param bool $debugErrors
     * @return bool|mixed
     * @internal param array $requestData - Optional: parameters to API methods
     */
    protected function callMindbodyService($serviceName, $methodName, array $request = [], $debugErrors = false)
    {
        $request = $this->addCredentials($request);

        $this->client = new SoapClient($this->apiServices[$serviceName], $this->soapOptions);

        try {
            $result = $this->client->$methodName(unserialize(serialize(array("Request" => $request))));

        } catch (SoapFault $s) {
            if ($this->debugSoapErrors && $debugErrors) {
                echo 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
                $this->debug();

                return false;
            }
        } catch (Exception $e) {
            if ($this->debugSoapErrors && $debugErrors) {
                echo 'ERROR: ' . $e->getMessage();

                return false;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getXMLRequest()
    {
        return $this->client->__getLastRequest();
    }

    /**
     * @return string
     */
    public function getXMLResponse()
    {
        return $this->client->__getLastResponse();
    }

    /**
     *
     */
    public function debug()
    {
        echo "<textarea>" . print_r($this->getXMLRequest(), 1) . "</textarea>";
        echo "<textarea>" . print_r($this->getXMLResponse(), 1) . "</textarea>";
    }

    /**
     *
     */
    private function setApiMethodsArray()
    {
        foreach ($this->apiServices as $serviceName => $serviceWSDL) {
            $client = new SoapClient($serviceWSDL, $this->soapOptions);

            $this->apiMethods = array_merge($this->apiMethods, array($serviceName => array_map(
                function ($n) {
                    $start = 1 + strpos($n, ' ');
                    $end = strpos($n, '(');
                    $length = $end - $start;

                    return substr($n, $start, $length);
                }, $client->__getFunctions()
            )));
        }
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

    private function getUserCredentials()
    {
        return [
            'Username' => $this->userUsername,
            'Password' => $this->userPassword,
            'SiteIDs'  => $this->siteIds
        ];
    }

    /**
     * @param $requestData
     * @return array
     */
    protected function addSourceCredentials($requestData)
    {
        $request = array_merge(array('SourceCredentials' => $this->getSourceCredentials()), $requestData);

        return $request;
    }

    /**
     * @param $request
     * @return array
     */
    protected function addUserCredentials($request)
    {
        $request = array_merge(array('UserCredentials' => $this->getUserCredentials()), $request);

        return $request;
    }

    /**
     * @param $request
     * @return array
     * @internal param $requestData
     */
    protected function addCredentials($request)
    {
        $request = $this->addSourceCredentials($request);

        if ($this->userUsername && $this->userPassword) {
            $request = $this->addUserCredentials($request);
        }

        return $request;
    }

    /**
     * @param array $siteIds
     * @return MindbodyAPI
     */
    public function setSiteIds(array $siteIds)
    {
        $this->siteIds = $siteIds;

        return $this;
    }

}
