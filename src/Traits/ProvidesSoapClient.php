<?php

namespace Nlocascio\Mindbody\Traits;

use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;

trait ProvidesSoapClient
{
    /**
     * @param $wsdl
     * @return \SoapClient
     */
    private function soapClient($wsdl)
    {
        return new \SoapClient($wsdl, $this->getSoapOptions());
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

    /**
     * @return mixed
     */
    public function getSoapOptions()
    {
        return config('mindbody.soap_options');
    }
}