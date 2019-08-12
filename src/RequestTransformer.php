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
        $psrRequest = $this->handlePostData($swooleRequest, $psrRequest);
        $psrRequest = $this->handleUploadedFiles($swooleRequest, $psrRequest);
        $psrRequest = $this->copyCookies($swooleRequest, $psrRequest);
        return $this->copyBody($psrRequest, $swooleRequest);
    }

    private function copyBody($psrRequest, $swooleRequest)
    {
        if (empty($swooleRequest->rawContent())) {
            return $psrRequest;
        }

        $body = $psrRequest->getBody();
        $body->write($swooleRequest->rawContent());
        $body->rewind();

        return $psrRequest->withBody($body);
    }

    private function handlePostData($swooleRequest, $psrRequest)
    {
        if (empty($swooleRequest->post) || !is_array($swooleRequest->post)) {
            return $psrRequest;
        }

        return $psrRequest->withParsedBody($swooleRequest->post);
    }

    private function handleUploadedFiles($swooleRequest, $psrRequest)
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

    private function copyCookies($swooleRequest, $psrRequest)
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
}
