<?php
namespace PsrSwoole\Factory;

use PsrSwoole\RequestFactory;
use Swoole\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use GuzzleHttp\Psr7\ServerRequest as GuzzleRequest;
use GuzzleHttp\Psr7\Uri as GuzzleUri;
use GuzzleHttp\Psr7\UploadedFile as GuzzleUploadedFile;

class GuzzleRequestFactory implements RequestFactory
{
    public function createRequest(Request $request): ServerRequestInterface
    {
        return new GuzzleRequest(
            $request->server['request_method'],
            $this->initUri($request)
        );
    }

    private function initUri($swooleRequest)
    {
        return '//'
            . $swooleRequest->header['host']
            . $swooleRequest->server['request_uri']
            . (isset($swooleRequest->server['query_string']) 
                ? '?' . $swooleRequest->server['query_string'] 
                : '')
            ;
    }

    public function createUploadedFile(array $swooleUploadedFile): UploadedFileInterface
    {
        return new GuzzleUploadedFile(
            $swooleUploadedFile['tmp_name'],
            $swooleUploadedFile['size'],
            $swooleUploadedFile['error'],
            $swooleUploadedFile['name'],
            $swooleUploadedFile['type']
        );
    }

    public function createUri(string $uri): UriInterface
    {
        return new GuzzleUri($uri);
    }
}
