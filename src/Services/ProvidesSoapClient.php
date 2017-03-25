<?php

namespace Nlocascio\Mindbody\Services;

use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;

trait ProvidesSoapClient
{
    private $options = [
        'soap_version' => SOAP_1_1,
        'cache_wsdl'   => WSDL_CACHE_BOTH,
        'features'     => SOAP_SINGLE_ELEMENT_ARRAYS,
        'exceptions'   => true,
    ];

    /**
     * @param $wsdl
     * @return \SoapClient
     */
    private function soapClient($wsdl)
    {
        return new \SoapClient($wsdl, $this->options);
    }

    /**
     * @param $methodName
     * @return \SoapClient
     * @throws MindbodyErrorException
     */
    private function getSoapClientForMethod($methodName)
    {
        foreach ($this->settings['endpoints'] as $wsdl) {
            $client = $this->soapClient($wsdl);

            if (str_contains(implode(" ", $client->__getFunctions()), " " . $methodName . "(")) {
                return $client;
            }
        }

        throw new MindbodyErrorException("Called unknown MINDBODY API Method: $methodName");
    }
}