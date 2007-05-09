<?php
/**
 * @package Campsite
 */


define('INVALID_OBJECT_STRING', 'invalid object');
define('INVALID_PROPERTY_STRING', 'invalid property');
define('OF_OBJECT_STRING', 'of object');


/**
 * @package Campsite
 */
final class CampContext {
    //
    private $m_properties = null;
    //
    private $m_objectTypes = array('publication'=>'Publication',
								   'issue'=>'Issue',
								   'section'=>'Section',
								   'article'=>'Article',
								   'language'=>'Language',
								   'image'=>'Image',
								   'attachment'=>'Attachment',
								   'audioclip'=>'Audioclip',
								   'comment'=>'Comment',
								   'topic'=>'Topic',
								   'user'=>'User',
								   'template'=>'Template',
								   'subscription'=>'Subscription'
								   );
    //
	private $m_objects = array();


    /**
     *
     */
    final public function __construct()
    {
        if (!is_null($this->m_properties)) {
			return;
		}

        $this->m_properties['htmlencoding'] = false;
        // ...
        // complet list of misc properties
        // ...
    } // fn __construct


    /**
     *
     */
    final public function __get($p_element)
    {
        try {
	    	$p_element = CampContext::TranslateProperty($p_element);

        	if (array_key_exists($p_element, $this->m_objectTypes)) {
                if (!isset($this->m_objects[$p_element])
        			    || is_null($this->m_objects[$p_element])) {
                    $this->createObject($p_element);
                }
                return $this->m_objects[$p_element];
            }

            if (is_array($this->m_properties)
                    && array_key_exists($p_element, $this->m_properties)) {
                return $this->m_properties[$p_element];
            }

            throw new InvalidPropertyException(get_class($this), $p_element);
        } catch (InvalidPropertyException $e) {
            $this->trigger_invalid_property_error($p_element);
            return null;
        }
    } // fn __get


    /**
     *
     */
    final public function __set($p_element, $p_value)
    {
    	$p_element = CampContext::TranslateProperty($p_element);

    	try {
	    	if (array_key_exists($p_element, $this->m_objectTypes)) {
                if (!is_object($p_value)) {
                    throw new InvalidObjectException($p_element);
                }

                $classFullPath = $_SERVER['DOCUMENT_ROOT'].'/template_engine/Meta'
                               . $this->m_objectTypes[$p_element].'.php';
                if (!file_exists($classFullPath)) {
                    throw new InvalidObjectException($p_element);
                }
                require_once($classFullPath);

                if (!is_a($p_value, 'Meta'.$this->m_objectTypes[$p_element])) {
                    throw new InvalidObjectException($p_element);
                }

                return $this->m_objects[$p_element] = $p_value;
            }

            try {
                if (is_array($this->m_properties)
                        && array_key_exists($p_element, $this->m_properties)) {
                    return $this->m_properties[$p_element] = $p_value;
                }

                throw new InvalidPropertyException(get_class($this), $p_element);
            } catch (InvalidPropertyException $e) {
                $this->trigger_invalid_property_error($p_element);
                return null;
            }
    	} catch (InvalidObjectException $e) {
            $this->trigger_invalid_object_error($e->getClassName());
            return null;
    	}
    } // fn __set


    /**
     *
     */
    private function createObject($p_objectType)
    {
    	global $_SERVER;

    	$p_objectType = CampContext::TranslateProperty($p_objectType);

    	$classFullPath = $_SERVER['DOCUMENT_ROOT'].'/template_engine/Meta'
    					. $this->m_objectTypes[$p_objectType].'.php';
    	if (!file_exists($classFullPath)) {
    		throw new InvalidObjectException($p_objectType);
    	}
    	require_once($classFullPath);

    	$className = 'Meta'.$p_objectType;
    	$this->m_objects[$p_objectType] =& new $className;

    	return $this->m_objects[$p_objectType];
    } // fn createObject


    static function TranslateProperty($p_property)
    {
    	return strtolower($p_property);
    } // fn TranslateProperty


    /**
     *
     */
    final protected function trigger_invalid_object_error($p_object)
    {
		CampTemplate::singleton()->trigger_error(INVALID_OBJECT_STRING . " $p_object ");
    } // fn trigger_invalid_object_error


    /**
     *
     */
    final protected function trigger_invalid_property_error($p_property)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
                      . OF_OBJECT_STRING . ' ' . get_class($this);
		CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    } // fn trigger_invalid_property_error

} // class CampContext

?>