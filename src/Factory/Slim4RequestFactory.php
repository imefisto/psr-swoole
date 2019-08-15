<?php
namespace PsrSwoole\Factory;

use PsrSwoole\RequestFactory;
use Swoole\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;
use Slim\Psr7\Request as SlimRequest;
use Slim\Psr7\Uri as SlimUri;
use Slim\Psr7\Headers as SlimHeaders;
use Slim\Psr7\UploadedFile as SlimUploadedFile;
use Slim\Psr7\Stream as SlimStream;

class Slim4RequestFactory implements RequestFactory
{
    public function createRequest(Request $request): ServerRequestInterface
    {
        return new SlimRequest(
            $request->server['request_method'],
            $this->initUri($request),
            new SlimHeaders,
            [],
            [],
            $this->initStream()
        );
    }

    private function initUri($swooleRequest)
    {
        $hostHeader = explode(':', $swooleRequest->header['host']);
        $queryString = isset($swooleRequest->server['query_string'])
            ? '?' . $swooleRequest->server['query_string'] 
            : ''
            ;

        return new SlimUri(
            '',
            $hostHeader[0],
            isset($hostHeader[1]) ? $hostHeader[1] : null,
            $swooleRequest->server['request_uri'],
            $queryString,
            ''
        );
    }

    private function initStream()
    {
        $stream = fopen('php://temp', 'w+');
        stream_copy_to_stream(fopen('php://input', 'r'), $stream);
        rewind($stream);
        return new SlimStream($stream);
    }

    public function createUploadedFile(array $swooleUploadedFile): UploadedFileInterface
    {
        return new SlimUploadedFile(
            $swooleUploadedFile['tmp_name'],
            $swooleUploadedFile['name'],
            $swooleUploadedFile['type'],
            $swooleUploadedFile['size'],
            $swooleUploadedFile['error']
        );
    }

    public function createUri(string $uri): UriInterface
    {
        $parts = parse_url($uri);
        return new SlimUri(
            isset($parts['scheme']) ? $parts['scheme'] : '',
            isset($parts['host']) ? $parts['host'] : '',
            isset($parts['port']) ? $parts['port'] : null,
            isset($parts['path']) ? $parts['path'] : '',
            isset($parts['query']) ? $parts['query'] : '',
            isset($parts['fragment']) ? $parts['fragment'] : '',
            isset($parts['user']) ? $parts['user'] : '',
            isset($parts['pass']) ? $parts['pass'] : ''
        );
    }
}
