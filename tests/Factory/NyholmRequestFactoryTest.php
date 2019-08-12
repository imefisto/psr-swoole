<?php
namespace PsrSwoole\Testing\Factory;

use PHPUnit\Framework\TestCase;
use Swoole\Http\Request;
use PsrSwoole\Factory\NyholmRequestFactory;
use Nyholm\Psr7\ServerRequest as NyholmServerRequest;
use Nyholm\Psr7\UploadedFile as NyholmUploadedFile;

class NyholmRequestFactoryTest extends TestCase
{
    public function setUp(): void
    {
        $this->swooleRequest = $this->getMockBuilder(Request::class)->getMock();
        $this->swooleRequest->server = [
            'request_method' => 'get',
            'request_uri' => 'some-uri',
        ];
        $this->factory = new NyholmRequestFactory;
    }

    /**
     * @test
     */
    public function createsNyholmPsrRequest()
    {
        $psrRequest = $this->factory->createRequest($this->swooleRequest);
        $this->assertInstanceOf(NyholmServerRequest::class, $psrRequest);
    }

    /**
     * @test
     */
    public function createsNyholmPsrUploadedFile()
    {
        $swooleUploadedFile = [
            'tmp_name' => 'tmp1',
            'name' => 'name1',
            'type' => 'type1',
            'size' => 77,
            'error' => 0
        ];

        $uploadedFile = $this->factory->createUploadedFile($swooleUploadedFile);
        $this->assertInstanceOf(
            NyholmUploadedFile::class,
            $uploadedFile
        );

        $this->assertEquals($swooleUploadedFile['size'], $uploadedFile->getSize());
        $this->assertEquals($swooleUploadedFile['name'], $uploadedFile->getClientFilename());
        $this->assertEquals($swooleUploadedFile['type'], $uploadedFile->getClientMediaType());
        $this->assertEquals($swooleUploadedFile['error'], $uploadedFile->getError());
    }
}
