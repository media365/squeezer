<?php

namespace Media365\Squeezer\Http;

use GuzzleHttp\ClientInterface;
use Media365\Squeezer\Contracts\Shortener;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;

class BitLyShortener implements Shortener
{
    protected $client;
    protected $defaults;

    /**
     * Create a new Bit.ly shortener.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param string $token
     * @param string $domain
     * @return void
     */
    public function __construct(ClientInterface $client, string $token, string $domain)
    {
        $this->client = $client;
        $this->defaults = [
            'allow_redirects' => false,
            'base_uri' => 'https://api-ssl.bitly.com',
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => "Bearer $token",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'domain' => $domain,
            ],
        ];
    }

    public function shorten($url, array $options = [])
    {
        $options = array_merge_recursive(Arr::add($this->defaults, 'json.long_url', $url), $options);
        $request = new Request('POST', '/v4/shorten');

        $response = $this->client->send($request, $options);

        return json_decode($response->getBody()->getContents())->link;
    }
}
