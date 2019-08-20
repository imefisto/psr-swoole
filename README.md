# psr7swoole
Convenient library to convert Swoole requests to PSR requests and PSR responses to Swoole responses

## Overview

This library is based on [Slim Swoole](https://github.com/pachico/slim-swoole). 

## Why to start a new library?

While [Slim Swoole](https://github.com/pachico/slim-swoole) assumes that you're using Slim 3, the PSR Swoole library pretends to be framework agnostic. You'll be able to convert Swoole requests to PSR requests, pass them to a some logic (ie: the slim framework) obtaining a PSR response and merging it with a Swoole response.

Check the example with [Slim 4](https://github.com/slimphp/Slim).
