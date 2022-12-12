<?php

namespace Psr\Http\Client;

class Warms
{
    use Mtrait;

    public function sendRequest(int $inn): array
    {
        return ['kpp' => $this->getKpp(), 'org' => $this->getNameOrg()];
    }
}
