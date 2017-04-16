<?php

namespace Nlocascio\Mindbody\Tests;

use InvalidArgumentException;
use Nlocascio\Mindbody\Exceptions\MindbodyErrorException;
use Nlocascio\Mindbody\Mindbody;
use Nlocascio\Mindbody\Tests\TestCase as BaseTestCase;

class MindbodyTest extends BaseTestCase
{
    /**
     * Integration tests for the package. Most of these tests WILL hit the MINDBODY API when called!
     */

    /** @test */
    public function it_calls_mindbody()
    {
        $mindbody = $this->app->make(Mindbody::class);

        $response = $mindbody->GetSites();

        $this->assertEquals($response->Status, 'Success');
    }

    /** @test */
    public function it_calls_mindbody_with_arguments()
    {
        $mindbody = $this->app->make(Mindbody::class);

        $response = $mindbody->GetSites([
            'XMLDetail' => 'Full'
        ]);

        $response2 = $mindbody->GetSites([
            'XMLDetail' => 'Bare'
        ]);

        $this->assertEquals($response->Status, 'Success');
        $this->assertEquals($response->XMLDetail, 'Full');
        $this->assertEquals($response2->Status, 'Success');
        $this->assertEquals($response2->XMLDetail, 'Bare');
    }

    /** @test */
    public function it_throws_an_exception_if_environment_variables_are_not_set()
    {
        config(['mindbody.connections.mindbody' => []]);

        $mindbody = $this->app->make(Mindbody::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Please set MINDBODY_SITEIDS, MINDBODY_SOURCENAME, MINDBODY_SOURCEPASSWORD environment variables.");

        $mindbody->GetSites();
    }

    /** @test */
    public function it_throws_an_exception_on_unknown_method()
    {
        $mindbody = $this->app->make(Mindbody::class);

        $this->expectException(MindbodyErrorException::class);
        $this->expectExceptionMessage("Called unknown MINDBODY API Method: ThatsNotAMethod");

        $mindbody->ThatsNotAMethod();
    }

    /** @test */
    public function it_throws_an_exception_on_bad_api_call()
    {
        config(['mindbody.connections.mindbody.source_credentials.password' => 'invalid_key']);

        $mindbody = $this->app->make(Mindbody::class);

        $this->expectException(MindbodyErrorException::class);
        $this->expectExceptionMessage("API Error 101: Invalid sourcename or password.");

        $mindbody->GetSites();
    }

    /** @test */
    public function it_allows_a_connection_to_be_configured_on_the_fly()
    {
        config(['mindbody.connections.new_connection' => config('mindbody.connections.mindbody')]);

        $mindbody = $this->app->make(Mindbody::class);

        $response = $mindbody->connection('new_connection')->GetSites();

        $this->assertEquals($response->Status, 'Success');
    }

    /** @test */
    public function it_retrieves_all_clients()
    {
        $mindbody = $this->app->make(Mindbody::class);

        $response = $mindbody->GetClients([
            'XMLDetail' => 'Bare',
            'PageSize' => 500,
            'Fields'            => [
                'Clients.FirstName',
                'Clients.LastName'
            ],
            'SearchText' => ''
        ]);

        $this->assertCount(500, $response->Clients->Client);
    }
}