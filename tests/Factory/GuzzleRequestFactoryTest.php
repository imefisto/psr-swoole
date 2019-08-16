<?php
namespace PsrSwoole\Testing\Factory;

use PsrSwoole\Factory\GuzzleRequestFactory;

class GuzzleRequestFactoryTest extends RequestFactory
{
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new GuzzleRequestFactory;
    }
}
