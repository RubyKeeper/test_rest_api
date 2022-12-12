<?php
namespace Clients;


class Data
{
    private array $clients;
    private object $redis;

    public function __construct(array $httpClient)
    {
        $this->redis = new \Predis\Client('redis:6379');
        $this->clients = $this->getClients($httpClient);
    }

    /**
     * @param string $inn
     *
     * @return array|null
     */
    public function getOrganizationByInn(string $inn): ?array
    {
        foreach ($this->clients as $item) {
            $start = microtime(true);
            $response = $item->sendRequest($inn);
            if (!$response or (microtime(true) - $start) > 1000000) {
                continue;
            } else {
//                $class_name = get_class($item);
//                $this->setRedisClient($class_name);
                return $response;
            }
        }
        return null;
    }

    /**
     * Функция записывает название класса в редис
     * @param string $class_name
     */
    private function setRedisClient(string $class_name): void
    {
        $this_date = date('Ymd');
        $this->redis->zincrby('stats:'.$this_date, 1, $class_name);
    }

    /**
     * Данный класс сеачала проверяет Редис на количество записей источников с баллом больше 12
     * а потом сравнивает с количеством всех источников, если они совпадают, то берем списки источников
     * с редиса, которые уже сортированы по баллам (успешным)
     *
     * @param array|$namespace
     *
     * @return array
     */
    private function getClients(array $namespace): array
    {
        $this_date = date('Ymd');
        $client_list_redis = $this->redis->zrevrangebyscore('stats:'.$this_date, '+inf', '80'); // все записи у которых балл выше 10

        /**
         * если равнество совпало, это будет означать,
         * в статистике отметился, каждый источник не менее 10 раз
         */
        if (count($client_list_redis) === count($namespace)) {
            $namespace_array = $client_list_redis;
        } else {
            shuffle($namespace); // статический массив всегда рандомно перемешиваем
            $namespace_array = $namespace;
        }

        $client = [];
        foreach ($namespace_array as $namespace) {
            $client[] = new $namespace;
        }

        return $client;
    }

}