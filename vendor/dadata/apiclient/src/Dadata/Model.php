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

class Dadata_Model implements ArrayAccess
{
          
    const NULL_VALUE = "{}dadata-php-null";
    protected $internal_gapi_mappings = array();
    protected $modelData = array();
    protected $processed = array();

    /**
     * Polymorphic - accepts a variable number of arguments dependent
     * on the type of the model subclass.
     */
    final public function __construct()
    {
      if (func_num_args() == 1 && is_array(func_get_arg(0))) {
        // Initialize the model with the array's contents.
        $array = func_get_arg(0);
        $this->mapTypes($array);
      }
      $this->gapiInit();
    }

    /**
     * Getter that handles passthrough access to the data array, and lazy object creation.
     * @param string $key Property name.
     * @return mixed The value if any, or null.
     */
    public function __get($key)
    {
      $keyTypeName = $this->keyType($key);
      $keyDataType = $this->dataType($key);
      if (isset($this->$keyTypeName) && !isset($this->processed[$key])) {
        if (isset($this->modelData[$key])) {
          $val = $this->modelData[$key];
        } else if (isset($this->$keyDataType) &&
            ($this->$keyDataType == 'array' || $this->$keyDataType == 'map')) {
          $val = array();
        } else {
          $val = null;
        }

        if ($this->isAssociativeArray($val)) {
          if (isset($this->$keyDataType) && 'map' == $this->$keyDataType) {
            foreach ($val as $arrayKey => $arrayItem) {
                $this->modelData[$key][$arrayKey] =
                  $this->createObjectFromName($keyTypeName, $arrayItem);
            }
          } else {
            $this->modelData[$key] = $this->createObjectFromName($keyTypeName, $val);
          }
        } else if (is_array($val)) {
          $arrayObject = array();
          foreach ($val as $arrayIndex => $arrayItem) {
            $arrayObject[$arrayIndex] =
              $this->createObjectFromName($keyTypeName, $arrayItem);
          }
          $this->modelData[$key] = $arrayObject;
        }
        $this->processed[$key] = true;
      }

      return isset($this->modelData[$key]) ? $this->modelData[$key] : null;
    }
    
    /**
     * Blank initialiser to be used in subclasses to do  post-construction initialisation - this
     * avoids the need for subclasses to have to implement the variadics handling in their
     * constructors.
     */
    protected function gapiInit()
    {
      return;
    }

    /**
     * Create a simplified object suitable for straightforward
     * conversion to JSON. This is relatively expensive
     * due to the usage of reflection, but shouldn't be called
     * a whole lot, and is the most straightforward way to filter.
     */
    public function toSimpleObject()
    {
      $object = new stdClass();

      // Process all other data.
      foreach ($this->modelData as $key => $val) {
        $result = $this->getSimpleValue($val);
        if ($result !== null) {
          $object->$key = $this->nullPlaceholderCheck($result);
        }
      }

      // Process all public properties.
      $reflect = new ReflectionObject($this);
      $props = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
      foreach ($props as $member) {
        $name = $member->getName();
        $result = $this->getSimpleValue($this->$name);
        if ($result !== null) {
          $name = $this->getMappedName($name);
          $object->$name = $this->nullPlaceholderCheck($result);
        }
      }

      return $object;
    }

    /**
     * Returns true only if the array is associative.
     * @param array $array
     * @return bool True if the array is associative.
     */
    protected function isAssociativeArray($array)
    {
      if (!is_array($array)) {
        return false;
      }
      $keys = array_keys($array);
      foreach ($keys as $key) {
        if (is_string($key)) {
          return true;
        }
      }
      return false;
    }

    /**
     * Verify if $obj is an array.
     * @throws Dadata_Exception Thrown if $obj isn't an array.
     * @param array $obj Items that should be validated.
     * @param string $method Method expecting an array as an argument.
     */
    public function assertIsArray($obj, $method)
    {
      if ($obj && !is_array($obj)) {
        throw new Dadata_Exception(
            "Incorrect parameter type passed to $method(). Expected an array."
        );
      }
    }

    public function offsetExists($offset)
    {
      return isset($this->$offset) || isset($this->modelData[$offset]);
    }

    public function offsetGet($offset)
    {
      return isset($this->$offset) ?
          $this->$offset :
          $this->__get($offset);
    }

    public function offsetSet($offset, $value)
    {
      if (property_exists($this, $offset)) {
        $this->$offset = $value;
      } else {
        $this->modelData[$offset] = $value;
        $this->processed[$offset] = true;
      }
    }

    public function offsetUnset($offset)
    {
      unset($this->modelData[$offset]);
    }

    protected function keyType($key)
    {
      return $key . "Type";
    }

    protected function dataType($key)
    {
      return $key . "DataType";
    }

    public function __isset($key)
    {
      return isset($this->modelData[$key]);
    }

    public function __unset($key)
    {
      unset($this->modelData[$key]);
    }
  
    /**
    * Initialize this object's properties from an array.
    *
    * @param array $properties Used to seed this object's properties.
    * @return void
    */
    protected function mapTypes($props)
    {
        // Hard initilise simple types, lazy load more complex ones.
        $reflection = new ReflectionObject($this);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        $props = $this->applyMappedNames($props);
        foreach ($properties as $property) {
            $name = $property->getName();
            if (isset($props[$name])) {
                $this->$name = $props[$name];
                unset($props[$name]);
            } elseif (isset($props[$camelKey = mb_strtolower($name, 'utf-8')])) {
                // This checks if property exists as camelCase, leaving it in array as snake_case
                // in case of backwards compatibility issues.
                $this->$camelKey = $props[$camelKey];
                unset($props[$camelKey]);
            }
        }
        foreach($props as $key => $val){
            $setter = 'set' . ucfirst(mb_strtolower($key, 'utf-8'));
            if(method_exists($this, $setter)) {
                // This checks if property setter exists                
                $this->$setter($val);
                unset($props[$key]);
            }
        }
        $this->modelData = $props;
    }
    
    /**
    * Check whether the value is the null placeholder and return true null.
    */
    private function nullPlaceholderCheck($value)
    {
      if ($value === self::NULL_VALUE) {
        return null;
      }
      return $value;
    }
    
    /**
    * If there is an internal name mapping, use that.
    */
    private function applyMappedNames($array){
        $newArray = array();
        foreach($array as $key => $value){
            $key = $this->getMappedName($key);
            $newArray[$key] = $value;
        }
        return $newArray;
    }
    
    /**
    * If there is an internal name mapping, use that.
    */
    private function getMappedName($key)
    {
      if (isset($this->internal_gapi_mappings) &&
          isset($this->internal_gapi_mappings[$key])) {
        $key = $this->internal_gapi_mappings[$key];
      }
      return $key;
    }
    
    /**
    * Given a variable name, discover its type.
    *
    * @param $name
    * @param $item
    * @return object The object from the item.
    */
   private function createObjectFromName($name, $item)
   {
     $type = $this->$name;
     return new $type($item);
   }
    
    /**
    * Handle different types of values, primarily
    * other objects and map and array data types.
    */
   private function getSimpleValue($value)
   {
     if ($value instanceof Dadata_Model) {
       return $value->toSimpleObject();
     } else if (is_array($value)) {
       $return = array();
       foreach ($value as $key => $a_value) {
         $a_value = $this->getSimpleValue($a_value);
         if ($a_value !== null) {
           $key = $this->getMappedName($key);
           $return[$key] = $this->nullPlaceholderCheck($a_value);
         }
       }
       return $return;
     }
     return $value;
   }
}