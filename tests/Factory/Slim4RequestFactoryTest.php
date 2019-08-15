<?php
namespace PsrSwoole\Testing\Factory;

use PsrSwoole\Factory\Slim4RequestFactory;

class Slim4RequestFactoryTest extends RequestFactory
{
    public function setUp(): void
    {
        parent::setUp();
        $this->factory = new Slim4RequestFactory;
    }
}
