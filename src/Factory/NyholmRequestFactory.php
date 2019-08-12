<?php
namespace PsrSwoole\Factory;

use PsrSwoole\RequestFactory;
use Swoole\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Nyholm\Psr7\ServerRequest as NyholmServerRequest;
use Nyholm\Psr7\UploadedFile as NyholmUploadedFile;

class NyholmRequestFactory implements RequestFactory
{
    public function createRequest(Request $request): ServerRequestInterface
    {
        return new NyholmServerRequest(
            $request->server['request_method'],
            $request->server['request_uri']
        );
    }

    public function createUploadedFile(array $swooleUploadedFile): UploadedFileInterface
    {
        return new NyholmUploadedFile(
            $swooleUploadedFile['tmp_name'],
            $swooleUploadedFile['size'],
            $swooleUploadedFile['error'],
            $swooleUploadedFile['name'],
            $swooleUploadedFile['type']
        );
    }
}
