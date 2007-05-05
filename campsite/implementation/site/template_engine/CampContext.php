<?php
/**
 * @package Campsite
 */


define('INVALID_OBJECT_STRING', 'invalid object');


/**
 * @package Campsite
 */
final class CampContext {
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

	private $m_objects = array();


    /**
     *
     */
    final public function __construct()
    {

    } // fn __construct


    /**
     *
     */
    final public function __get($p_objectType)
    {
        try {
	    	$p_objectType = CampContext::TranslateObjectType($p_objectType);

        	if (!array_key_exists($p_objectType, $this->m_objectTypes)) {
        		throw new InvalidObjectException($p_objectType);
        	}

        	if (!isset($this->m_objects[$p_objectType])
        			|| is_null($this->m_objects[$p_objectType])) {
        		$this->createObject($p_objectType);
        	}

            return $this->m_objects[$p_objectType];
        } catch (InvalidObjectException $e) {
            $this->trigger_invalid_object_error($e->getClassName());
            return null;
        }
    } // fn __get


    /**
     *
     */
    final public function __set($p_objectType, $p_value)
    {
    	$p_objectType = CampContext::TranslateObjectType($p_objectType);

    	try {
	    	if (!array_key_exists($p_objectType, $this->m_objectTypes)) {
    			throw new InvalidObjectException($p_objectType);
    		}

    		if (!is_object($p_value)) {
    			throw new InvalidObjectException($p_objectType);
    		}

	    	$classFullPath = $_SERVER['DOCUMENT_ROOT'].'/template_engine/Meta'
    						. $this->m_objectTypes[$p_objectType].'.php';
    		if (!file_exists($classFullPath)) {
    			throw new InvalidObjectException($p_objectType);
    		}
    		require_once($classFullPath);

    		if (!is_a($p_value, 'Meta'.$this->m_objectTypes[$p_objectType])) {
    			throw new InvalidObjectException($p_objectType);
    		}

    		$this->m_objects[$p_objectType] = $p_value;
    	} catch (InvalidObjectException $e) {
            $this->trigger_invalid_object_error($e->getClassName());
            return null;
    	}

    	return $this->m_objects[$p_objectType];
    } // fn __set


    /**
     *
     */
    private function createObject($p_objectType)
    {
    	global $_SERVER;

    	$p_objectType = CampContext::TranslateObjectType($p_objectType);

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


    static function TranslateObjectType($p_objectType)
    {
    	return strtolower($p_objectType);
    }


    /**
     *
     */
    final protected function trigger_invalid_object_error($p_object)
    {
		CampTemplate::singleton()->trigger_error(INVALID_OBJECT_STRING . " $p_object ");
    } // fn trigger_invalid_object_error

} // class CampContext

?>