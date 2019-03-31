<?php

namespace Piano\Service\PianoApi;

use Psr\Http\Message\RequestInterface;

class PianoApiClient {
    private static $instance = null;
    private $client;

    public function __construct() {
        $handler = new \GuzzleHttp\Handler\CurlHandler();
        $stack = \GuzzleHttp\HandlerStack::create($handler);

        $stack->push(\GuzzleHttp\Middleware::mapRequest(function (RequestInterface $request) {
            if ('POST' !== $request->getMethod()) {
                return $request;
            }

            return new \GuzzleHttp\Psr7\Request(
                $request->getMethod(),
                $request->getUri(),
                $request->getHeaders() + ['Content-Type' => 'application/x-www-form-urlencoded'],
                \GuzzleHttp\Psr7\stream_for($request->getBody() . '&' . http_build_query([
                    'aid' => 'o1sRRZSLlw',
                    'api_token' => 'zziNT81wShznajW2BD5eLA4VCkmNJ88Guye7Sw4D'
                ])),
                $request->getProtocolVersion()
            );
        }));

        $this->client = new \GuzzleHttp\Client(['handler' => $stack, 'base_uri' => 'https://sandbox.tinypass.com/api/v3/']);
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PianoApiClient();
        }

        return self::$instance;
    }

    public function request($url, $options) {
        return $this->client->post($url, $options);
    }
}
