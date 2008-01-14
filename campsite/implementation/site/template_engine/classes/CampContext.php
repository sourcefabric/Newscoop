<?php
/**
 * @package Campsite
 *
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


/**
 * Class CampContext
 */
final class CampContext
{
    // Defines the object types
    private static $m_objectTypes = array(
								    'language'=>array('class'=>'Language',
								                      'handler'=>'setLanguageHandler'),
    								'publication'=>array('class'=>'Publication',
                                                         'handler'=>'setPublicationHandler'),
								    'issue'=>array('class'=>'Issue',
								                   'handler'=>'setIssueHandler'),
								    'section'=>array('class'=>'Section',
								                     'handler'=>'setSectionHandler'),
								    'article'=>array('class'=>'Article',
								                     'handler'=>'setArticleHandler'),
								    'image'=>array('class'=>'Image'),
								    'attachment'=>array('class'=>'Attachment'),
								    'audioclip'=>array('class'=>'Audioclip'),
								    'comment'=>array('class'=>'Comment'),
								    'subtitle'=>array('class'=>'Subtitle'),
								    'topic'=>array('class'=>'Topic'),
								    'user'=>array('class'=>'User'),
								    'template'=>array('class'=>'Template'),
								    'subscription'=>array('class'=>'Subscription')
    );

    // Defines the list objects
    private $m_listObjects = array(
	                         'issues'=>array('class'=>'Issues', 'list'=>'issues'),
	                         'sections'=>array('class'=>'Sections', 'list'=>'sections'),
	                         'articles'=>array('class'=>'Articles', 'list'=>'articles'),
	                         'articleimages'=>array('class'=>'ArticleImages',
	                                                'list'=>'article_images'),
    						 'articleattachments'=>array('class'=>'ArticleAttachments',
	                                                     'list'=>'article_attachments'),
	                         'articleaudioattachments'=>array('class'=>'ArticleAudioAttachments',
	                                                          'list'=>'article_audio_attachments'),
    						 'articlecomments'=>array('class'=>'ArticleComments',
	                                                  'list'=>'article_comments'),
	                         'subtitles'=>array('class'=>'Subtitles', 'list'=>'subtitles'),
    						 'articletopics'=>array('class'=>'ArticleTopics',
	                                                'list'=>'article_topics'),
	                         'searchresults'=>array('class'=>'SearchResults',
	                                                'list'=>'search_results'),
	                         'subtopics'=>array('class'=>'Subtopics', 'list'=>'subtopics')
    );

    /**
     * Stores the context objects.
     *
     * @var array
     */
    private $m_objects = array();

    /**
     * Stores the context properties.
     *
     * @var array
     */
    private $m_properties = null;

    /**
     * Stores the readonly properties; the users can't modify them directly
     *
     * @var array
     */
    private $m_readonlyProperties = null;

    /**
     * Stores a given list of properties at the beginning of each list block
     *
     * @var array
     */
    private $m_savedProperties = array();

    /**
     * Stores the current context at the beginning of each local block
     *
     * @var array
     */
    private $m_savedContext = array();


    /**
     * Class constructor
     */
    final public function __construct()
    {
        global $Campsite;

        if (!is_null($this->m_properties)) {
            return;
        }

        $this->m_properties['htmlencoding'] = false;
        $this->m_properties['body_field_article_type'] = null;
        $this->m_properties['body_field_name'] = null;

        $this->m_readonlyProperties['version'] = $Campsite['VERSION'];

        $this->m_readonlyProperties['lists'] = array();
        $this->m_readonlyProperties['issues_lists'] = array();
        $this->m_readonlyProperties['sections_lists'] = array();
        $this->m_readonlyProperties['articles_lists'] = array();
        $this->m_readonlyProperties['article_attachments_lists'] = array();

        $url = new MetaURL();
        $this->m_readonlyProperties['url'] = new MetaURL();
        $this->publication = $url->publication;
        $this->language = $url->language;
        $this->issue = $url->issue;
        $this->section = $url->section;
        $this->article = $url->article;

        $this->m_readonlyProperties['default_language'] = $this->language;
        $this->m_readonlyProperties['default_publication'] = $this->publication;
        $this->m_readonlyProperties['default_issue'] = $this->issue;
        $this->m_readonlyProperties['default_section'] = $this->section;
        $this->m_readonlyProperties['default_article'] = $this->article;
        $this->m_readonlyProperties['default_topic'] = $this->topic;
        $this->m_readonlyProperties['default_url'] = new MetaURL();

        $userId = CampRequest::GetVar('LoginUserId');
        if (!is_null($userId)) {
            $user = new User($userId);
            if ($user->exists()
            && $user->getKeyId() == CampRequest::GetVar('LoginUserKey')) {
                $this->user = new MetaUser($userId);
            }
        }

        $this->m_readonlyProperties['request_action'] = MetaAction::CreateAction(CampRequest::GetInput());
        $this->m_readonlyProperties['request_action']->takeAction($this);

        $availableActions = CampContext::ReadAvailableActions();
        foreach (CampContext::ReadAvailableActions() as $actionName) {
            $actionName = strtolower($actionName);
            $propertyName = CampContext::TranslateProperty($actionName . '_action');
            if ($this->m_readonlyProperties['request_action']->name == $actionName) {
                $this->m_readonlyProperties[$propertyName] =& $this->m_readonlyProperties['request_action'];
            } else {
                $this->m_readonlyProperties[$propertyName] = MetaAction::CreateAction(array());
            }
        }
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
            if (!is_null(CampContext::ObjectType($p_element))) {
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
        if (!is_null(CampContext::ObjectType($p_element))) {
            try {
                if (!is_object($p_value)) {
                    throw new InvalidObjectException($p_element);
                }

                $classFullPath = $_SERVER['DOCUMENT_ROOT'].'/template_engine/metaclasses/'
                . CampContext::ObjectType($p_element).'.php';
                if (!file_exists($classFullPath)) {
                    throw new InvalidObjectException($p_element);
                }
                require_once($classFullPath);

                $metaclass = CampContext::ObjectType($p_element);
                if (!is_a($p_value, $metaclass)) {
                    throw new InvalidObjectException($p_element);
                }

                if (isset($this->m_objects[$p_element])
                && !is_null($this->m_objects[$p_element])) {
                    $oldValue = $this->m_objects[$p_element];
                } else {
                    $oldValue = new $metaclass;
                }
                $this->m_objects[$p_element] = $p_value;

                if (isset(CampContext::$m_objectTypes[$p_element]['handler'])) {
                    $setHandler = CampContext::$m_objectTypes[$p_element]['handler'];
                    $this->$setHandler($oldValue, $p_value);
                }

                return $this->m_objects[$p_element];
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
        return !is_null(CampContext::ObjectType($p_property))
        || (is_array($this->m_properties)
        && array_key_exists($p_property, $this->m_properties))
        || (is_array($this->m_readonlyProperties)
        && array_key_exists($p_property, $this->m_readonlyProperties));
    } // fn hasProperty


    /**
     * Returns true if the given object exists.
     *
     * @param string $p_object
     */
    public function hasObject($p_object)
    {
        return !is_null(CampContext::ObjectType($p_object));
    } // fn hasObject


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
    } // fn GetListObjectName


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
     *
     * @return void
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
    } // fn SaveProperties


    /**
     * Restores the last list of properties from the stack
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
    } // fn RestoreProperties


    /**
     * Saves the current context objects.
     *
     * @param array $p_objectsList
     *
     * @return void
     */
    public function saveCurrentContext(array $p_propertiesList = array())
    {
        if (count($p_propertiesList) == 0) {
            $p_propertiesList = $this->allPropertiesNames();
        }
        $savedContext = array();
        foreach ($p_propertiesList as $propertyName) {
            if ($this->hasProperty($propertyName)) {
                $savedContext[$propertyName] = $this->$propertyName;
            }
        }
        array_push($this->m_savedContext, $savedContext);
    } // fn saveCurrentContext


    /**
     * Restores the global context.
     */
    public function restoreContext()
    {
        if (empty($this->m_savedContext)) {
            return;
        }
        $savedContext = array_pop($this->m_savedContext);
        foreach ($savedContext as $propertyName => $propertyValue) {
            $this->$propertyName = $propertyValue;
        }
    } // fn restoreContext


    /**
     * Returns the list of all properties
     *
     * @return array
     */
    public function allPropertiesNames()
    {
        return array_merge(array_keys($this->m_objects),
        array_keys($this->m_properties));
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
    } // fn setCurrentList


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
    } // fn resetCurrentList


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

        $classFullPath = $_SERVER['DOCUMENT_ROOT'].'/template_engine/metaclasses/'
        . CampContext::ObjectType($p_objectType).'.php';
        if (!file_exists($classFullPath)) {
            throw new InvalidObjectException($p_objectType);
        }
        require_once($classFullPath);

        $className = CampContext::ObjectType($p_objectType);
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


    /**
     * Handler for the language change event.
     *
     * @param MetaLanguage $p_oldLanguage
     */
    private function setLanguageHandler(MetaLanguage $p_oldLanguage, MetaLanguage $p_newLanguage)
    {
        if ($p_newLanguage == $p_oldLanguage) {
            return;
        }
        $this->m_readonlyProperties['url']->language = $p_newLanguage;
    }


    /**
     * Handler for the publication change event.
     *
     * @param MetaPublication $p_oldPublication
     */
    private function setPublicationHandler(MetaPublication $p_oldPublication,
    MetaPublication $p_newPublication)
    {
        if ($p_newPublication == $p_oldPublication) {
            return;
        }
        $this->issue = new MetaIssue();
        $this->m_readonlyProperties['url']->publication = $p_newPublication;
    }


    /**
     * Handler for the issue change event.
     *
     * @param MetaIssue $p_oldIssue
     */
    private function setIssueHandler(MetaIssue $p_oldIssue, MetaIssue $p_newIssue)
    {
        if ($p_newIssue == $p_oldIssue) {
            return;
        }
        $this->section = new MetaSection();
        $this->m_readonlyProperties['url']->issue = $p_newIssue;
    }


    /**
     * Handler for the section change event.
     *
     * @param MetaSection $p_oldSection
     */
    private function setSectionHandler(MetaSection $p_oldSection, MetaSection $p_newSection)
    {
        if ($p_newSection == $p_oldSection) {
            return;
        }
        $this->article = new MetaArticle();
        $this->m_readonlyProperties['url']->section = $p_newSection;
    }


    /**
     * Handler for the article change event.
     *
     * @param MetaArticle $p_oldArticle
     */
    private function setArticleHandler(MetaArticle $p_oldArticle, MetaArticle $p_newArticle)
    {
        if ($p_newArticle == $p_oldArticle) {
            return;
        }
        $this->subtitle = new MetaSubtitle();
        $this->image = new MetaImage();
        $this->attachment = new MetaAttachment();
        $this->audioclip = new MetaAudioclip();
        $this->comment = new MetaComment();
        $this->m_readonlyProperties['url']->article = $p_newArticle;
    }


    /**
     * Returns the name corresponding to the given property; null if
     * the property is not an object.
     *
     * @param string $p_property
     */
    public static function ObjectType($p_property)
    {
        $p_property = CampContext::TranslateProperty($p_property);

        // Verify if an object of this type exists
        if (array_key_exists($p_property, CampContext::$m_objectTypes)) {
            return 'Meta'.CampContext::$m_objectTypes[$p_property]['class'];
        }
        return null;
    }


    /**
     * Searches for classes that process actions. Returns an array of
     * action names.
     *
     * @return array
     */
    private static function ReadAvailableActions()
    {
        require_once('File/Find.php');

        $actions = array();
        $directoryPath = $_SERVER['DOCUMENT_ROOT'].'/template_engine/metaclasses';
        $actionIncludeFiles = File_Find::search('MetaAction*.php', $directoryPath, 'shell', false);
        foreach ($actionIncludeFiles as $includeFile) {
            require_once($includeFile);
            if (preg_match('/MetaAction([^.]+)\.php/', $includeFile, $matches) == 0
            || strtolower($matches[1]) == 'request') {
                continue;
            }
            $actionName = $matches[1];
            if (class_exists('MetaAction'.$actionName)) {
                $actions[] = $actionName;
            }
        }
        return $actions;
    }

} // class CampContext

?>