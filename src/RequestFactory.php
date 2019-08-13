<?php
namespace PsrSwoole;

use Swoole\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

interface RequestFactory
{
    public function createRequest(Request $request): ServerRequestInterface;
    public function createUploadedFile(array $swooleUploadedFile): UploadedFileInterface;
    public function createUri(string $uri): UriInterface;
}
