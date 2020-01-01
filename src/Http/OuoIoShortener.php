<?php

namespace Media365\Squeezer\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Media365\Squeezer\Contracts\Shortener;

class OuoIoShortener implements Shortener
{
    protected $client;
    protected $defaults;
    protected $token;

    /**
     * Create a new Ouo.io shortener.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param string $token
     * @return void
     */
    public function __construct(ClientInterface $client, string $token)
    {
        $this->client = $client;
        $this->defaults = [
            'allow_redirects' => false,
            'base_uri' => 'https://ouo.io',
        ];
        $this->token = $token;
    }

    /**
     * {@inheritDoc}
     */
    public function shorten($url, array $options = [])
    {
        $options = array_merge(Arr::add($this->defaults, 'query.s', $url), $options);
        $request = new Request('GET', "/api/$this->token");

        $response = $this->client->send($request, $options);

        return $response->getBody()->getContents();
    }
}
