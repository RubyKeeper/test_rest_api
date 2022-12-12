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

class Dadata_Collection extends Dadata_Model implements Iterator, Countable
{
    
    protected $collection_key = 'suggestions';
    public $etag;      
    public $kind;
    public $nextLink;
    public $nextPageToken;
    public $selfLink;

    public function setEtag($etag)
    {
      $this->etag = $etag;
    }

    public function getEtag()
    {
      return $this->etag;
    }

    public function setKind($kind)
    {
      $this->kind = $kind;
    }

    public function getKind()
    {
      return $this->kind;
    }

    public function setNextLink($nextLink)
    {
      $this->nextLink = $nextLink;
    }

    public function getNextLink()
    {
      return $this->nextLink;
    }

    public function setNextPageToken($nextPageToken)
    {
      $this->nextPageToken = $nextPageToken;
    }

    public function getNextPageToken()
    {
      return $this->nextPageToken;
    }

    public function setSelfLink($selfLink)
    {
      $this->selfLink = $selfLink;
    }

    public function getSelfLink()
    {
      return $this->selfLink;
    }    

    public function rewind()
    {
      if (isset($this->modelData[$this->collection_key])
          && is_array($this->modelData[$this->collection_key])) {
        reset($this->modelData[$this->collection_key]);
      }
    }

    public function current()
    {
      $this->coerceType($this->key());
      if (is_array($this->modelData[$this->collection_key])) {
        return current($this->modelData[$this->collection_key]);
      }
    }

    public function key()
    {
      if (isset($this->modelData[$this->collection_key])
          && is_array($this->modelData[$this->collection_key])) {
        return key($this->modelData[$this->collection_key]);
      }
    }

    public function next()
    {
      return next($this->modelData[$this->collection_key]);
    }

    public function valid()
    {
      $key = $this->key();
      return $key !== null && $key !== false;
    }

    public function count()
    {
      if (!isset($this->modelData[$this->collection_key])) {
        return 0;
      }
      return count($this->modelData[$this->collection_key]);
    }

    public function offsetExists($offset)
    {
      if (!is_numeric($offset)) {
        return parent::offsetExists($offset);
      }
      return isset($this->modelData[$this->collection_key][$offset]);
    }

    public function offsetGet($offset)
    {
      if (!is_numeric($offset)) {
        return parent::offsetGet($offset);
      }
      $this->coerceType($offset);
      return $this->modelData[$this->collection_key][$offset];
    }

    public function offsetSet($offset, $value)
    {
      if (!is_numeric($offset)) {
        return parent::offsetSet($offset, $value);
      }
      $this->modelData[$this->collection_key][$offset] = $value;
    }

    public function offsetUnset($offset)
    {
      if (!is_numeric($offset)) {
        return parent::offsetUnset($offset);
      }
      unset($this->modelData[$this->collection_key][$offset]);
    }

    private function coerceType($offset)
    {
      $typeKey = $this->keyType($this->collection_key);
      if (isset($this->$typeKey) && !is_object($this->modelData[$this->collection_key][$offset])) {
        $type = $this->$typeKey;
        $this->modelData[$this->collection_key][$offset] =
            new $type($this->modelData[$this->collection_key][$offset]);
      }
    }
    
}