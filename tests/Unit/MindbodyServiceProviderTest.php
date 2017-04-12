<?php

namespace Nlocascio\Mindbody\Tests;

use Nlocascio\Mindbody\Mindbody;
use Nlocascio\Mindbody\MindbodyServiceProvider;
use Nlocascio\Mindbody\Tests\TestCase as BaseTestCase;

class MindbodyServiceProviderTest extends BaseTestCase
{
    /** @test */
    public function it_provides_the_mindbody_class()
    {
        $serviceProvider = new MindbodyServiceProvider(null);

        $this->assertEquals($serviceProvider->provides(), [Mindbody::class]);
    }
}