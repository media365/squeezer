<?php

namespace Media365\Squeezer;

use GuzzleHttp\ClientInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Media365\Squeezer\Http\BitLyShortener;
use Media365\Squeezer\Http\FirebaseShortener;
use Media365\Squeezer\Http\IsGdShortener;
use Media365\Squeezer\Http\OuoIoShortener;
use Media365\Squeezer\Http\PolrShortener;
use Media365\Squeezer\Http\ShorteStShortener;
use Media365\Squeezer\Http\TinyUrlShortener;

class SqueezerManager
{
    protected $app;
    protected $shorteners;

    /**
     * Create a new URL shortener manager instance.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->shorteners = [];
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->driver()->$method(...$parameters);
    }

    /**
     * Create an instance of the Bit.ly driver.
     *
     * @param array $config
     * @return \Media365\Squeezer\Http\BitLyShortener
     */
    protected function createBitLyDriver(array $config)
    {
        return new BitLyShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token'),
            Arr::get($config, 'domain', 'bit.ly')
        );
    }

    /**
     * Create an instance of the Firebase driver.
     *
     * @param array $config
     * @return \Media365\Squeezer\Http\FirebaseShortener
     */
    protected function createFirebaseDriver(array $config)
    {
        return new FirebaseShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token'),
            Arr::get($config, 'prefix'),
            Arr::get($config, 'suffix')
        );
    }
    /**
     * Create an instance of the Is.gd driver.
     *
     * @param array $config
     * @return \Media365\Squeezer\Http\IsGdShortener
     */
    protected function createIsGdDriver(array $config)
    {
        return new IsGdShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'base_uri'),
            Arr::get($config, 'statistics')
        );
    }
    /**
     * Create an instance of the Ouo.io driver.
     *
     * @param array $config
     * @return \Media365\Squeezer\Http\OuoIoShortener
     */
    protected function createOuoIoDriver(array $config)
    {
        return new OuoIoShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token')
        );
    }
    /**
     * Create an instance of the Polr driver.
     *
     * @param array $config
     * @return \Media365\Squeezer\Http\PolrShortener
     */
    protected function createPolrDriver(array $config)
    {
        return new PolrShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token'),
            Arr::get($config, 'prefix')
        );
    }
    /**
     * Create an instance of the Shorte.st driver.
     *
     * @param array $config
     * @return \Media365\Squeezer\Http\ShorteStShortener
     */
    protected function createShorteStDriver(array $config)
    {
        return new ShorteStShortener(
            $this->app->make(ClientInterface::class),
            Arr::get($config, 'token')
        );
    }

    /**
     * Create an instance of the TinyURL driver.
     *
     * @return \Media365\Squeezer\Http\TinyUrlShortener
     */
    protected function createTinyUrlDriver()
    {
        return new TinyUrlShortener($this->app->make(ClientInterface::class));
    }

    /**
     * Get a URL shortener driver instance.
     *
     * @param string|null $name
     * @return \Media365\Squeezer\Contracts\Shortener
     */
    public function driver(string $name = null)
    {
        return $this->shortener($name);
    }

    /**
     * Resolve the given URL shortener.
     *
     * @param string $name
     * @return \Media365\Squeezer\Contracts\Shortener
     */
    protected function resolve(string $name)
    {
        $config = $this->app['config']["squeezer.shorteners.$name"];

        if (is_null($config) || !array_key_exists('driver', $config)) {
            throw new InvalidArgumentException("URL shortener [{$name}] is not defined");
        }

        $driverMethod = 'create' . Str::studly($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->$driverMethod($config);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function shortener(string $name = null)
    {
        $name = $name ?: $this->app['config']['squeezer.default'];

        if (array_key_exists($name, $this->shorteners)) {
            return $this->shorteners[$name];
        }

        return $this->shorteners[$name] = $this->resolve($name);
    }
}
