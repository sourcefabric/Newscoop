<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Stephan Schmidt <schst@php-tools.net>                       |
// +----------------------------------------------------------------------+
//
//    $Id: Unserializer.php,v 1.1 2005/05/27 08:09:14 paul Exp $

/**
 * uses PEAR error managemt
 */
require_once 'PEAR.php';

/**
 * uses XML_Parser to unserialize document
 */
require_once 'XML/Parser.php';

/**
 * error code for no serialization done
 */
define('XML_UNSERIALIZER_ERROR_NO_UNSERIALIZATION', 151);

/**
 * XML_Unserializer
 *
 * class to unserialize XML documents that have been created with
 * XML_Serializer. To unserialize an XML document you have to add
 * type hints to the XML_Serializer options.
 *
 * If no type hints are available, XML_Unserializer will guess how
 * the tags should be treated, that means complex structures will be
 * arrays and tags with only CData in them will be strings.
 *
 * <code>
 * require_once 'XML/Unserializer.php';
 *
 * //  be careful to always use the ampersand in front of the new operator
 * $unserializer = &new XML_Unserializer();
 *
 * $unserializer->unserialize($xml);
 *
 * $data = $unserializer->getUnserializedData();
 * <code>
 *
 * Possible options for the Unserializer are:
 *
 * 1. complexTypes => array|object
 * This is needed, when unserializing XML files w/o type hints. If set to
 * 'array' (default), all nested tags will be arrays, if set to 'object'
 * all nested tags will be objects, that means you have read access like:
 *
 * <code>
 * require_once 'XML/Unserializer.php';
 * $options = array('complexType' => 'object');
 * $unserializer = &new XML_Unserializer($options);
 *
 * $unserializer->unserialize('http://pear.php.net/rss.php');
 *
 * $rss = $unserializer->getUnserializedData();
 * echo $rss->channel->item[3]->title;
 * </code>
 *
 * 2. keyAttribute
 * This lets you specify an attribute inside your tags, that are used as key
 * for associative arrays or object properties.
 * You will need this if you have XML that looks like this:
 *
 * <users>
 *   <user handle="schst">Stephan Schmidt</user>
 *   <user handle="ssb">Stig S. Bakken</user>
 * </users>
 *
 * Then you can use:
 * <code>
 * require_once 'XML/Unserializer.php';
 * $options = array('keyAttribute' => 'handle');
 * $unserializer = &new XML_Unserializer($options);
 *
 * $unserializer->unserialize($xml, false);
 *
 * $users = $unserializer->getUnserializedData();
 * </code>
 *
 * @category XML
 * @package  XML_Serializer
 * @version  0.14.1
 * @author   Stephan Schmidt <schst@php-tools.net>
 * @uses     XML_Parser
 */
class XML_Unserializer extends PEAR
{

   /**
    * default options for the serialization
    * @access private
    * @var array $_defaultOptions
    */
    var $_defaultOptions = array(
                         'complexType'       => 'array',                // complex types will be converted to arrays, if no type hint is given
                         'keyAttribute'      => '_originalKey',         // get array key/property name from this attribute
                         'typeAttribute'     => '_type',                // get type from this attribute
                         'classAttribute'    => '_class',               // get class from this attribute (if not given, use tag name)
                         'tagAsClass'        => true,                   // use the tagname as the classname
                         'defaultClass'      => 'stdClass',             // name of the class that is used to create objects
                         'parseAttributes'   => false,                  // parse the attributes of the tag into an array
                         'attributesArray'   => false,                  // parse them into sperate array (specify name of array here)
                         'prependAttributes' => '',                     // prepend attribute names with this string
                         'contentName'       => '_content',             // put cdata found in a tag that has been converted to a complex type in this key
                         'tagMap'            => array(),                // use this to map tagnames
                         'forceEnum'         => array(),                // these tags will always be an indexed array
                         'encoding'          => null,                   // specify the encoding character of the document to parse
                         'targetEncoding'    => null,                   // specify the target encoding
                         'decodeFunction'    => null,                   // function used to decode data
                         'returnResult'      => false                   // unserialize() returns the result of the unserialization instead of true
                        );

   /**
    * actual options for the serialization
    * @access private
    * @var array $options
    */
    var $options = array();

   /**
    * unserialized data
    * @var string $_unserializedData
    */
    var $_unserializedData = null;

   /**
    * name of the root tag
    * @var string $_root
    */
    var $_root = null;

   /**
    * stack for all data that is found
    * @var array    $_dataStack
    */
    var $_dataStack  =   array();

   /**
    * stack for all values that are generated
    * @var array    $_valStack
    */
    var $_valStack  =   array();

   /**
    * current tag depth
    * @var int    $_depth
    */
    var $_depth = 0;

   /**
    * XML_Parser instance
    *
    * @access   private
    * @var      object XML_Parser
    */
    var $_parser = null;
    
   /**
    * constructor
    *
    * @access   public
    * @param    mixed   $options    array containing options for the serialization
    */
    function XML_Unserializer($options = null)
    {
        if (is_array($options)) {
            $this->options = array_merge($this->_defaultOptions, $options);
        } else {
            $this->options = $this->_defaultOptions;
        }
    }

   /**
    * return API version
    *
    * @access   public
    * @static
    * @return   string  $version API version
    */
    function apiVersion()
    {
        return '0.14';
    }

   /**
    * reset all options to default options
    *
    * @access   public
    * @see      setOption(), XML_Unserializer(), setOptions()
    */
    function resetOptions()
    {
        $this->options = $this->_defaultOptions;
    }

   /**
    * set an option
    *
    * You can use this method if you do not want to set all options in the constructor
    *
    * @access   public
    * @see      resetOption(), XML_Unserializer(), setOptions()
    */
    function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

   /**
    * sets several options at once
    *
    * You can use this method if you do not want to set all options in the constructor
    *
    * @access   public
    * @see      resetOption(), XML_Unserializer(), setOption()
    */
    function setOptions($options)
    {
        $this->options = array_merge($this->options, $options);
    }

   /**
    * unserialize data
    *
    * @access   public
    * @param    mixed    $data   data to unserialize (string, filename or resource)
    * @param    boolean  $isFile string should be treated as a file
    * @param    array    $options
    * @return   boolean  $success
    */
    function unserialize($data, $isFile = false, $options = null)
    {
        $this->_unserializedData = null;
        $this->_root = null;

        // if options have been specified, use them instead
        // of the previously defined ones
        if (is_array($options)) {
            $optionsBak = $this->options;
            if (isset($options['overrideOptions']) && $options['overrideOptions'] == true) {
                $this->options = array_merge($this->_defaultOptions, $options);
            } else {
                $this->options = array_merge($this->options, $options);
            }
        }
        else {
            $optionsBak = null;
        }

        $this->_valStack = array();
        $this->_dataStack = array();
        $this->_depth = 0;

        $this->_createParser();
        
        if (is_string($data)) {
            if ($isFile) {
                $result = $this->_parser->setInputFile($data);
                if (PEAR::isError($result)) {
                    return $result;
                }
                $result = $this->_parser->parse();
            } else {
                $result = $this->_parser->parseString($data,true);
            }
        } else {
           $this->_parser->setInput($data);
           $result = $this->_parser->parse();
        }

        if ($this->options['returnResult'] === true) {
        	$return = $this->_unserializedData;
        } else {
            $return = true;
        }

        if ($optionsBak !== null) {
            $this->options = $optionsBak;
        }

        if (PEAR::isError($result)) {
            return $result;
        }

        return $return;
    }

   /**
    * get the result of the serialization
    *
    * @access public
    * @return string  $serializedData
    */
    function getUnserializedData()
    {
        if ($this->_root === null ) {
            return  $this->raiseError('No unserialized data available. Use XML_Unserializer::unserialize() first.', XML_UNSERIALIZER_ERROR_NO_UNSERIALIZATION);
        }
        return $this->_unserializedData;
    }

   /**
    * get the name of the root tag
    *
    * @access public
    * @return string  $rootName
    */
    function getRootName()
    {
        if ($this->_root === null ) {
            return  $this->raiseError('No unserialized data available. Use XML_Unserializer::unserialize() first.', XML_UNSERIALIZER_ERROR_NO_UNSERIALIZATION);
        }
        return $this->_root;
    }

   /**
    * Start element handler for XML parser
    *
    * @access private
    * @param  object $parser  XML parser object
    * @param  string $element XML element
    * @param  array  $attribs attributes of XML tag
    * @return void
    */
    function startHandler($parser, $element, $attribs)
    {
        if (isset($attribs[$this->options['typeAttribute']])) {
            $type = $attribs[$this->options['typeAttribute']];
        } else {
            $type = 'string';
        }

        if ($this->options['decodeFunction'] !== null) {
            $element = call_user_func($this->options['decodeFunction'], $element);
            $attribs = array_map($this->options['decodeFunction'], $attribs);
        }
        
        $this->_depth++;
        $this->_dataStack[$this->_depth] = null;

        $val = array(
                     'name'         => $element,
                     'value'        => null,
                     'type'         => $type,
                     'childrenKeys' => array(),
                     'aggregKeys'   => array()
                    );

        if ($this->options['parseAttributes'] == true && (count($attribs) > 0)) {
            $val['children'] = array();
            $val['type']  = $this->options['complexType'];
            $val['class'] = $element;

            if ($this->options['attributesArray'] != false) {
                $val['children'][$this->options['attributesArray']] = $attribs;
            } else {
                foreach ($attribs as $attrib => $value) {
                    $val['children'][$this->options['prependAttributes'].$attrib] = $value;
                }
            }
        }

        $keyAttr = false;
        
        if (is_string($this->options['keyAttribute'])) {
            $keyAttr = $this->options['keyAttribute'];
        } elseif (is_array($this->options['keyAttribute'])) {
            if (isset($this->options['keyAttribute'][$element])) {
                $keyAttr = $this->options['keyAttribute'][$element];
            } elseif (isset($this->options['keyAttribute']['__default'])) {
                $keyAttr = $this->options['keyAttribute']['__default'];
            }
        }
        
        if ($keyAttr !== false && isset($attribs[$keyAttr])) {
            $val['name'] = $attribs[$keyAttr];
        }

        if (isset($attribs[$this->options['classAttribute']])) {
            $val['class'] = $attribs[$this->options['classAttribute']];
        }

        array_push($this->_valStack, $val);
    }

   /**
    * End element handler for XML parser
    *
    * @access private
    * @param  object XML parser object
    * @param  string
    * @return void
    */
    function endHandler($parser, $element)
    {
        $value = array_pop($this->_valStack);
        //$data  = trim($this->_dataStack[$this->_depth]);
        $data  = ($this->_dataStack[$this->_depth]);

        // adjust type of the value
        switch(strtolower($value['type'])) {
            /*
             * unserialize an object
             */
            case 'object':
                if(isset($value['class'])) {
                    $classname  = $value['class'];
                } else {
                    $classname = '';
                }
                if (is_array($this->options['tagMap']) && isset($this->options['tagMap'][$classname])) {
                    $classname = $this->options['tagMap'][$classname];
                }

                // instantiate the class
                if ($this->options['tagAsClass'] === true && class_exists($classname)) {
                    $value['value'] = &new $classname;
                } else {
                    $value['value'] = &new $this->options['defaultClass'];
                }
                if ($data !== '') {
                    $value['children'][$this->options['contentName']] = $data;
                }

                // set properties
                foreach($value['children'] as $prop => $propVal) {
                    // check whether there is a special method to set this property
                    $setMethod = 'set'.$prop;
                    if (method_exists($value['value'], $setMethod)) {
                        call_user_func(array(&$value['value'], $setMethod), $propVal);
                    } else {
                        $value['value']->$prop = $propVal;
                    }
                }
                //  check for magic function
                if (method_exists($value['value'], '__wakeup')) {
                    $value['value']->__wakeup();
                }
                break;

            /*
             * unserialize an array
             */
            case 'array':
                if ($data !== '') {
                    $value['children'][$this->options['contentName']] = $data;
                }
                if (isset($value['children'])) {
                    $value['value'] = $value['children'];
                } else {
                    $value['value'] = array();
                }
                break;

            /*
             * unserialize a null value
             */
            case 'null':
                $data = null;
                break;

            /*
             * unserialize a resource => this is not possible :-(
             */
            case 'resource':
                $value['value'] = $data;
                break;

            /*
             * unserialize any scalar value
             */
            default:
                settype($data, $value['type']);
                $value['value'] = $data;
                break;
        }
        $parent = array_pop($this->_valStack);
        if ($parent === null) {
            $this->_unserializedData = &$value['value'];
            $this->_root = &$value['name'];
            return true;
        } else {
            // parent has to be an array
            if (!isset($parent['children']) || !is_array($parent['children'])) {
                $parent['children'] = array();
                if (!in_array($parent['type'], array('array', 'object'))) {
                    $parent['type'] = $this->options['complexType'];
                    if ($this->options['complexType'] == 'object') {
                        $parent['class'] = $parent['name'];
                    }
                }
            }

            if (!empty($value['name'])) {
                // there already has been a tag with this name
                if (in_array($value['name'], $parent['childrenKeys']) || in_array($value['name'], $this->options['forceEnum'])) {
                    // no aggregate has been created for this tag
                    if (!in_array($value['name'], $parent['aggregKeys'])) {
                        if (isset($parent['children'][$value['name']])) {
                            $parent['children'][$value['name']] = array($parent['children'][$value['name']]);
                        } else {
                            $parent['children'][$value['name']] = array();
                        }
                        array_push($parent['aggregKeys'], $value['name']);
                    }
                    array_push($parent['children'][$value['name']], $value['value']);
                } else {
                    $parent['children'][$value['name']] = &$value['value'];
                    array_push($parent['childrenKeys'], $value['name']);
                }
            } else {
                array_push($parent['children'],$value['value']);
            }
            array_push($this->_valStack, $parent);
        }

        $this->_depth--;
    }

   /**
    * Handler for character data
    *
    * @access private
    * @param  object XML parser object
    * @param  string CDATA
    * @return void
    */
    function cdataHandler($parser, $cdata)
    {
        $this->_dataStack[$this->_depth] .= $cdata;
    }

   /**
    * create the XML_Parser instance
    *
    * @access    private
    */
    function _createParser()
    {
        if (is_object($this->_parser)) {
            $this->_parser->free();
            unset($this->_parser);
        }
        $this->_parser = &new XML_Parser($this->options['encoding'], 'event', $this->options['targetEncoding']);
        $this->_parser->folding = false;
        $this->_parser->setHandlerObj($this);
        return true;
    }
}
?>