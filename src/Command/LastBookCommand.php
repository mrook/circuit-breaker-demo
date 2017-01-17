<?php

namespace Demo\Command;

use GuzzleHttp\Client;
use Odesk\Phystrix\AbstractCommand;

class LastBookCommand extends AbstractCommand
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    protected function run()
    {
        $result = $this->client->request('GET', 'http://localhost:1337/book/last', ['timeout' => 0.1]);
        return \json_decode($result->getBody()->getContents(), true);
    }
    
    protected function getFallback()
    {
        return [];
    }
}
