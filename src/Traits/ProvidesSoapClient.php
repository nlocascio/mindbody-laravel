<?php

namespace Nlocascio\Mindbody\Traits;

use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;

trait ProvidesSoapClient
{
    private $options = [
        'soap_version' => SOAP_1_1,
        'features'     => SOAP_SINGLE_ELEMENT_ARRAYS,
        'exceptions'   => true,
        'compression'  => SOAP_COMPRESSION_ACCEPT | SOAP_COMPRESSION_GZIP,
        'keep_alive'   => true,
        'trace'        => false,
        'encoding'     => 'UTF-8',
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
        foreach ($this->settings[$this->connection]['endpoints'] as $wsdl) {
            $client = $this->soapClient($wsdl);

            if (str_contains(implode(" ", $client->__getFunctions()), " " . $methodName . "(")) {
                return $client;
            }
        }

        throw new MindbodyErrorException("Called unknown MINDBODY API Method: $methodName");
    }
}