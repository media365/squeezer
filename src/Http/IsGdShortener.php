<?php

namespace Media365\Squeezer\Http;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Media365\Squeezer\Contracts\Shortener;

class IsGdShortener implements Shortener
{
    protected $client;
    protected $defaults;

    /**
     * Create a new Is.gd shortener.
     *
     * @param \GuzzleHttp\ClientInterface $client
     * @param \Psr\Http\Message\UriInterface|string $baseUri
     * @param bool $statistics
     * @return void
     */
    public function __construct(ClientInterface $client, $baseUri, bool $statistics)
    {
        $this->client = $client;
        $this->defaults = [
            'allow_redirects' => false,
            'base_uri' => (string)$baseUri,
            'query' => [
                'format' => 'simple',
                'logstats' => intval($statistics),
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function shorten($url, array $options = [])
    {
        $options = Arr::add(array_merge_recursive($this->defaults, $options), 'query.url', $url);
        $request = new Request('GET', '/create.php');

        $response = $this->client->send($request, $options);

        return $response->getBody()->getContents();
    }
}
