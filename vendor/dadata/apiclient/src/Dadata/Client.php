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

/**
 * The Dadata API Client
 *
 * @author Evgeniy Zhelyazkovt <evgeniyzhelyazkov@gmail.com>
 */
class Dadata_Client
{
    const LIBVER = "0.0.1-alfa";
    const USER_AGENT_SUFFIX = "dadata-api-php-client/";
    
    private $client;
    
    private $basePath;
    private $apiKey;
    private $contentType;
    
  /**
   * Construct the Dadata Client.
   *
   * @param $config Dadata_Config or string for the ini file to load
   */
  public function __construct($config = null)
  {
    $this->client = new \GuzzleHttp\Client(array('base_uri' => $config['base_uri']));
    $this->setApiKey($config['api_key']);
    $this->setContentType($config['content_type']); 
  }
  
    /**
     * Helper method to execute deferred HTTP requests.
     *
     * @return object of the type of the expected class or array.
     */
    public function execute($request)
    {      
        if($request->getPostBody()){
            $httpResponse = $this->client->request($request->getRequestMethod(), $request->getUrl(), 
                    array(
                        'headers' => $request->getRequestHeaders(),
                        'body' => $request->getPostBody()
                    )
                );
        }else{
            $httpResponse = $this->client->request($request->getRequestMethod(), $request->getUrl(), 
                    array(
                        'headers' => $request->getRequestHeaders()
                    )
                );
        }        
        return Dadata_Http_REST::decodeHttpResponse($request, $httpResponse);
    }  
   
  /**
   * Set base URL to use for API calls
   * @param $uri string - the domain to use.
   */
  public function setBasePath($uri)
  {
    $this->basePath = $uri;
  }

  /**
   * @return string the base URL to use for calls to the APIs
   */
  public function getBasePath()
  {
    return $this->basePath;
  }  

  /**
   * Set Api Key
   * @param string the Api Key to use for calls to the APIs
   */
  public function setApiKey($key)
  {
    $this->apiKey = $key;
  }
  
  /**
   * @return string the Api Key to use for calls to the APIs
   */
  public function getApiKey()
  {
    return $this->apiKey;
  }
  
  /**
   * @return string the Content-Type to use for calls to the APIs
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  
  /**
   * Set Content-Type to use for API calls
   * @param $contentType string
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }

}
