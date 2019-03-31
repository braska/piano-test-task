<?php

namespace Piano\Service\PianoApi;

use Psr\Http\Message\RequestInterface;

class PianoApiClient {
    private static $instance = null;
    private $client;

    public function __construct()
    {
        $AID = getenv('PIANO_AID');
        $API_TOKEN = getenv('PIANO_API_TOKEN');
        $BASE_URL = getenv('PIANO_BASE_URI') ?: 'https://sandbox.tinypass.com/api/v3/';

        if (!$AID || !$API_TOKEN) {
            throw new \Exception("Please, provide PIANO_AID and PIANO_API_TOKEN env vars");
        }

        $handler = new \GuzzleHttp\Handler\CurlHandler();
        $stack = \GuzzleHttp\HandlerStack::create($handler);

        $stack->push(\GuzzleHttp\Middleware::mapRequest(function (RequestInterface $request) use ($AID, $API_TOKEN) {
            if ('POST' !== $request->getMethod()) {
                return $request;
            }

            return new \GuzzleHttp\Psr7\Request(
                $request->getMethod(),
                $request->getUri(),
                $request->getHeaders() + ['Content-Type' => 'application/x-www-form-urlencoded'],
                \GuzzleHttp\Psr7\stream_for($request->getBody() . '&' . http_build_query([
                    'aid' => $AID,
                    'api_token' => $API_TOKEN
                ])),
                $request->getProtocolVersion()
            );
        }));

        $this->client = new \GuzzleHttp\Client(['handler' => $stack, 'base_uri' => $BASE_URL]);
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PianoApiClient();
        }

        return self::$instance;
    }

    public function request($url, $options)
    {
        return $this->client->post($url, $options);
    }
}
