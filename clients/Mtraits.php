<?php
namespace Psr\Http\Client;

trait Mtraits
{
    /**
     * Генерация случайной организации
     * @return string
     */
    private function getNameOrg():string {
        $org_name[] = 'Орион';
        $org_name[] = 'Первая фирма';
        $org_name[] = 'Торговая фирма';
        $org_name[] = 'Клиниговая компания';
        $org_name[] = 'Тамерлан';
        $org_name[] = 'Аристон';
        $number = array_rand($org_name);
        return $org_name[$number];
    }

    /**
     * Генерация случайного КПП
     * @return string
     */
    private function getKpp():string {
        $k = [];
        $count = 0;
        while($count < 10) {
            $k[] = mt_rand(0, 9);
            $count++;
        }
        return implode($k);
    }
}