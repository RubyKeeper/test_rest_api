<?php

namespace Psr\Http\Client;

class Dadata
{
    use Mtrait;

    private object $dadata;

    public function __construct()
    {
        $this->dadata = new \Dadata\DadataClient('8971a0cc7626aac9256753aa7d56d6a92796f6f3', '28398c960b00f74445f0deb091762046950aaff0');
    }

    public function sendRequest(int $inn): array
    {
        $response = $this->dadata->findById("party", (string) $inn);

        $res = [];
        foreach ($response as $value) {
            $res = ['kpp' => $value['data']['kpp'], 'name' => $value['value']];
        }

        return $res;
    }
}
