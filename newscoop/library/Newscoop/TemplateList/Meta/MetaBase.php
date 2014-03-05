<?php

/**
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\TemplateList\Meta;

use Doctrine\Common\Inflector\Inflector;

/**
 * Modern Meta class
 */
abstract class MetaBase
{
    protected $dataObject;

    public function __construct($dataObject = null)
    {
        $this->dataObject = $dataObject;
    }

    protected function getPropertiesMap()
    {
        return array();
    }

    public function __get($property)
    {
        if (count($this->getPropertiesMap()) > 0) {
            $map = $this->getPropertiesMap();
            if (array_key_exists($property, $map)) {
                $methodName = $map[$property];

                if (method_exists($this, $methodName)) {
                    return $this->$methodName();
                } elseif (method_exists($this->dataObject, $methodName)) {
                    return $this->dataObject->$methodName();
                }
            }

            throw new \LogicException("Undefined ".$property." property.");
        }

        $methodName = 'get'.\Doctrine\Common\Util\Inflector::classify($property);

        if (method_exists($this, $methodName)) {
            return $this->$methodName();
        } elseif (property_exists($this, $property)) {
            return $this->$property;
        } elseif (method_exists($this->dataObject, $methodName)) {
            return $this->dataObject->$methodName();
        } elseif (property_exists($this->dataObject, $property)) {
            return $this->dataObject->$property;
        }

        throw new \LogicException("Undefined ".$methodName." method or missing ".$property." property.");
    }
}
