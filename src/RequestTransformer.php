<?php
namespace PsrSwoole;

use Swoole\Http\Request;
use Dflydev\FigCookies\Cookie;
use Dflydev\FigCookies\FigRequestCookies;

class RequestTransformer
{
    public function __construct(RequestFactory $factory)
    {
        $this->factory = $factory;
    }

    public function toPsr(Request $swooleRequest)
    {
        $psrRequest = $this->factory->createRequest($swooleRequest);
        $psrRequest = $this->handlePostData($psrRequest, $swooleRequest);
        $psrRequest = $this->handleUploadedFiles($psrRequest, $swooleRequest);
        $psrRequest = $this->copyCookies($psrRequest, $swooleRequest);
        $psrRequest = $this->initUri($psrRequest, $swooleRequest);
        return $this->copyBody($psrRequest, $swooleRequest);
    }

    private function handlePostData($psrRequest, $swooleRequest)
    {
        if (empty($swooleRequest->post) || !is_array($swooleRequest->post)) {
            return $psrRequest;
        }

        return $psrRequest->withParsedBody($swooleRequest->post);
    }

    private function handleUploadedFiles($psrRequest, $swooleRequest)
    {
        if (empty($swooleRequest->files) || !is_array($swooleRequest->files)) {
            return $psrRequest;
        }

        $uploadedFiles = [];

        foreach ($swooleRequest->files as $key => $file) {
            $uploadedFiles[$key] = $this->factory->createUploadedFile($file);
        }

        return $psrRequest->withUploadedFiles($uploadedFiles);
    }

    private function copyCookies($psrRequest, $swooleRequest)
    {
        if (empty($swooleRequest->cookie)) {
            return $psrRequest;
        }

        foreach ($swooleRequest->cookie as $name => $value) {
            $cookie = Cookie::create($name, $value);
            $psrRequest = FigRequestCookies::set($psrRequest, $cookie);
        }

        return $psrRequest;
    }

    private function initUri($psrRequest, $swooleRequest)
    {
        $uri = '//' . $swooleRequest->header['host']
            . $swooleRequest->server['request_uri']
            . (isset($swooleRequest->server['query_string']) ? '?' . $swooleRequest->server['query_string'] : '')
            ;
        return $psrRequest->withUri($this->factory->createUri($uri));
    }

    private function copyBody($psrRequest, $swooleRequest)
    {
        if (empty($swooleRequest->rawContent())) {
            return $psrRequest;
        }

        $body = $psrRequest->getBody();
        $body->write($swooleRequest->rawContent());
        $body->rewind();

        return $psrRequest
            ->withBody($body)
        ;
    }
}
