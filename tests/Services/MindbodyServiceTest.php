<?php

/**
 * Created by PhpStorm.
 * User: nlocascio
 * Date: 11/18/16
 * Time: 12:51 PM
 */
class MindbodyServiceTest extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return ['Nlocascio\Mindbody\Providers\MindbodyServiceProvider'];
    }

    /** @test */
    public function it_creates_a_new_mindbody_service()
    {
//        $mindbodyservice = new \Nlocascio\Mindbody\Services\MindbodyService();
    }
}
