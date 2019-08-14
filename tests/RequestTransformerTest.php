<?php
namespace PsrSwoole\Testing;

use PHPUnit\Framework\TestCase;
use Dflydev\FigCookies\Cookies;
use Dflydev\FigCookies\FigRequestCookies;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Swoole\Http\Request;

use PsrSwoole\RequestTransformer;
use PsrSwoole\Factory\NyholmRequestFactory;

class RequestTransformerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->swooleRequest = $this->getMockBuilder(Request::class)->getMock();
        $this->swooleRequest->server = [
            'request_method' => 'get',
            'request_uri' => '/some-uri',
        ];
        $this->swooleRequest->header = [
            'host' => 'example.com',
        ];

        $this->requestTransformer = new RequestTransformer(
            new NyholmRequestFactory
        );
    }

    /**
     * @test
     */
    public function returnsPsrRequest()
    {
        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);
        $this->assertInstanceOf(ServerRequestInterface::class, $psrRequest);
    }

    /**
     * @test
     */
    public function bodyGetsCopiedCorrectly()
    {
        $bodyContent = 'This is my body.';
        $this->swooleRequest->expects($this->any())->method('rawContent')->willReturn($bodyContent);

        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);
        $this->assertSame($bodyContent, $psrRequest->getBody()->getContents());
    }

    /**
     * @test
     */
    public function headerGetsCopiedCorrectly()
    {
        $this->swooleRequest->header = array_merge(
            $this->swooleRequest->header,
            [
                'foo' => 'bar'
            ]
        );
        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);
        $this->assertEquals($this->swooleRequest->header['foo'], $psrRequest->getHeader('foo')[0]);
    }

    /**
     * @test
     */
    public function postDataGetsCopiedIfExistsAndIsMultipartFormData()
    {
        $this->swooleRequest->header = array_merge(
            $this->swooleRequest->header,
            [
                'content-type' => 'multipart/form-data',
                'foo' => 'bar'
            ]
        );
        $this->swooleRequest->post = [
            'foo' => 'bar'
        ];
        
        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);
        $this->assertSame([
            'foo' => 'bar'
        ], $psrRequest->getParsedBody());
    }

    /**
     * @test
     */
    public function postDataGetsCopiedIfExistsAndXWwwFormUrlEncoded()
    {
        $this->swooleRequest->header = array_merge(
            $this->swooleRequest->header,
            [
                'content-type' => 'application/x-www-form-urlencoded'
            ]
        );
        $this->swooleRequest->post = [
            'foo' => 'bar'
        ];

        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);
        $this->assertSame([
            'foo' => 'bar'
        ], $psrRequest->getParsedBody());
    }

    /**
     * @test
     */
    public function uploadedFilesAreCopiedProperty()
    {
        // Arrange
        $this->swooleRequest->header = array_merge(
            $this->swooleRequest->header,
            ['content-type' => 'multipart/form-data']
        );
        $this->swooleRequest->files = [
            'name1' => [
                'tmp_name' => 'tmp1',
                'name' => 'name1',
                'type' => 'type1',
                'size' => 77,
                'error' => 0
            ],
            'name2' => [
                'tmp_name' => 'tmp2',
                'name' => 'name2',
                'type' => 'type2',
                'size' => 88,
                'error' => 0
            ],
        ];

        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);

        $this->assertNotEmpty($psrRequest->getUploadedFiles());

        foreach ($psrRequest->getUploadedFiles() as $uploadedFile) {
            $this->assertInstanceOf(UploadedFileInterface::class, $uploadedFile);
        }

        $this->assertSame($psrRequest->getUploadedFiles()['name1']->getClientFilename(), 'name1');
        $this->assertSame($psrRequest->getUploadedFiles()['name1']->getClientMediaType(), 'type1');
        $this->assertSame($psrRequest->getUploadedFiles()['name1']->getError(), 0);
        $this->assertSame($psrRequest->getUploadedFiles()['name1']->getSize(), 77);
        $this->assertSame($psrRequest->getUploadedFiles()['name2']->getClientFilename(), 'name2');
        $this->assertSame($psrRequest->getUploadedFiles()['name2']->getClientMediaType(), 'type2');
        $this->assertSame($psrRequest->getUploadedFiles()['name2']->getError(), 0);
        $this->assertSame($psrRequest->getUploadedFiles()['name2']->getSize(), 88);
    }

    /**
     * @test
     */
    public function testCookiesAreCopiedProperly()
    {
        $this->swooleRequest->cookie = [
            'some-cookie-1' => 'some-value-1',
            'some-cookie-2' => 'some-value-2',
            'some-cookie-3' => 'some-value-3',
        ];

        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);

        $cookies = Cookies::fromRequest($psrRequest)->getAll();
        $this->assertEquals(count($cookies), 3);
        $this->assertEquals(FigRequestCookies::get($psrRequest, 'some-cookie-2')->getValue(), 'some-value-2');
    }

    /**
     * @test
     */
    public function hostIsCopiedProperly()
    {
        $psrRequest = $this->requestTransformer->toPsr($this->swooleRequest);
        $this->assertEquals($this->swooleRequest->header['host'], $psrRequest->getUri()->getHost());
    }
}
