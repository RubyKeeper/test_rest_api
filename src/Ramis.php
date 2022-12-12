<?php

namespace Psr\Http\Client;

class Ramis
{
    use Mtrait;

    public function sendRequest(int $inn): array
    {
        return ['kpp' => $this->getKpp(), 'org' => $this->getNameOrg()];
    }
}