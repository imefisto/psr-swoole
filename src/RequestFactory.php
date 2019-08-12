<?php
namespace PsrSwoole;

use Swoole\Http\Request;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;

interface RequestFactory
{
    public function createRequest(Request $request): ServerRequestInterface;
    public function createUploadedFile(array $swooleUploadedFile): UploadedFileInterface;
}
