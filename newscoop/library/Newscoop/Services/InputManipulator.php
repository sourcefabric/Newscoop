<?php
/**
 * @package Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

/**
 * Input Manipulator service
 */
class InputManipulator
{
    /**
     * cleanMagicQuotes
     * @param array $array object to clean from quotes
     *
     * @return array $gpcList cleaned object
     */

    public static function cleanMagicQuotes(array $array)
    {
       $gpcList = array();

       foreach ($array as $key => $value) {
           $decodedKey = stripslashes($key);
           if (is_array($value)) {
               $decodedValue = self::cleanMagicQuotes($value);
           } else {
               $decodedValue = stripslashes($value);
           }
           $gpcList[$decodedKey] = $decodedValue;
       }

       return $gpcList;
    }

    /**
     * getVar searches and returns the variable from an array or string in the requested type.
     *
     * @param array                $parameters
     * @param array|string         $parameters['inputObject']   haystack
     * @param string               $parameters['variableName']  needle
     * @param string               $parameters['variableType']  type the variable should have (string, int, array, boolean)
     * @param string|int|bool|null $parameters['defaultValue']  default value for the variable if it doesn't exist
     * @param bool                 $parameters['checkIfExists'] check if the variable exists (for if statements, only returns BOOL)
     * @param bool                 $parameters['ignoreErrors']  ignore errors, return empty
     *
     * @return string|int|bool the variable found or a bool with checkIfExists
     */

    public static function getVar(array $parameters = array())
    {
        $requiredParams = array('inputObject', 'variableName');
        $defaultParams = array(
                                'variableType' => 'string',
                                'defaultValue' => null,
                                'checkIfExists' => false,
                                'ignoreErrors' => false
                            );
        foreach ($requiredParams as $requiredParam) {
            if (!array_key_exists($requiredParam, $parameters)) {
                throw new \InvalidArgumentException(__METHOD__.': Parameter '.$requiredParam.' is required.');
            }
        }

        foreach ($defaultParams as $defaultParam => $defaultValue) {
            if (!array_key_exists($defaultParam, $parameters)) {
                $parameters[$defaultParam] = $defaultValue;
            }
        }

        // allow the GetVar to also use strings
        if (!is_array($parameters['inputObject'])) {
            $parameters['inputObject'] = array($parameters['variableName'] => $parameters['inputObject']);
        }

        $parameters['variableType'] = strtolower($parameters['variableType']);

        if (!isset($parameters['inputObject'][$parameters['variableName']])) {
            if ($parameters['checkIfExists']) {
                return false;
            } else {
                return true;
            }
            if (!$parameters['ignoreErrors']) {
                throw new \InvalidArgumentException('"'.$parameters['variableName'].'" is not set');
            }

            return $parameters['defaultValue'];
        }
        // Clean the slashes
        if (get_magic_quotes_gpc()) {
            if (is_array($parameters['inputObject'][$parameters['variableName']])) {
                $parameters['inputObject'][$parameters['variableName']] = self::cleanMagicQuotes($parameters['inputObject'][$parameters['variableName']]);
            } else {
                $parameters['inputObject'][$parameters['variableName']] = stripslashes($parameters['inputObject'][$parameters['variableName']]);
            }
        }
        switch ($parameters['variableType']) {
            case 'boolean':
                $value = strtolower($parameters['inputObject'][$parameters['variableName']]);
                if ( ($value == "true") || (is_numeric($value) && ($value > 0)) ) {
                    return true;
                } else {
                    return false;
                }
                break;
            case 'int':
                if (!is_numeric($parameters['inputObject'][$parameters['variableName']])) {
                    if (!$parameters['ignoreErrors']) {
                        throw new \InvalidArgumentException('"'.$parameters['variableName'].'" Incorrect type. Expected type: "'.$parameters['variableType'].'" got "'.gettype($parameters['inputObject'][$parameters['variableName']]).'" ("'.$parameters['inputObject'][$parameters['variableName']].'") instead.');
                    }

                    return (int) $parameters['defaultValue'];
                }
                break;
            case 'string':
                if (!is_string($parameters['inputObject'][$parameters['variableName']])) {
                    if (!$parameters['ignoreErrors']) {
                        throw new \InvalidArgumentException('"'.$parameters['variableName'].'" Incorrect type. Expected type: "'.$parameters['variableType'].'" got "'.gettype($parameters['inputObject'][$parameters['variableName']]).'" ("'.$parameters['inputObject'][$parameters['variableName']].'") instead.');
                    }

                    return $parameters['defaultValue'];
                }
                break;
            case 'array':
                if (!is_array($parameters['inputObject'][$parameters['variableName']])) {
                    $newArray = array();
                    $newArray[] = $parameters['inputObject'][$parameters['variableName']];

                    return $newArray;
                }
                break;
            default:
                throw new \InvalidArgumentException(__METHOD__.': Variable type '.$parameters['variableType'].' is not supported.');
                break;
        }

        return $parameters['inputObject'][$parameters['variableName']];
    }
}
