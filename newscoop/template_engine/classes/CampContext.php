<?php
/**
 * @package Campsite
 *
 * @author Mugur Rus <mugur.rus@gmail.com>
 * @author Holman Romero <holman.romero@gmail.com>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/../../classes/Browser.php';

/**
 * Class CampContext
 */
final class CampContext
{
    /**
     * dummy login action
     * @var array
     */
    public $login_action;

    /** @var int */
    private $userCount;

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
        'comment'=>array('class'=>'Comment',
			         'handler'=>'setCommentHandler'),
        'subtitle'=>array('class'=>'Subtitle',
                      'handler'=>'setSubtitleHandler'),
        'topic'=>array('class'=>'Topic',
                   'handler'=>'setTopicHandler'),
        'user'=>array('class'=>'User'),
        'template'=>array('class'=>'Template'),
        'location'=>array('class'=>'MapLocation'),
        'author'=>array('class'=>'Author'),
        'list_user' => array(
            'class' => 'User',
        ),
        'community_feed' => array(
            'class' => 'CommunityFeed',
        ),
        'user_comment' => array(
            'class' => 'Comment',
        ),
        'weather_location' => array(
            'class' => 'WeatherStat', 
        ),
    );

    // Defines the list objects
    private $m_listObjects = array(
        'languages'=>array('class'=>'Languages', 'list'=>'languages',
                     'url_id'=>'lang'),
        'issues'=>array('class'=>'Issues', 'list'=>'issues',
			         'url_id'=>'iss'),
        'sections'=>array('class'=>'Sections', 'list'=>'sections',
			           'url_id'=>'sec'),
        'articles'=>array('class'=>'Articles', 'list'=>'articles',
			           'url_id'=>'art'),
        'maparticles'=>array('class'=>'MapArticles', 'list'=>'map_articles',
			           'url_id'=>'mart'),
        'articleauthors'=>array('class'=>'ArticleAuthors',
                             'list'=>'article_authors',
                             'url_id'=>'aas'),
        'articlelocations'=>array('class'=>'ArticleLocations',
                            'list'=>'article_locations',
                            'url_id'=>'alc'),
        'maplocations'=>array('class'=>'MapLocations',
                            'list'=>'map_locations',
                            'url_id'=>'mlc'),
        'articleimages'=>array('class'=>'ArticleImages',
                            'list'=>'article_images',
                            'url_id'=>'aim'),
        'articleattachments'=>array('class'=>'ArticleAttachments',
                                 'list'=>'article_attachments',
                                 'url_id'=>'aat'),
        'articlecomments'=>array('class'=>'ArticleComments',
                              'list'=>'article_comments',
                              'url_id'=>'acm'),
        'subtitles'=>array('class'=>'Subtitles', 'list'=>'subtitles',
                        'url_id'=>'st'),
        'articletopics'=>array('class'=>'ArticleTopics',
                            'list'=>'article_topics', 'url_id'=>'atp'),
        'searchresults'=>array('class'=>'SearchResults',
                            'list'=>'search_results', 'url_id'=>'src'),

        'searchresultssolr' => array(
            'class' => 'SearchResultsSolr',
            'list' => 'search_solr',
            'url_id' => 'solr'
        ),

        'subtopics'=>array('class'=>'Subtopics', 'list'=>'subtopics',
				        'url_id'=>'tp'),
        'images'=>array('class'=>'Images', 'list'=>'images', 'url_id'=>'img'),
        'users' => array(
            'class' => 'Users',
            'list' => 'users',
            'url_id' => 'uid',
        ),
        'communityfeeds' => array(
            'class' => 'CommunityFeeds',
            'list' => 'community_feeds',
            'url_id' => 'cfid',
        ),
        'boxarticles' => array(
            'class' => 'BoxArticles',
            'list' => 'box_articles',
            'url_id' => 'box',
        ),
        'usercomments' => array(
            'class' => 'UserComments',
            'list' => 'user_comments',
            'url_id' => 'cid',
        ),
        'playlist' => array
        (
        	'class' => 'Playlist',
        	'list' => 'playlist',
			'url_id' => 'pls'
        ),
        'weatherstat' => array
        (
            'class' => 'WeatherStat', 
            'list' => 'weather_stat',
            'url_id' => 'wss'
        )
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
     * Stores the list count for each list type for the current request
     *
     * @var array
     */
    private $m_list_count = array();

    private static $m_nullMetaArticle = null;

    private static $m_nullMetaSection = null;

    /** @var Application_Form_Contact */
    public $form_contact;

    /** @var array */
    public $flash_messages = array();

    /**
     * Class constructor
     */
    final public function __construct()
    {
        global $Campsite, $controller;

        if (!is_null($this->m_properties)) {
            return;
        }

        $this->login_action = (object) array(
            'is_error' => false,
            'error_message' => '',
        );

        self::$m_nullMetaArticle = new MetaArticle();
        self::$m_nullMetaSection = new MetaSection();

        // register plugin objects and listobjects
        foreach (CampPlugin::GetPluginsInfo(true) as $info) {
        	if (is_array($info['template_engine']['objecttypes'])) {
        		foreach ($info['template_engine']['objecttypes'] as $objecttype) {
        			$this->registerObjectType($objecttype);
        		}
        	}

        	if (is_array($info['template_engine']['listobjects'])) {
        		foreach ($info['template_engine']['listobjects'] as $listobject) {
        			$this->registerListObject($listobject);
        		}
        	}
        }

        $this->m_properties['htmlencoding'] = false;
        $this->m_properties['subs_by_type'] = null;

        $this->m_readonlyProperties['version'] = $Campsite['VERSION'];

        $this->m_readonlyProperties['current_list'] = null;
        $this->m_readonlyProperties['lists'] = array();
        $this->m_readonlyProperties['prev_list_empty'] = null;

        $this->m_readonlyProperties['default_url'] = new MetaURL();
        $this->m_readonlyProperties['url'] = new MetaURL();
        if (!$this->m_readonlyProperties['default_url']->is_valid) {
        	header('HTTP/1.0 404 Not Found');
        	if (!$this->m_readonlyProperties['url']->language->defined) {
        		$this->m_readonlyProperties['url']->language = $this->m_readonlyProperties['url']->publication->default_language;
        		$this->m_readonlyProperties['default_url'] = $this->m_readonlyProperties['url'];
        	}
        }

        $this->m_objects['user'] = $this->m_readonlyProperties['url']->user;
        $this->m_readonlyProperties['preview'] = $this->m_readonlyProperties['url']->preview;

        if (!$this->m_readonlyProperties['preview']) {
        	if (!$this->m_readonlyProperties['url']->article->is_published) {
        		$this->m_readonlyProperties['default_url']->article = self::$m_nullMetaArticle;
                $this->m_readonlyProperties['url']->article = self::$m_nullMetaArticle;
        	}
            if (!$this->m_readonlyProperties['url']->issue->is_published) {
                $this->m_readonlyProperties['default_url']->article = self::$m_nullMetaArticle;
                $this->m_readonlyProperties['url']->article = self::$m_nullMetaArticle;
                $this->m_readonlyProperties['default_url']->section = self::$m_nullMetaSection;
                $this->m_readonlyProperties['url']->section = self::$m_nullMetaSection;
                $this->m_readonlyProperties['default_url']->issue = new MetaIssue();
                $this->m_readonlyProperties['url']->issue = new MetaIssue();
            }
        }

        $this->m_objects['publication'] = $this->m_readonlyProperties['url']->publication;
        $this->m_objects['language'] = $this->m_readonlyProperties['url']->language;
        $this->m_objects['issue'] = $this->m_readonlyProperties['url']->issue;
        $this->m_objects['section'] = $this->m_readonlyProperties['url']->section;
        $this->m_objects['article'] = $this->m_readonlyProperties['url']->article;
        $this->m_objects['template'] = $this->m_readonlyProperties['url']->template;
        if (is_numeric($this->m_readonlyProperties['url']->get_parameter('tpid'))) {
            $this->m_objects['topic'] = new MetaTopic($this->m_readonlyProperties['url']->get_parameter('tpid'));
        }

        $this->m_readonlyProperties['default_template'] = $this->m_objects['template'];
        $this->m_readonlyProperties['default_language'] = $this->m_objects['language'];
        $this->m_readonlyProperties['default_publication'] = $this->m_objects['publication'];
        $this->m_readonlyProperties['default_issue'] = $this->m_objects['issue'];
        $this->m_readonlyProperties['default_section'] = $this->m_objects['section'];
        $this->m_readonlyProperties['default_article'] = $this->m_objects['article'];
        $this->m_readonlyProperties['default_topic'] = $this->topic;

        if (!is_null($commentId = CampRequest::GetVar('acid'))) {
            $this->m_objects['comment'] = new MetaComment($commentId);
        }

        $this->m_readonlyProperties['request_action'] = MetaAction::CreateAction(CampRequest::GetInput(CampRequest::GetMethod()));
        $requestActionName = $this->m_readonlyProperties['request_action']->name;
        if ($requestActionName != 'default') {
        	$this->m_readonlyProperties['request_action']->takeAction($this);
        }

        foreach (MetaAction::ReadAvailableActions() as $actionName=>$actionAttributes) {
            $propertyName = $actionName . '_action';
            if ($requestActionName == $actionName) {
                $this->m_readonlyProperties[$propertyName] =& $this->m_readonlyProperties['request_action'];
            } else {
                $this->m_readonlyProperties[$propertyName] = MetaAction::DefaultAction();
            }
        }

        // Initialize the default comment attribute at the end, after the
        // submit comment action had run.
        $this->m_readonlyProperties['default_comment'] = $this->comment;

        // add browser info
        $this->m_readonlyProperties['browser'] = new Browser;

        // initialize plugins
        foreach (CampPlugin::GetPluginsInfo(true) as $info) {
            if (function_exists($info['template_engine']['init'])) {
                $plugin_init = $info['template_engine']['init'];
                $plugin_init($this);
            }
        }

        // initialize geo-map holders
        $this->m_properties['map_dynamic_constraints'] = null;
        $this->m_properties['map_dynamic_areas'] = null;
        $this->m_properties['map_dynamic_max_points'] = 0;
        $this->m_properties['map_dynamic_tot_points'] = 0;
        $this->m_properties['map_dynamic_points_raw'] = null;
        $this->m_properties['map_dynamic_points_objects'] = null;
        $this->m_properties['map_dynamic_meta_article_objects'] = null;
        $this->m_properties['map_dynamic_map_label'] = "";
        $this->m_properties['map_dynamic_id_counter'] = 0;
        $this->m_properties['map_common_header_set'] = false;

        if (defined('APPLICATION_PATH')) {
            $options = $controller->getInvokeArg('bootstrap')->getOptions();
            $form = new \Application_Form_Contact();
            $form->setMethod('POST');
            $request = \Zend_Controller_Front::getInstance()->getRequest();
            if ($request->isPost() && $form->isValid($request->getPost())) {
                $email = new \Zend_Mail('utf-8');
                $email->setFrom($form->email->getValue(), $form->first_name->getValue() . ' ' . $form->last_name->getValue())
                    ->setSubject($form->subject->getValue())
                    ->setBodyText($form->message->getValue())
                    ->addTo($options['email']['contact'])
                    ->send();

                $controller->getHelper('flashMessenger')->addMessage("form_contact_done");
                $controller->getHelper('redirector')->gotoUrl($request->getPathInfo());
                exit;
            }

            $this->form_contact = $form;
            $this->flash_messages = $controller->getHelper('flashMessenger')->getMessages();
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

            // Verify if a readonly property with this name exists
            if (is_array($this->m_readonlyProperties)
            && array_key_exists($p_element, $this->m_readonlyProperties)) {
                return $this->m_readonlyProperties[$p_element];
            }

            // Verify if a property with this name exists
            if (is_array($this->m_properties)
            && array_key_exists($p_element, $this->m_properties)) {
                return $this->m_properties[$p_element];
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

                $classFullPath = $GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/'
                . CampContext::ObjectType($p_element).'.php';

                if (file_exists($classFullPath)) {
                    require_once($classFullPath);
                } else {
                    $pluginImplementsClassFullPath = false;

                    foreach (CampPlugin::GetEnabled() as $CampPlugin) {
                        $pluginClassFullPath = $GLOBALS['g_campsiteDir'].'/'.$CampPlugin->getBasePath()
                        .'/template_engine/metaclasses/'.CampContext::ObjectType($p_element).'.php';
                        if (file_exists($pluginClassFullPath)) {
                            $pluginImplementsClassFullPath = $pluginClassFullPath;
                        }
                    }
                    if ($pluginImplementsClassFullPath) {
                        require_once(($pluginImplementsClassFullPath));
                    } else {
                        throw new InvalidObjectException($p_element);
                    }
                }


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

                if (isset(CampContext::$m_objectTypes[$p_element]['handler'])) {
                    $setHandler = CampContext::$m_objectTypes[$p_element]['handler'];
                    $this->$setHandler($oldValue, $p_value);
                } else {
                    $this->m_objects[$p_element] = $p_value;
                }

                return isset($this->m_objects[$p_element]) ? $this->m_objects[$p_element] : null;
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
    public function has_property($p_property)
    {
        $p_property = CampContext::TranslateProperty($p_property);
        return !is_null(CampContext::ObjectType($p_property))
        || array_key_exists($p_property, $this->m_properties)
        || array_key_exists($p_property, $this->m_readonlyProperties);
    } // fn has_property


    /**
     * Returns true if the given object exists.
     *
     * @param string $p_object
     */
    public function has_object($p_object)
    {
    	$p_object = CampContext::TranslateProperty($p_object);
        return !is_null(CampContext::ObjectType($p_object));
    } // fn has_object


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
            if (!$this->has_property($propertyName)) {
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
            if ($this->has_property($propertyName)) {
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
        $propertiesNames = array_keys($this->m_properties);
        $propertiesNames[]= 'language';
        $propertiesNames[]= 'publication';
        $propertiesNames[]= 'issue';
        $propertiesNames[]= 'section';
        $propertiesNames[]= 'article';
        $propertiesNames[]= 'image';
        $propertiesNames[]= 'attachment';
        $propertiesNames[]= 'comment';
        $propertiesNames[]= 'subtitle';
        $propertiesNames[]= 'topic';
        $propertiesNames[]= 'user';
        return $propertiesNames;
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

        $p_list->setId($this->next_list_id($listObjectName));

        $listName = $this->m_listObjects[$objectName]['list'];
        if (!isset($this->m_list_count[$listName.'_lists'])) {
        	$this->m_list_count[$listName.'_lists'] = 1;
        } else {
        	$this->m_list_count[$listName.'_lists']++;
        }

   	    $this->SaveProperties($p_savePropertiesList);

   	    $this->m_readonlyProperties['lists'][] =& $p_list;
   	    $this->m_readonlyProperties['current_list'] =& $p_list;
   	    $this->m_readonlyProperties[$listName.'_lists'][] =& $p_list;
   	    $this->m_readonlyProperties['current_'.$listName.'_list'] =& $p_list;
   	    return $this->m_readonlyProperties;
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

        $this->m_readonlyProperties['prev_list_empty'] = (int)($this->m_readonlyProperties['current_list']->count == 0);

        $this->RestoreProperties();

        $objectName = $this->GetListObjectName(get_class($this->m_readonlyProperties['current_list']));
        $listName = $this->m_listObjects[$objectName]['list'];

        if (count($this->m_readonlyProperties[$listName.'_lists']) > 0) {
            array_pop($this->m_readonlyProperties[$listName.'_lists']);
            $this->m_readonlyProperties['current_'.$listName.'_list'] =
                end($this->m_readonlyProperties[$listName.'_lists']);
        }

        array_pop($this->m_readonlyProperties['lists']);
   	    $this->m_readonlyProperties['current_list'] = end($this->m_readonlyProperties['lists']);
    } // fn resetCurrentList


    /**
     * Returns the corresponding id for the current list
     *
     * @return int - the current list identifier
     */
    public function current_list_id() {
        $listName = $this->m_listObjects[$objectName]['list'];
        if (!isset($this->m_readonlyProperties['current_list'])) {
            return null;
        }
        return $this->m_readonlyProperties['current_list']->id;
    }


    /**
     * Returns the corresponding id for a new list of the given type
     *
     * @param string $p_className
     */
    public function next_list_id($p_className) {
        $objectName = $this->GetListObjectName($p_className);
        if (is_null($objectName) || $objectName == '') {
            return null;
        }
        $listName = $this->m_listObjects[$objectName]['list'];
        $prefix = 'ls-'.$this->m_listObjects[$objectName]['url_id'];
        if (!isset($this->m_list_count[$listName.'_lists'])) {
            return $prefix . '0';
        }
        return $prefix . $this->m_list_count[$listName.'_lists'];
    }


    public function list_id_prefix($p_className) {
        $objectName = $this->GetListObjectName($p_className);
        if (is_null($objectName) || $objectName == '') {
            return null;
        }
        return 'ls-'.$this->m_listObjects[$objectName]['url_id'];
    }


    /**
     * Returns the corresponding list start index for a new list of the given type
     *
     * @param string $p_className
     */
    public function next_list_start($p_className) {
        $nextListId = $this->next_list_id($p_className);
        if (is_null($nextListId)) {
            return null;
        }
        return $this->m_readonlyProperties['default_url']->get_parameter($nextListId);
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

        $classFullPath = $GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/'
        . CampContext::ObjectType($p_objectType).'.php';

        if (file_exists($classFullPath)) {
            require_once($classFullPath);
        } else {
            $pluginImplementsClassFullPath = false;

            foreach (CampPlugin::GetEnabled() as $CampPlugin) {
                $pluginClassFullPath = $GLOBALS['g_campsiteDir'].'/'.$CampPlugin->getBasePath()
                .'/template_engine/metaclasses/'.CampContext::ObjectType($p_objectType).'.php';
                if (file_exists($pluginClassFullPath)) {
                    $pluginImplementsClassFullPath = $pluginClassFullPath;
                }
            }
            if ($pluginImplementsClassFullPath) {
                require_once(($pluginImplementsClassFullPath));
            } else {
                throw new InvalidObjectException($p_objectType);
            }
        }

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
    public static function TranslateProperty($p_property)
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
    final protected function trigger_invalid_property_error($p_property, $p_smarty=null)
    {
        $errorMessage = INVALID_PROPERTY_STRING . " $p_property "
        . OF_OBJECT_STRING . ' campsite';
        CampTemplate::singleton()->trigger_error($errorMessage, $p_smarty);
    } // fn trigger_invalid_property_error


    /**
     * Register an new object type (for plugin)
     *
     * @param array $p_objectType
     * structure: array(object name => object class name)
     */
    final private function registerObjectType(array $p_objectType)
    {
        try {
            // check the structure
            $keys = array_keys($p_objectType);
            $values = array_values($p_objectType);

            if (count($keys) !== 1 || count($values) !== 1) {
                throw new Exception('CampContext::registerObjectType called with malformed parameter: '.print_r($p_objectType));
            }
        } catch (Exception $e) {
            $this->trigger_invalid_register_error($e->getMessage());
        }

        CampContext::$m_objectTypes += $p_objectType;
    }


    /**
     * Register an list object (for plugins)
     *
     * @param array $p_listObject
     * structure: array(list object name => array('class' => class name, 'list' => list class name))
     */
    final private function registerListObject(array $p_listObject)
    {
        try {
            // check the structure
            $keys = array_keys($p_listObject);
            $values = array_values($p_listObject);

            if (count($keys) !== 1 || count($values) !== 1) {
                throw new Exception('CampContext::registerListObject called with malformed parameter: '.print_r($p_listObject));
            }
        } catch (Exception $e) {
            $this->trigger_invalid_register_error($e->getMessage());
        }

        $this->m_listObjects += $p_listObject;
    }


    final protected function trigger_invalid_register_error($p_message)
    {
        CampTemplate::singleton()->trigger_error($p_message);
    }


    /**
     * Returns the language defined in the current context; if it
     * wasn't defined it initializes the language by an empty object.
     * This method was defined because it's faster than using the
     * magic method __get().
     *
     * @return MetaIssue
     */
    final protected function getLanguage() {
        if (!isset($this->m_objects['language'])) {
            $this->createObject('language');
        }
        return $this->m_objects['language'];
    }


    /**
     * Returns the publication defined in the current context; if it
     * wasn't defined it initializes the publication by an empty object.
     * This method was defined because it's faster than using the
     * magic method __get().
     *
     * @return MetaPublication
     */
    final protected function getPublication() {
        if (!isset($this->m_objects['publication'])) {
            $this->createObject('publication');
        }
        return $this->m_objects['publication'];
    }


    /**
     * Returns the issue defined in the current context; if it
     * wasn't defined it initializes the issue by an empty object.
     * This method was defined because it's faster than using the
     * magic method __get().
     *
     * @return MetaIssue
     */
    final protected function getIssue() {
        if (!isset($this->m_objects['issue'])) {
            $this->createObject('issue');
        }
        return $this->m_objects['issue'];
    }


    /**
     * Returns the section defined in the current context; if it
     * wasn't defined it initializes the section by an empty object.
     * This method was defined because it's faster than using the
     * magic method __get().
     *
     * @return MetaIssue
     */
    final protected function getSection() {
        if (!isset($this->m_objects['section'])) {
            $this->createObject('section');
        }
        return $this->m_objects['section'];
    }


    /**
     * Returns the article defined in the current context; if it
     * wasn't defined it initializes the article by an empty object.
     * This method was defined because it's faster than using the
     * magic method __get().
     *      *
     * @return MetaIssue
     */
    final protected function getArticle() {
        if (!isset($this->m_objects['article'])) {
            $this->createObject('article');
        }
        return $this->m_objects['article'];
    }


    /**
     * Handler for the language change event.
     *
     * @param MetaLanguage $p_oldLanguage
     * @param MetaLanguage $p_newLanguage
     */
    private function setLanguageHandler(MetaLanguage $p_oldLanguage, MetaLanguage $p_newLanguage)
    {
        static $languageHandlerRunning = false;
        if ($languageHandlerRunning || $p_newLanguage->same_as($p_oldLanguage)) {
            return;
        }
        $languageHandlerRunning = true;

        $this->m_readonlyProperties['url']->language = $p_newLanguage;
        $this->m_objects['language'] = $p_newLanguage;

        if ($this->article->defined()) {
        	$oldArticle = $this->m_objects['article'];
        	$newArticle = new MetaArticle($p_newLanguage->number,
        	                              $oldArticle->number);
            $this->setArticleHandler($oldArticle, $newArticle);
        }
        if ($this->section->defined() && !$this->m_objects['article']->defined()) {
        	$oldSection = $this->m_objects['section'];
        	$newSection = new MetaSection($oldSection->publication->identifier,
        	                              $oldSection->issue->number,
        	                              $p_newLanguage->number,
        	                              $oldSection->number);
            $this->setSectionHandler($oldSection, $newSection);
        }
        if ($this->issue->defined() && !$this->m_objects['section']->defined()) {
        	$oldIssue = $this->m_objects['issue'];
        	$newIssue = new MetaIssue($oldIssue->publication->identifier,
        	                          $p_newLanguage->number,
        	                          $oldIssue->number);
            $this->setIssueHandler($oldIssue, $newIssue);
        }

        $languageHandlerRunning = false;
    }


    /**
     * Handler for the publication change event.
     *
     * @param MetaPublication $p_oldPublication
     * @param MetaPublication $p_newPublication
     */
    private function setPublicationHandler(MetaPublication $p_oldPublication,
    MetaPublication $p_newPublication)
    {
        static $publicationHandlerRunning = false;
        if ($publicationHandlerRunning || $p_newPublication->same_as($p_oldPublication)) {
            return;
        }
        $publicationHandlerRunning = true;

        if ($p_newPublication->defined() && !$this->getLanguage()->defined()) {
            $this->setLanguageHandler($this->getLanguage(), $p_newPublication->default_language);
        }
        $this->setIssueHandler($this->getIssue(), new MetaIssue());
        $this->m_readonlyProperties['url']->publication = $p_newPublication;
        $this->m_objects['publication'] = $p_newPublication;

        $publicationHandlerRunning = false;
    }


    /**
     * Handler for the issue change event.
     *
     * @param MetaIssue $p_oldIssue
     * @param MetaIssue $p_newIssue
     */
    private function setIssueHandler(MetaIssue $p_oldIssue, MetaIssue $p_newIssue)
    {
        static $issueHandlerRunning = false;

        if (!$this->m_readonlyProperties['preview']
        && !$p_newIssue->is_published && $p_newIssue->defined()) {
            return;
        }

        if ($issueHandlerRunning || $p_newIssue->same_as($p_oldIssue)) {
            return;
        }
        $issueHandlerRunning = true;

        if ($p_newIssue->defined() && !$this->getPublication()->same_as($p_newIssue->publication)) {
            $this->setPublicationHandler($this->getPublication(), $p_newIssue->publication);
        }
        if ($p_newIssue->defined() && !$this->getLanguage()->same_as($p_newIssue->language)) {
            $this->setLanguageHandler($this->getLanguage(), $p_newIssue->language);
        }
        $this->setSectionHandler($this->getSection(), self::$m_nullMetaSection);
        $this->m_readonlyProperties['url']->issue = $p_newIssue;
        $this->m_objects['issue'] = $p_newIssue;

        $issueHandlerRunning = false;
    }


    /**
     * Handler for the section change event.
     *
     * @param MetaSection $p_oldSection
     * @param MetaSection $p_newSection
     */
    private function setSectionHandler(MetaSection $p_oldSection, MetaSection $p_newSection)
    {
        static $sectionHandlerRunning = false;

        if (!$this->m_readonlyProperties['preview'] && isset($p_newSection->issue)
        && !$p_newSection->issue->is_published && $p_newSection->defined()) {
            return;
        }

        if ($sectionHandlerRunning || $p_newSection->same_as($p_oldSection)) {
            return;
        }
        $sectionHandlerRunning = true;

        if ($p_newSection->defined() && !$this->getIssue()->same_as($p_newSection->issue)) {
            $this->setIssueHandler($this->getIssue(), $p_newSection->issue);
        }
        $this->setArticleHandler($this->getArticle(), self::$m_nullMetaArticle);
        $this->m_readonlyProperties['url']->section = $p_newSection;
        $this->m_objects['section'] = $p_newSection;

        $sectionHandlerRunning = false;
    }


    /**
     * Handler for the article change event.
     *
     * @param MetaArticle $p_oldArticle
     * @param MetaArticle $p_newArticle
     */
    private function setArticleHandler(MetaArticle $p_oldArticle, MetaArticle $p_newArticle)
    {
        static $articleHandlerRunning = false;

        if (!$this->m_readonlyProperties['preview']
        && (!$p_newArticle->is_published || !$p_newArticle->issue->is_published)
        && $p_newArticle->defined()) {
        	return;
        }

        if ($articleHandlerRunning || $p_newArticle->same_as($p_oldArticle)) {
            return;
        }
        $articleHandlerRunning = true;

        if ($p_newArticle->defined() && !$this->getSection()->same_as($p_newArticle->section)) {
            $this->setSectionHandler($this->getSection(), $p_newArticle->section);
        }
        unset($this->m_objects['subtitle']);
        unset($this->m_objects['image']);
        unset($this->m_objects['attachment']);
        unset($this->m_objects['comment']);
        unset($this->m_objects['location']);
        $this->m_readonlyProperties['url']->article = $p_newArticle;
        $formParameters = $this->m_readonlyProperties['url']->form_parameters;
        foreach ($formParameters as $parameter) {
            if (strncmp($parameter['name'], 'st-', strlen('st-')) == 0) {
                $this->m_readonlyProperties['url']->reset_parameter($parameter['name']);
            }
        }
        $this->m_objects['article'] = $p_newArticle;

        $articleHandlerRunning = false;
    }


    /**
     * Handler for the comment change event.
     *
     * @param MetaComment $p_oldComment
     * @param MetaComment $p_newComment
     */
    private function setCommentHandler(MetaComment $p_oldComment, MetaComment $p_newComment) {
        if (!$p_oldComment->same_as($p_newComment)) {
            if ($p_newComment->defined()) {
            	$this->setArticleHandler($this->article, $p_newComment->article);
            }
            $this->m_objects['comment'] = $p_newComment;
        }
    }


    private function setSubtitleHandler(MetaSubtitle $p_oldSubtitle, MetaSubtitle $p_newSubtitle) {
        if (!$p_oldSubtitle->same_as($p_newSubtitle)) {
            $this->m_objects['subtitle'] = $p_newSubtitle;
        }
    }


    private function setTopicHandler(MetaTopic $p_oldTopic, MetaTopic $p_newTopic) {
        if (!$p_oldTopic->same_as($p_newTopic)) {
            $this->m_readonlyProperties['url']->set_parameter('tpid', $p_newTopic->identifier);
            $this->m_objects['topic'] = $p_newTopic;
        }
    }


    /**
     * Returns the name corresponding to the given property; null if
     * the property is not an object.
     *
     * @param string $p_property
     */
    protected static function ObjectType($p_property)
    {
        // Verify if an object of this type exists
        if (array_key_exists($p_property, CampContext::$m_objectTypes)) {
            return 'Meta'.CampContext::$m_objectTypes[$p_property]['class'];
        }
        return null;
    }

    /**
     * Get active user count
     *
     * @return int
     */
    public function getUserCount()
    {
        if ($this->userCount === null) {
            $this->userCount = (int) Zend_Registry::get('container')->getService('user')
                ->getPublicUserCount();
        }

        return $this->userCount;
    }
}
