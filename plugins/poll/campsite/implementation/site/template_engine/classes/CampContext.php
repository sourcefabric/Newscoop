<?php
/**
 * @package Campsite
 */


/**
 * Includes
 */
// We indirectly reference the DOCUMENT_ROOT so we can enable
// scripts to use this file from the command line, $_SERVER['DOCUMENT_ROOT']
// is not defined in these cases.
$g_documentRoot = $_SERVER['DOCUMENT_ROOT'];

require_once($g_documentRoot.'/template_engine/include/constants.php');


/**
 * definition of CampContext class
 */
final class CampContext {
	// Defines the object types
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
								   'subscription'=>'Subscription',
								   'poll'=>'Poll',
								   'pollanswer'=>'PollAnswer',
                                   'url'=>'URL'
								   );

	// Defines the list objects
	private $m_listObjects = array(
	                         'issues'=>array('class'=>'Issues', 'list'=>'issues'),
	                         'sections'=>array('class'=>'Sections', 'list'=>'sections'),
	                         'articles'=>array('class'=>'Articles', 'list'=>'articles'),
	                         'articleattachments'=>array('class'=>'ArticleAttachments',
	                                                     'list'=>'article_attachments'),
	                         'articlecomments'=>array('class'=>'ArticleComments',
	                                                  'list'=>'article_comments'),
	                         'articleimages'=>array('class'=>'ArticleImages',
	                                                'list'=>'article_images'),
	                         'articletopics'=>array('class'=>'ArticleTopics',
	                                                'list'=>'article_topics'),
	                         'articleaudioattachments'=>array('class'=>'ArticleAudioAttachments',
	                                                          'list'=>'article_audio_attachments'),
	                         'searchresults'=>array('class'=>'SearchResults',
	                                                'list'=>'search_results'),
	                         'subtopics'=>array('class'=>'Subtopics', 'list'=>'subtopics'),
	                         'subtitles'=>array('class'=>'Subtitles', 'list'=>'subtitles'),
	                         'polls'=>array('class'=>'Polls', 'list'=>'polls'),
	                         'pollanswers'=>array('class'=>'PollAnswers', 'list'=>'pollanswers'),
	                         );

    // Stores the context objects.
	private $m_objects = array();

    // Stores the context properties.
    private $m_properties = null;

    // Stores the readonly properties; the users can't modify them directly.
    private $m_readonlyProperties = null;

    // Stores a given list of properties at the beginning of each list block
    private $m_savedProperties = array();


    /**
     * constructor
     *
     */
    final public function __construct()
    {
        if (!is_null($this->m_properties)) {
			return;
		}

        $this->m_properties['htmlencoding'] = false;
        // ...
        // complete list of misc properties
        // ...

        $this->m_readonlyProperties['lists'] = array();
        $this->m_readonlyProperties['issues_lists'] = array();
        $this->m_readonlyProperties['sections_lists'] = array();
        $this->m_readonlyProperties['articles_lists'] = array();
        $this->m_readonlyProperties['article_attachments_lists'] = array();
        $this->m_readonlyProperties['polls'] = array();
    } // fn __construct


    /**
     * Overloaded method call to give access to context properties.
     *
     * @param string $p_element - the property name
     * @return mix - the property value
     */
    final public function __get($p_element)
    {
        try {
	    	$p_element = CampContext::TranslateProperty($p_element);

	    	// Verify if an object of this type exists
        	if (array_key_exists($p_element, $this->m_objectTypes)) {
                if (!isset($this->m_objects[$p_element])
        			    || is_null($this->m_objects[$p_element])) {
                    $this->createObject($p_element);
                }
                return $this->m_objects[$p_element];
            }

            // Verify if a property with this name exists
            if (is_array($this->m_properties)
                    && array_key_exists($p_element, $this->m_properties)) {
                return $this->m_properties[$p_element];
            }

            // Verify if a readonly property with this name exists
            if (is_array($this->m_readonlyProperties)
                    && array_key_exists($p_element, $this->m_readonlyProperties)) {
                return $this->m_readonlyProperties[$p_element];
            }

            // No object of this type of property with this name exist.
            $this->trigger_invalid_property_error($p_element);
        } catch (InvalidObjectException $e) {
        	$this->trigger_invalid_object_error($e->getClassName());
        }
		return null;
    } // fn __get


    /**
     * Overloade method call to set the context properties.
     *
     * @param string $p_element - property name
     * @param string $p_value - value of the property
     * @return mix - the property value
     */
    final public function __set($p_element, $p_value)
    {
    	$p_element = CampContext::TranslateProperty($p_element);

    	// Verify if an object of this type exists
    	if (array_key_exists($p_element, $this->m_objectTypes)) {
	    	try {
                if (!is_object($p_value)) {
                    throw new InvalidObjectException($p_element);
                }

                $classFullPath = camp_find_class('Meta'.$this->m_objectTypes[$p_element], 'template_engine/metaclasses');
                
                if (!file_exists($classFullPath)) {
                    throw new InvalidObjectException($p_element);
                }
                require_once($classFullPath);

                if (!is_a($p_value, 'Meta'.$this->m_objectTypes[$p_element])) {
                    throw new InvalidObjectException($p_element);
                }

                return $this->m_objects[$p_element] = $p_value;
	    	} catch (InvalidObjectException $e) {
    	        $this->trigger_invalid_object_error($e->getClassName());
        	    return null;
    		}
        }

        // Verify if a property with this name exists
		if (is_array($this->m_properties)
				&& array_key_exists($p_element, $this->m_properties)) {
			return $this->m_properties[$p_element] = $p_value;
		}

		// No object of this type of property with this name exist.
		$this->trigger_invalid_property_error($p_element);
		return null;
    } // fn __set


	/**
	 * Returns true if the given property exists.
	 *
	 * @param string $p_property
	 */
    public function hasProperty($p_property)
    {
    	return array_key_exists($p_property, $this->m_objectTypes)
    			|| (is_array($this->m_properties)
					&& array_key_exists($p_property, $this->m_properties))
    			|| (is_array($this->m_readonlyProperties)
					&& array_key_exists($p_property, $this->m_readonlyProperties));
    }


    /**
     * Returns the object name from the list class name.
     *
     * @param string $p_listClassName
     * @return string
     */
    private function GetListObjectName($p_listClassName)
    {
        $nameLength = strlen($p_listClassName);
        if ($nameLength <= 4) {
            return '';
        }
        $tail = substr($p_listClassName, ($nameLength - 4));
        if (strtolower($tail) != 'list') {
            return '';
        }
        return strtolower(substr($p_listClassName, 0, ($nameLength - 4)));
    }


    /**
     * Returns the list name of the current list.
     *
     * @return string
     *      The name of the list
     */
    public function getCurrentListName()
    {
        if (!isset($this->m_readonlyProperties['current_list'])
                || count($this->m_readonlyProperties['lists']) == 0) {
            return null;
        }

        $objectName = $this->GetListObjectName(get_class($this->m_readonlyProperties['current_list']));
        $listName = $this->m_listObjects[$objectName]['list'];

        return 'current_'.$listName.'_list';
    } // fn getCurrentListName


    /**
     * Saves a given list of properties
     *
     * @param array $p_propertiesList
     */
    private function SaveProperties(array $p_propertiesList)
    {
        $savedProperties = array();
        foreach ($p_propertiesList as $propertyName) {
            if (!$this->hasProperty($propertyName)) {
                continue;
            }
            $savedProperties[$propertyName] = $this->$propertyName;
        }
        array_push($this->m_savedProperties, $savedProperties);
    }


    /**
     * Restores the last list of properties from the stack
     *
     */
    private function RestoreProperties()
    {
        if (empty($this->m_savedProperties)) {
            return;
        }
        $savedProperties = array_pop($this->m_savedProperties);
        foreach ($savedProperties as $propertyName=>$propertyValue) {
            $this->$propertyName = $propertyValue;
        }
    }


    /**
     * Sets the current list.
     *
     * @param object $p_list
     * @return void
     */
	public function setCurrentList(&$p_list, array $p_savePropertiesList = array())
    {
    	if (!is_object($p_list)) {
    		throw new InvalidObjectException($p_list);
    	}

        $objectName = $this->GetListObjectName(get_class($p_list));
        if ($objectName == '' || !isset($this->m_listObjects[$objectName])) {
            throw new InvalidObjectException(get_class($p_list));
        }

    	$listObjectName = $this->m_listObjects[$objectName]['class'].'List';
    	if (!is_a($p_list, $listObjectName)) {
    		throw new InvalidObjectException(get_class($p_list));
    	}

   	    $this->SaveProperties($p_savePropertiesList);

    	$listName = $this->m_listObjects[$objectName]['list'];
    	$this->m_readonlyProperties['lists'][] =& $p_list;
    	$this->m_readonlyProperties['current_list'] =& $p_list;
    	$this->m_readonlyProperties[$listName.'_lists'][] =& $p_list;
    	$this->m_readonlyProperties['current_'.$listName.'_list'] =& $p_list;
    }


    /**
     * Resets the current list.
     *
     * @return void
     */
    public function resetCurrentList()
    {
        if (!isset($this->m_readonlyProperties['current_list'])
                || count($this->m_readonlyProperties['lists']) == 0) {
            return;
        }

        $this->RestoreProperties();

   	    $this->m_readonlyProperties['current_list'] = array_pop($this->m_readonlyProperties['lists']);

        $objectName = $this->GetListObjectName(get_class($this->m_readonlyProperties['current_list']));
    	$listName = $this->m_listObjects[$objectName]['list'];

	    if (count($this->m_readonlyProperties[$listName.'_lists']) == 0) {
	        return;
	    }
       	$this->m_readonlyProperties['current_'.$listName.'_list'] = array_pop($this->m_readonlyProperties[$listName.'_lists']);
    }


    /**
     * Creates an object of the given type. Returns the created object.
     *
     * @param string $p_objectType
     * @return object
     */
    private function createObject($p_objectType)
    {
    	global $_SERVER;

    	$p_objectType = CampContext::TranslateProperty($p_objectType);

    	$classFullPath = camp_find_class('Meta'.$this->m_objectTypes[$p_element], 'template_engine/metaclasses');
    	
    	if (!file_exists($classFullPath)) {
    		throw new InvalidObjectException($p_objectType);
    	}
    	require_once($classFullPath);

    	$className = 'Meta'.$this->m_objectTypes[$p_objectType];
    	$this->m_objects[$p_objectType] = new $className;

    	return $this->m_objects[$p_objectType];
    } // fn createObject


    /**
     * Processes a property name; returns a valid property name.
     *
     * @param string $p_property
     * @return string
     */
    static function TranslateProperty($p_property)
    {
    	return strtolower($p_property);
    } // fn TranslateProperty


    /**
     * Triggers an invalid object error.
     *
     * @param string $p_object - object name
     */
    final protected function trigger_invalid_object_error($p_object)
    {
		CampTemplate::singleton()->trigger_error(INVALID_OBJECT_STRING . " $p_object ");
    } // fn trigger_invalid_object_error


	/**
	 * Triggers an invalid property error.
	 *
	 * @param string $p_property - property name
	 */
    final protected function trigger_invalid_property_error($p_property)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
                      . OF_OBJECT_STRING . ' ' . get_class($this);
		CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    } // fn trigger_invalid_property_error

} // class CampContext

?>