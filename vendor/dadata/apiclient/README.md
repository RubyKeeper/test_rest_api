# Dadata API Client #

Библиотека для работы с REST API сайта http://dadata.ru. Позволяет находить организации и банки по реквизитам, а так же полную информацию по адресу.

### Требования ###

* [PHP 5.2 или выше](http://php.net)
* [google/google-api-php-client](https://github.com/google/google-api-php-client)

### Установка ###

Вы можете использовать **Composer** или просто **Скачать Релиз**

#### Composer ####

Рекомендуемый метод установки через [composer](https://getcomposer.org/). Следуйте [инструкции по установке](https://getcomposer.org/doc/00-intro.md) если у вас не установлен composer.

Выполните команды в консоли для установки библиотеки:

```
#!console

composer require dadata/apiclient
```

#### Скачать Релиз ####

Если вы не хотите использовать composer, вы можете скачать пакет с библиотекой в архиве. Страница с релизами содержит список стабильных версий. Скачайте любой из доступных релизов [на этой странице](https://bitbucket.org/henui/dadata-apiclient/downloads).

Распакуйте содержимое архива в папку vendor и добавьте в composer.json пути к классам:

```
#!json

"classmap": [
   "vendor/google/apiclient/src",
   "vendor/dadata/apiclient/src"
]  
```

### Примеры ###

для начала работы нужно получить API Client и REST сервис:


```
#!php

<?php

$client = new \Dadata_Client(array('api_key' => 'YOUR_API_KEY', 'content_type' => 'application/json'));
$rest = new \Dadata_Service_Rest($client);

```

#### Получение данных об организации по ИНН ####

```
#!php

<?php

   $suggest = $rest->suggest->party(array('query' => '7604254988'));
   foreach($suggest->getSuggestions() as $party){
      $organization = $party->getData(); // получение данных об организации
      $name = $organization->getValue(); // Наименование организации одной строкой (для списка подсказок)
      $nameFull = $organization->getUnrestrictedValue(); // Наименование организации одной строкой (полное)
      $address = $organization->getAddress(); // получение адреса организации
   }

```

#### Получение адреса ####

```
#!php

<?php

   $suggest = $rest->suggest->address(array('query' => 'Ярославль Победы 38', 'count' => 10));
   foreach($suggest->getSuggestions() as $address){
      $value = $address->getValue(); // Адрес одной строкой (как показывается в списке подсказок)
      $unrestrictedValue = $address->getUnrestrictedValue(); // Адрес одной строкой (полный, от региона)
      $data = $address->getData(); // Получение подробных данных об адресе в виде объекта.
   }      

```

#### Получение данных о банке по БИК ####

```
#!php

<?php

   $suggest = $rest->suggest->bank(array('query' => '046577964'));
   foreach($suggest->getSuggestions() as $bank){
      $bankName = $bank->getValue(); // Наименование банка одной строкой (для списка подсказок)
      $bankNameFull = $bank->getUnrestrictedValue(); // Наименование банка одной строкой (полное)
      $data = $item->getData(); // Подробные данные о банке.
      $bankAddress = $item->getData()->getAddress(); // Адрес банка
      $rkc = $item->getData()->getRkc(); // Расчетно-кассовый центр. Объект такой же структуры, как сам банк
      $rkcAddress = $item->getData()->getRkc()->getAddress(); // Адрес расчетно-кассового центра
   }     

```

#### Определение геопозиции по IP ####

```
#!php

<?php

   $geo = $rest->geo->location(array('ip' => '128.74.215.229'));
   $location = $geo->getLocation();
   $address = $location->getValue(); // Получение адреса одной строкой (для списка подсказок)
   $addressFull = $location->getUnrestrictedValue(); // Получение адреса одной строкой (полного)
   $data = $location->getData(); // Получение подробных данных об адресе в виде объекта
   $coords = $data->getCoords(); // Получение точных координат в виде строки (lat,lon)

```

### Symfony DadataApi Bundle ###

Используйте готовый [DadataApiBundle](https://bitbucket.org/henui/dadata-api-bundle) для работы с фреймворком Symfony.