<?php

namespace Larawatch\Http;

use GuzzleHttp\ClientInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;

class Client
{
    /** @var ClientInterface|null */
    protected $client;

    /** @var string */
    protected $login;

    /** @var string */
    protected $project;

    protected $dataFile;

    protected FilesystemAdapter $disk;

    protected string $diskName;

    protected string $fullPath;

    protected string $existingDataFile;

    protected $existingData;

    public function __construct(string $login, string $project, ClientInterface $client = null)
    {
        $this->login = $login;
        $this->project = $project;
        $this->client = $client;
        $this->disk = Storage::disk('local');
    }

    protected function getGuzzleHeadersToken(): array
    {
        return [
            'Authorization' => 'Bearer ' . config('larawatch.destination_token'),
            'User-Agent' => 'Larawatch-Package',
            'project_key' => config('larawatch.project_key'),
            'server_key' => config('lasrawatch.server_key'),
        ];
    }

    protected function getGuzzleHeadersUser(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->login,
            'User-Agent' => 'Larawatch-Package',
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    protected function getProjectKeys(): array
    {
        return [
            'project_key' => config('larawatch.project_key'),
            'server_key' => config('larawatch.server_key'),
        ];
    }

    /**
     * @param  array  $exception
     * @return \GuzzleHttp\Promise\PromiseInterface|\Psr\Http\Message\ResponseInterface|null
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function report($exception)
    {
        try {
            return $this->getGuzzleHttpClient()->request('POST', config('larawatch.server'), [
                'headers' => $this->getGuzzleHeadersUser(),
                'json' => array_merge([
                    'project' => $this->project,
                    'additional' => [],
                ], $exception),
                'verify' => config('larawatch.verify_ssl'),
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getResponse();
        } catch (\Exception $e) {
            return;
        }
    }

    public function sendRawData(string $destination, array $data)
    {
        try {
            return $this->getGuzzleHttpClient()->request('POST', config('larawatch.base_url') . $destination, [
                'headers' => $this->getGuzzleHeadersToken(),
                'json' => array_merge($this->getProjectKeys(), $data),
                'verify' => config('larawatch.verify_ssl'),
            ]);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $e->getResponse();
        } catch (\Exception $e) {
            return;
        }
    }

    public function sendDataFile(string $dataFile)
    {
        $this->existingDataFile = $dataFile;

        if (!$this->disk->exists($this->existingDataFile)) {
            return false;
        } else {
            try {

                return $this->getGuzzleHttpClient()->request('POST', config('larawatch.base_url') . 'uploadfile', [
                    'headers' => $this->getGuzzleHeadersToken(),
                    'multipart' => [
                        [
                            'name' => 'file',
                            'filename' => $dataFile,
                            'contents' => $this->disk->get($this->existingDataFile),
                        ],
                        [
                            'name' => "project_key",
                            'contents' => config('larawatch.project_key'),
                        ],
                        [
                            'name' => "server_key",
                            'contents' => config('larawatch.server_key'),
                        ],

                    ],
                    'verify' => config('larawatch.verify_ssl'),
                ]);
            } catch (\GuzzleHttp\Exception\RequestException $e) {
                Log::error('Error:' . serialize($e));
                return $e->getResponse();
            } catch (\Exception $e) {
                Log::error('Error:' . serialize($e));
                return;
            }
        }
    }

    /**
     * @return \GuzzleHttp\Client|\GuzzleHttp\ClientInterface
     */
    public function getGuzzleHttpClient()
    {
        if (!isset($this->client)) {
            $this->client = new \GuzzleHttp\Client([
                'timeout' => 15,
            ]);
        }

        return $this->client;
    }

    /**
     * @return $this
     */
    public function setGuzzleHttpClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }
}
