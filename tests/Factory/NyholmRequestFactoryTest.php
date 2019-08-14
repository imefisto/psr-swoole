<?php
namespace PsrSwoole\Testing\Factory;

use PsrSwoole\Factory\NyholmRequestFactory;

class NyholmRequestFactoryTest extends RequestFactory
{
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new NyholmRequestFactory;
    }
}
