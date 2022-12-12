<?php

namespace Psr\Http\Client;

class Warms implements ClientInterface
{
    use Mtraits;

    public function sendRequest(int $inn): array
    {
        return ['kpp' => $this->getKpp(), 'org' => $this->getNameOrg()];
    }
}
