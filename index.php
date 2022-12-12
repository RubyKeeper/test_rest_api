<?php
include './vendor/autoload.php';

// список источников
$namespace = [
    'Psr\Http\Client\Ramis',
    'Psr\Http\Client\Warms',
    'Psr\Http\Client\Dadata'
];


// сюда пишем ИНН которые хотим проверить
$inn_array = [
    '5003021311',
    '0105031623',
    '0105011190',
    '0278081806',
    '0267009477',
    '0276073077',
    '0276073574',
    '0276059298',
    '0274062111',
    '0323110787',
];

$m = new \Clients\Data($namespace);

foreach ($inn_array as $value) {
    $answer[] = $m->getOrganizationByInn($value);
}

var_dump($answer);

echo 'Статистика по источникам в редисе:<br>';
$this_date = date('Ymd');
$redis = new \Predis\Client('redis:6379');
$client_list_redis = $redis->zrevrangebyscore('stats:'.$this_date, '+inf', '0', 'WITHSCORES');
foreach ($client_list_redis as $key=>$item) {
    echo 'Класс: '. $key. ', успешно вызывался => ' . $item .'раз<br>';
}