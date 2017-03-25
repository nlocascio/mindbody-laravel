<?php

namespace Nlocascio\Mindbody\Tests;

use Nlocascio\Mindbody\MindbodyServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Dotenv\Dotenv;

abstract class TestCase extends BaseTestCase
{
    public function setUp()
    {
        $this->loadEnvironmentVariables();

        parent::setUp();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [MindbodyServiceProvider::class];
    }

    /**
     *
     */
    private function loadEnvironmentVariables()
    {
        $dotenv = new Dotenv(__DIR__ . '/../');
        $dotenv->load();
        $dotenv->required(['MINDBODY_SITEIDS', 'MINDBODY_SOURCENAME', 'MINDBODY_SOURCEPASSWORD']);
    }
}