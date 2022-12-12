<?php

/* 
 *  Copyright 2016 Evgeniy Zhelyazkov <evgeniyzhelyazkov@gmail.com>.
 * 
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

class Dadata_Service_Rest
{
    private $client;
    
    /**
    * Return the associated Dadata_Client class.
    * @return Dadata_Client
    */
    public function getClient()
    {
        return $this->client;
    }
    
    /**
     * Constructs the internal representation of the Suggestions service.
     *
     * @param Dadata_Client $client
     */
    public function __construct(Dadata_Client $client)
    {
        $this->client = $client;        
        $this->servicePath = '/suggestions/api/4_1/rs/';
        $this->serviceName = 'Rest';
      
        $this->geo = new Dadata_Service_Geo_Resource(
            $this,
            $this->serviceName,
            'geo',
            array(
              'methods' => array(
                    'location' => array(
                        'path' => 'detectAddressByIp',
                        'httpMethod' => 'GET',
                        'parameters' => array(
                            'ip' => array(
                                'location' => 'query',
                                'type' => 'string',
                            )
                        )
                    )
                )
            )
        );
        
        $this->suggest = new Dadata_Service_Suggest_Resource(
            $this,
            $this->serviceName,
            'suggest',
            array(
              'methods' => array(
                    'party' => array(
                        'path' => 'suggest/party',
                        'httpMethod' => 'POST',
                        'parameters' => array(
                            'query' => array(
                                'location' => 'query',
                                'type' => 'string',
                            )
                        )
                    ),
                    'address' => array(
                        'path' => 'suggest/address',
                        'httpMethod' => 'POST',
                        'parameters' => array(
                            'query' => array(
                                'location' => 'query',
                                'type' => 'string',
                            ),
                            'count' => array(
                                'location' => 'query',
                                'type' => 'integer'
                            )
                        )
                    ),
                    'bank' => array(
                        'path' => 'suggest/bank',
                        'httpMethod' => 'POST',
                        'parameters' => array(
                            'query' => array(
                                'location' => 'query',
                                'type' => 'string',
                            )
                        )
                    ),
                    'country' => array(
                        'path' => 'suggest/country',
                        'httpMethod' => 'POST',
                        'parameters' => array(
                            'query' => array(
                                'location' => 'query',
                                'type' => 'string',
                            )
                        )
                    )
                )
            )
        );
    }
  
}

 /**
 * The "geo" collection of methods.
 * Typical usage is:
 *  <code>
 *   $restService = new Dadata_Service_Rest(...);
 *   $geo = $restService->geo;
 *  </code>
 */
class Dadata_Service_Geo_Resource extends Dadata_Service_Resource
{    
    /**
    * Возвращает информацию о местоположении по IP
    * @param array $optParams
    * @return Dadata_Geo_Location
    */
   public function location($optParams = array())
   {
        $params = array();
        $params = array_merge($params, $optParams);
        return $this->call('location', array($params), "Dadata_Geo_Location_Collection");
   }
}
  
  /**
 * The "suggest" collection of methods.
 * Typical usage is:
 *  <code>
 *   $restService = new Dadata_Service_Rest(...);
 *   $suggest = $restService->suggest;
 *  </code>
 */
class Dadata_Service_Suggest_Resource extends Dadata_Service_Resource
{

  /**
   * Возвращает информацию об организации.
   * @param array $optParams
   * @return Dadata_Suggest_Party_Collection
   */
  public function party($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('party', array($params), "Dadata_Suggest_Party_Collection");
  }
  
  /**
   * Возвращает информацию об адресе вместе с координатами.
   * @param array $optParams
   * @return Dadata_Suggest_Address_Collection
   */
  public function address($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('address', array($params), "Dadata_Suggest_Address_Collection");
  }
  
  /**
   * Возвращает информацию о банке.
   * @param array $optParams
   * @return Dadata_Suggest_Bank_Collection
   */
  public function bank($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('bank', array($params), "Dadata_Suggest_Bank_Collection");
  }
  
  /**
   * Возвращает информацию о стране.
   * @param array $optParams
   * @return Dadata_Suggest_Country_Collection
   */
  public function country($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('country', array($params), "Dadata_Suggest_Country_Collection");
  }
 
}

class Dadata_Geo_Location_Collection extends Dadata_Collection
{
    protected $locationType = 'Dadata_Geo_Location_Data';
    
    public function getLocation(){
        return $this->location;
    }    
}

class Dadata_Geo_Location_Data extends Dadata_Data_Collection
{
    protected $dataType = 'Dadata_Suggest_Address';
}

/**
 * Информация об адресе с точными координатами
 */
class Dadata_Suggest_Address extends Dadata_Model
{
    public $qc_complete;
    public $qc_house;
    public $qc_geo;
    public $postal_code;
    public $postal_box;
    public $country;
    public $region_fias_id;
    public $region_kladr_id;
    public $region_with_type;
    public $region_type;
    public $region_type_full;
    public $region;
    public $area_fias_id;
    public $area_kladr_id;
    public $area_with_type;
    public $area_type;
    public $area_type_full;
    public $area;
    public $city_fias_id;
    public $city_kladr_id;
    public $city_with_type;
    public $city_type;
    public $city_type_full;
    public $city;
    public $city_district;
    public $settlement_fias_id;
    public $settlement_kladr_id;
    public $settlement_with_type;
    public $settlement_type;
    public $settlement_type_full;
    public $settlement;
    public $street_fias_id;
    public $street_kladr_id;
    public $street_with_type;
    public $street_type;
    public $street_type_full;
    public $street;
    public $house_fias_id;
    public $house_kladr_id;
    public $house_type;
    public $house_type_full;
    public $house;
    public $block_type;
    public $block_type_full;
    public $block;
    public $flat_area;
    public $square_meter_price;
    public $flat_price;
    public $flat_type;
    public $flat_type_full;
    public $flat;
    public $fias_id;
    public $fias_leve;
    public $kladr_id;
    public $tax_office;
    public $tax_office_legal;
    public $capital_marker;
    public $okato;
    public $oktmo;
    public $timezone;
    public $geo_lat;
    public $geo_lon;
    public $beltway_hit;
    public $beltway_distance;
    public $unparsed_parts;
    public $qc;
    
    public function getCoords(){
        return $this->geo_lat.','.$this->geo_lon;
    }
}

/**
 * Список стран
 */
class Dadata_Suggest_Country_Collection extends Dadata_Suggestions_Collection
{
    protected $suggestionsType = 'Dadata_Suggest_Country_Data';    
}

/**
 * Данные о стране с условиями поиска
 */
class Dadata_Suggest_Country_Data extends Dadata_Data_Collection
{    
    protected $dataType = 'Dadata_Suggest_Country'; 
}

/**
 * Данные о стране
 */
class Dadata_Suggest_Country extends Dadata_Collection
{
    /**
     * Значение одной строкой (как показывается в списке подсказок)
     */
    public $value;
    
    /**
     * { code,	// Цифровой код страны
     *   alfa2,	// Буквенный код альфа-2
     *   alfa3,	// Буквенный код альфа-3
     *   name_short, //Краткое наименование страны
     *   name // Полное официальное наименование страны
     * }
     * @var array 
     */
    public $data;
}

/**
 * Список банков
 */
class Dadata_Suggest_Bank_Collection extends Dadata_Suggestions_Collection
{
    protected $suggestionsType = 'Dadata_Suggest_Bank_Data';    
}

/**
 * Данные о банке с условиями поиска
 */
class Dadata_Suggest_Bank_Data extends Dadata_Data_Collection
{    
    protected $dataType = 'Dadata_Suggest_Bank'; 
}

/**
 * Данные о банке
 */
class Dadata_Suggest_Bank extends Dadata_Collection
{
    protected $internal_gapi_mappings = array(
        'correspondent_account' => 'correspondentAccount',
        'registration_number' => 'registrationNumber'
    );
    protected $rkcType = 'Dadata_Suggest_Bank';
    protected $addressType = 'Dadata_Suggest_Address_Data';
    public $opf;
    public $name;
    public $bic;
    public $swift;
    public $okpo;
    public $correspondentAccount;
    public $registrationNumber;
    public $phone;
    public $state;
    
    public function setRkc($rkc){
        $this->rkc = $rkc;
    }
    
    public function getRkc(){
        return $this->rkc;
    }
    
    public function setAddress($address) {
        $this->address = $address;
    }
    
    public function getAddress() {
        return $this->address;
    }
}


/**
 * Данные об организации с условиями поиска
 */
class Dadata_Suggest_Address_Data extends Dadata_Data_Collection
{    
    protected $dataType = 'Dadata_Suggest_Address';
}

 /**
 * Список адресов
 */
class Dadata_Suggest_Address_Collection extends Dadata_Suggestions_Collection
{
    protected $suggestionsType = 'Dadata_Suggest_Address_Data';
}

 /**
 * Список организаций
 */
class Dadata_Suggest_Party_Collection extends Dadata_Suggestions_Collection
{
    protected $suggestionsType = 'Dadata_Suggest_Party_Data';
}

/**
 * Подробные данные об организации с условиями поиска
 */
class Dadata_Suggest_Party_Data extends Dadata_Data_Collection
{
    protected $dataType = 'Dadata_Suggest_Party';    
}

/**
 * Информация об организации
 */
class Dadata_Suggest_Party extends Dadata_Collection
{
    protected $addressType = 'Dadata_Suggest_Address_Data';
    public $kpp;
    public $inn;
    public $ogrn;
    public $okpo;
    public $okved;
    public $opf;
    public $name;
    public $state;
    public $management;
    public $type;    
            
    public function getKpp() {
        return $this->kpp;
    }

    public function getInn() {
        return $this->inn;
    }

    public function getOgrn() {
        return $this->ogrn;
    }

    public function getOkpo() {
        return $this->okpo;
    }

    public function getOkved() {
        return $this->okved;
    }

    public function getOpf() {
        return $this->opf;
    }

    public function getName() {
        return $this->name;
    }

    public function getState() {
        return $this->state;
    }

    public function setAddress($address) {
        $this->address = $address;
    }
    
    public function getAddress() {
        return $this->address;
    }

    public function getManagement() {
        return $this->management;
    }

    public function getType() {
        return $this->type;
    }
    
}