# psr7swoole
Library to convert Swoole requests to PSR requests and PSR responses to Swoole responses

## Overview

This library is based on [Slim Swoole](https://github.com/pachico/slim-swoole). 

## Install

composer require imefisto/psr-swoole

## Why to start a new library?

While [Slim Swoole](https://github.com/pachico/slim-swoole) assumes that you're using Slim 3, the PSR Swoole library pretends to be framework agnostic. You'll be able to convert Swoole requests to PSR requests, pass them to a some logic (ie: the slim framework) obtaining a PSR response and merging it with a Swoole response.

## Factories

In order to translate from Swoole to PSR request, factories has been implemented. Currently, [Guzzle](https://github.com/guzzle/psr7), [Nyholm](https://github.com/Nyholm/psr7) and [Slim](https://github.com/slimphp/Slim-Psr7) are supported (check Factory folder). Others can be added extending PsrSwoole\\RequestFactory.

Check the example with [Slim 4](https://github.com/slimphp/Slim).
