<?php

namespace Larawatch\Http;

use GuzzleHttp\ClientInterface;

class StatsClient
{
    /** @var ClientInterface|null */
    protected $client;

    /** @var string */
    protected $login;


    /**
     * @param ClientInterface|null $client
     */
    public function __construct(string $login, string $project, ClientInterface $client = null)
    {
        $this->client = $client;
    }

    public function sendRawData(string $destination, array $data)
    {
        try {
            return $this->getGuzzleHttpClient()->request('POST', config('larawatch.base_url') . $destination, [
                'headers' => [
                    'Authorization' => 'Bearer '.config('larawatch.destination_token'),
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'Larawatch-Package'
                ],
                'json' => array_merge([
                    'project_key' => config('larawatch.project_key'),
                    'server_key' => config('larawatch.server_key'),
                ], $data),
                'verify' => config('larawatch.verify_ssl'),
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getResponse();
        } catch (\Exception $e) {
            return null;
        }
    }
    /**
     * @return \GuzzleHttp\Client
     */
    public function getGuzzleHttpClient()
    {
        if (! isset($this->client)) {
            $this->client = new \GuzzleHttp\Client([
                'timeout' => 15,
            ]);
        }

        return $this->client;
    }

    /**
     * @param ClientInterface $client
     * @return $this
     */
    public function setGuzzleHttpClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }
}
