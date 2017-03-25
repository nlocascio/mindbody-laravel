<?php

namespace Nlocascio\Mindbody\Services;

trait ProvidesMindbodyCredentials
{
    /**
     * Provide Request Credentials
     *
     * @return mixed
     */
    protected function getCredentials()
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
    protected function getSourceCredentials()
    {
        return [
            'SourceName' => $this->settings['source_credentials']['username'],
            'Password' => $this->settings['source_credentials']['password'],
            'SiteIDs'  => $this->settings['site_ids']
        ];
    }

    /**
     * @return array|null
     */
    protected function getUserCredentials()
    {
        return [
            'Username' => $this->settings['user_credentials']['username'],
            'Password' => $this->settings['user_credentials']['password'],
            'SiteIDs'  => $this->settings['site_ids']
        ];
    }
}