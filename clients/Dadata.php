<?php

namespace Psr\Http\Client;

use Psr\Http\Message\RequestInterface;

class Dadata implements ClientInterface
{
    use Mtraits;

    private object $dadata;

    public function __construct()
    {
        $this->dadata = new \Dadata_Client('8971a0cc7626aac9256753aa7d56d6a92796f6f3', null);
    }

    public function sendRequest(int $inn): array
    {
        $response = $this->dadata->findById("party", (string) $inn);

        return ['dada' => $this->getKpp(), 'org' => $this->getNameOrg()];
    }
}
