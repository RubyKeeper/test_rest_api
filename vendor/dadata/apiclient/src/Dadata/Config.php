<?php
/*
 * Copyright 2016 Evgeniy Zhelyazkov <evgeniyzhelyazkov@gmail.com>.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * A class to contain the library configuration for the Dadata API client.
 */
class Dadata_Config extends Google_Config
{
    
    private $apiKey;
    
    private $contentType;
 
  /**
   * Create a new Dadata_Config. Can accept an ini file location with the
   * local configuration. For example:
   *     base_path="https://suggestions.dadata.ru/suggestions/api"
   *
   * @param [$ini_file_location] - optional - The location of the ini file to load
   */
  public function __construct($ini_file_location = null)          
  {
    parent::__construct($ini_file_location);
    $this->configuration['base_path'] = 'https://suggestions.dadata.ru/suggestions/api';         
    //$this->configuration['io_class'] = 'Bitrix24_File';
    $this->configuration['classes']['Google_Cache_File']['directory'] = __DIR__.'/tmp';
  }
  
  public function getApiKey() {
      return $this->apiKey;
  }

  public function setApiKey($apiKey) {
      $this->apiKey = $apiKey;
  }
  
  public function getContentType() {
      return $this->contentType;
  }

  public function setContentType($contentType) {
      $this->contentType = $contentType;
  }
      
  /**
   * Set base URL to use for API calls
   * @param $uri string - the domain to use.
   */
  public function setBasePath($uri)
  {
    $this->configuration['base_path'] = $uri;
  }
  
  /**
   * @return string the base URL to use for API calls
   */
  public function getBasePath()
  {
    return $this->configuration['base_path'];
  }

}
