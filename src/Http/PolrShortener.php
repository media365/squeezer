<?php

namespace Media365\Squeezer\Http;

use GuzzleHttp\ClientInterface;
use Media365\Squeezer\Contracts\Shortener;
use function GuzzleHttp\json_decode;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;

class PolrShortener implements Shortener
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $defaults;

    /**
     * Create a new Polr shortener.
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
            'base_uri' => $domain,
            'headers' => [
                'Accept' => 'application/json',
            ],
            'query' => [
                'key' => $token,
                'response_type' => 'json',
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function shorten($url, array $options = [])
    {
        $options = array_merge_recursive(Arr::add($this->defaults, 'query.url', $url), $options);
        $request = new Request('GET', '/api/v2/action/shorten');
        $response = $this->client->send($request, $options);

        return json_decode($response->getBody()->getContents())->result;
    }
}
