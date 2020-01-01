<?php

namespace Media365\Squeezer\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Media365\Squeezer\Contracts\Shortener;

class TinyUrlShortener implements Shortener
{
    protected $client;
    protected const defaults = [
        'allow_redirects' => false,
        'base_uri' => 'https://tinyurl.com',
    ];

    /**
     * Create a new TinyURL shortener.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @return void
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }
    
    public function shorten($url, array $options = [])
    {
        $options = array_merge(Arr::add(static::defaults, 'query.url', $url), $options);
        $request = new Request('GET', '/api-create.php');
        $response = $this->client->send($request, $options);

        return $response->getBody()->getContents();
    }
}
