<?php

namespace Nlocascio\Mindbody\Traits;

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
            'SourceName' => $this->settings[$this->connection]['source_credentials']['username'],
            'Password' => $this->settings[$this->connection]['source_credentials']['password'],
            'SiteIDs'  => $this->settings[$this->connection]['site_ids']
        ];
    }

    /**
     * @return array|null
     */
    protected function getUserCredentials()
    {
        return [
            'Username' => $this->settings[$this->connection]['user_credentials']['username'],
            'Password' => $this->settings[$this->connection]['user_credentials']['password'],
            'SiteIDs'  => $this->settings[$this->connection]['site_ids']
        ];
    }
}