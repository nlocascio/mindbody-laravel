<?php

namespace Nlocascio\Mindbody\Services;

use ErrorException;
use \SoapClient;

//ini_set("user_agent", "FOOBAR");

class MindbodyAPI {

    protected $sourceCredentials = [];
    protected $userCredentials = [];
    protected $siteIds = [];

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
    public $debugSoapErrors = false;

    /**
     * @param array $siteIds
     * @param array $options
     */
    public function __construct(array $siteIds, array $options = ['debug => false'])
    {
        $this->siteIds = $siteIds;
        $this->debugSoapErrors = isset($options['debug']) ? $options['debug'] : false;

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
     * @param $sourceUsername
     * @param $sourcePassword
     * @return MindbodyAPI
     */
    public function setSourceCredentials($sourceUsername, $sourcePassword)
    {
        $this->sourceCredentials = [
            'SourceName' => $sourceUsername,
            'Password'   => $sourcePassword,
            'SiteIDs'    => $this->siteIds
        ];

        return $this;
    }

    /**
     * @param $userUsername
     * @param $userPassword
     * @return MindbodyAPI
     */
    public function setUserCredentials($userUsername, $userPassword)
    {
        $this->userCredentials = [
            'Username' => $userUsername,
            'Password'   => $userPassword,
            'SiteIDs'    => $this->siteIds
        ];

        return $this;
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
        $soapService = false;

        foreach ($this->apiMethods as $apiServiceName => $apiMethods)
        {
            if (in_array($name, $apiMethods))
            {
                $soapService = $apiServiceName;
            }
        }

        if (empty($soapService))
        {
            throw new ErrorException("Called unknown MINDBODY API method: $name");
        }

        if (empty($arguments))
        {
            return $this->callMindbodyService($soapService, $name);
        }

        switch (count($arguments))
        {
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
     * @param $serviceName          - Mindbody Soap service name
     * @param $methodName           - Mindbody API method name
     * @param array $requestData    - Optional: parameters to API methods
     * @param bool $returnObject    - Optional: Return the SOAP response object
     * @param bool $debugErrors
     * @return bool|mixed
     */
    protected function callMindbodyService($serviceName, $methodName, $requestData = array(), $returnObject = true, $debugErrors = true)
    {
        $request = array_merge(array('SourceCredentials' => $this->sourceCredentials), $requestData);

        if ( ! empty($this->userCredentials))
        {
            $request = array_merge(array('UserCredentials' => $this->userCredentials), $request);
        }

        $this->client = new SoapClient($this->apiServices[$serviceName], $this->soapOptions);

        try
        {
            $result = $this->client->$methodName(array("Request" => $request));

        } catch (SoapFault $s)
        {
            if ($this->debugSoapErrors && $debugErrors)
            {
                echo 'ERROR: [' . $s->faultcode . '] ' . $s->faultstring;
                $this->debug();

                return false;
            }
        } catch (Exception $e)
        {
            if ($this->debugSoapErrors && $debugErrors)
            {
                echo 'ERROR: ' . $e->getMessage();

                return false;
            }
        }

        if ($returnObject)
        {
            return $result;
        } else
        {
            return json_decode(json_encode($result), 1);
        }


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
     * @param $data
     * @return array
     */
    public function makeNumericArray($data)
    {
        if (is_array($data) && isset($data[0]))
        {
            return $data;
        } else
        {
            return array($data);
        }
    }

    /**
     *
     */
    private function setApiMethodsArray()
    {
        foreach ($this->apiServices as $serviceName => $serviceWSDL)
        {
            $client = new SoapClient($serviceWSDL, $this->soapOptions);

            $this->apiMethods = array_merge($this->apiMethods, array($serviceName => array_map(
                function ($n)
                {
                    $start = 1 + strpos($n, ' ');
                    $end = strpos($n, '(');
                    $length = $end - $start;

                    return substr($n, $start, $length);
                }, $client->__getFunctions()
            )));
        }
    }

//    public function sortClassesByDate(array $classes)
//    {
//        //Sort by datetime
//        usort($classes, function ($a, $b)
//        {
//            $t1 = strtotime($a->StartDateTime);
//            $t2 = strtotime($b->StartDateTime);
//
//            return $t1 - $t2;
//        });
//
//        return $classes;
//    }


//    public function replace_empty_arrays_with_nulls(array $array)
//    {
//        foreach ($array as &$value)
//        {
//            if (is_array($value))
//            {
//                if (empty($value))
//                {
//                    $value = null;
//                } else
//                {
//                    $value = $this->replace_empty_arrays_with_nulls($value);
//                }
//            }
//        }
//
//        return $array;
//    }

//    public function FunctionDataXml()
//    {
//        $passed = func_get_args();
//        $request = empty($passed[0]) ? null : $passed[0];
//        $returnObject = empty($passed[1]) ? null : $passed[1];
//        $debugErrors = empty($passed[2]) ? null : $passed[2];
//        $data = $this->callMindbodyService('DataService', 'FunctionDataXml', $request);
//        $xmlString = $this->getXMLResponse();
//        $sxe = new SimpleXMLElement($xmlString);
//        $sxe->registerXPathNamespace("mindbody", "http://clients.mindbodyonline.com/api/0_5");
//        $res = $sxe->xpath("//mindbody:FunctionDataXmlResponse");
//        if ($returnObject)
//        {
//            return $res[0];
//        } else
//        {
//            return $this->replace_empty_arrays_with_nulls(json_decode(json_encode($res[0]), 1));
//        }
//    }

    /*
    ** overrides SelectDataXml method to remove some invalid XML element names
    **
    ** string $query - a TSQL query
    */
//    public function SelectDataXml($query, $returnObject = false, $debugErrors = true)
//    {
//        $result = $this->callMindbodyService('DataService', 'SelectDataXml', array('SelectSql' => $query), $returnObject, $debugErrors);
//        $xmlString = $this->getXMLResponse();
//        // replace some invalid xml element names
//        $xmlString = str_replace("Current Series", "CurrentSeries", $xmlString);
//        $xmlString = str_replace("Item#", "ItemNum", $xmlString);
//        $xmlString = str_replace("Massage Therapist", "MassageTherapist", $xmlString);
//        $xmlString = str_replace("Workshop Instructor", "WorkshopInstructor", $xmlString);
//        $sxe = new SimpleXMLElement($xmlString);
//        $sxe->registerXPathNamespace("mindbody", "http://clients.mindbodyonline.com/api/0_5");
//        $res = $sxe->xpath("//mindbody:SelectDataXmlResponse");
//        if ($returnObject)
//        {
//            return $res[0];
//        } else
//        {
//            return $this->replace_empty_arrays_with_nulls(json_decode(json_encode($res[0]), 1));
//        }
//    }


}
