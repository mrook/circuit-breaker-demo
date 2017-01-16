<?php

namespace Demo\Command;

use Odesk\Phystrix\AbstractCommand;

class LastBookCommand extends AbstractCommand
{
    /**
     * @return mixed
     */
    protected function run()
    {
        $client = new \GuzzleHttp\Client();
        $result = $client->request('GET', 'http://localhost:1337/book/last', ['timeout' => 0.1]);
        return \json_decode($result->getBody()->getContents(), true);
    }
    
    protected function getFallback()
    {
        return [];
    }
}
