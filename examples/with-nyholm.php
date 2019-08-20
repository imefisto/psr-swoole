<?php
require __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use Nyholm\Psr7\Factory\Psr17Factory;

$http = new Swoole\Http\Server("127.0.0.1", 9501, SWOOLE_PROCESS);

// define slim app
$app = AppFactory::create(new Psr17Factory);
$app->get('/', function ($request, $response, $args) {
    $response->getBody()->write('Hello world!');
    return $response;
});

$requestTransformer = new \PsrSwoole\RequestTransformer(
    new \PsrSwoole\Factory\NyholmRequestFactory
);

$responseMerger = new \PsrSwoole\ResponseMerger;

$http->on("start", function ($server) {
    echo "Swoole http server is started at http://127.0.0.1:9501\n";
});

$http->on("request", function ($request, $response) use ($app, $requestTransformer, $responseMerger) {
    $psrRequest = $requestTransformer->toPsr($request);
    $psrResponse = $app->handle($psrRequest);

    return $responseMerger
        ->toSwoole($psrResponse, $response)
        ->end();
});

$http->start();
