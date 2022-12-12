<?php

/* 
 *  This file is part of the LogistBundle Symfony package.
 * 
 *  (c) Evgeniy Zhelyazkov <evgeniyzhelyazkov@gmail.com>
 * 
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

if (!class_exists('Dadata_Client')) {
  require_once dirname(__FILE__) . '/../autoload.php';
}

/**
 * This class implements the RESTful transport of apiServiceRequest()'s
 */
class Dadata_Http_REST
{
  
  /**
   * Decode an HTTP Response.
   * @static
   * @throws Dadata_Service_Exception
   * @param Dadata_Http_Request $response The http response to be decoded.
   * @param Dadata_Client $client
   * @return mixed|null
   */
  public static function decodeHttpResponse($request, $response, Dadata_Client $client = null)
  {
    $code = $response->getStatusCode();
    $body = $response->getBody()->getContents();
    $decoded = null;

    if ((intVal($code)) >= 300) {
      $decoded = json_decode($body, true);
      $err = 'Error calling ' . $response->getRequestMethod() . ' ' . $response->getUrl();
      if (isset($decoded['error']) &&
          isset($decoded['error']['message'])  &&
          isset($decoded['error']['code'])) {
        // if we're getting a json encoded error definition, use that instead of the raw response
        // body for improved readability
        $err .= ": ({$decoded['error']['code']}) {$decoded['error']['message']}";
      } else {
        $err .= ": ($code) $body";
      }

      $errors = null;
      // Specific check for APIs which don't return error details, such as Blogger.
      if (isset($decoded['error']) && isset($decoded['error']['errors'])) {
        $errors = $decoded['error']['errors'];
      }

      $map = null;
      if ($client) {
        $client->getLogger()->error(
            $err,
            array('code' => $code, 'errors' => $errors)
        );

        $map = $client->getClassConfig(
            'Google_Service_Exception',
            'retry_map'
        );
      }
      throw new Dadata_Service_Exception($err, $code, null, $errors, $map);
    }
    // Only attempt to decode the response, if the response code wasn't (204) 'no content'
    if ($code != '204') {
      if ($request->getExpectedRaw()) {
        return $body;
      }
      
      $decoded = json_decode($body, true);
      if ($decoded === null || $decoded === "") {
        $error = "Invalid json in service response: $body";
        if ($client) {
          $client->getLogger()->error($error);
        }
        throw new Dadata_Service_Exception($error);
      }

      if ($request->getExpectedClass()) {
        $class = $request->getExpectedClass();
        $decoded = new $class($decoded);
      }
    }
    return $decoded;
  }

  /**
   * Parse/expand request parameters and create a fully qualified
   * request uri.
   * @static
   * @param string $servicePath
   * @param string $restPath
   * @param array $params
   * @return string $requestUrl
   */
  public static function createRequestUri($servicePath, $restPath, $params)
  {
    $requestUrl = $servicePath . $restPath;
    $uriTemplateVars = array();
    $queryVars = array();
    foreach ($params as $paramName => $paramSpec) {
      if ($paramSpec['type'] == 'boolean') {
        $paramSpec['value'] = ($paramSpec['value']) ? 'true' : 'false';
      }
      if ($paramSpec['location'] == 'path') {
        $uriTemplateVars[$paramName] = $paramSpec['value'];
      } else if ($paramSpec['location'] == 'query') {
        if (isset($paramSpec['repeated']) && is_array($paramSpec['value'])) {
          foreach ($paramSpec['value'] as $value) {
            $queryVars[] = $paramName . '=' . rawurlencode(rawurldecode($value));
          }
        } else {
          $queryVars[] = $paramName . '=' . rawurlencode(rawurldecode($paramSpec['value']));
        }
      }
    }
    
    if (count($queryVars)) {
      $requestUrl .= '?' . implode('&', $queryVars);
    }

    return $requestUrl;
  }
}
