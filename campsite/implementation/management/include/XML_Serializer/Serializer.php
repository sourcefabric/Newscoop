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
//    $Id: Serializer.php,v 1.1 2005/05/27 08:09:14 paul Exp $

/**
 * uses PEAR error management
 */
require_once 'PEAR.php';

/**
 * uses XML_Util to create XML tags
 */
require_once 'XML/Util.php';

/**
 * error code for no serialization done
 */
define('XML_SERIALIZER_ERROR_NO_SERIALIZATION', 51);

/**
 * do not replace entitites
 */
define('XML_SERIALIZER_ENTITIES_NONE', XML_UTIL_ENTITIES_NONE);

/**
 * replace all XML entitites
 * This setting will replace <, >, ", ' and &
 */
define('XML_SERIALIZER_ENTITIES_XML', XML_UTIL_ENTITIES_XML);

/**
 * replace only required XML entitites
 * This setting will replace <, " and &
 */
define('XML_SERIALIZER_ENTITIES_XML_REQUIRED', XML_UTIL_ENTITIES_XML_REQUIRED);

/**
 * replace HTML entitites
 * @link    http://www.php.net/htmlentities
 */
define('XML_SERIALIZER_ENTITIES_HTML', XML_UTIL_ENTITIES_HTML);

/**
 * XML_Serializer
 * class that serializes various structures into an XML document
 *
 * this class can be used in two modes:
 *
 *  1. create an XML document from an array or object that is processed by other
 *    applications. That means, you can create a RDF document from an array in the
 *    following format:
 *
 *    $data = array(
 *              'channel' => array(
 *                            'title' => 'Example RDF channel',
 *                            'link'  => 'http://www.php-tools.de',
 *                            'image' => array(
 *                                        'title' => 'Example image',
 *                                        'url'   => 'http://www.php-tools.de/image.gif',
 *                                        'link'  => 'http://www.php-tools.de'
 *                                           ),
 *                            array(
 *                                 'title' => 'Example item',
 *                                 'link'  => 'http://example.com'
 *                                 ),
 *                            array(
 *                                 'title' => 'Another Example item',
 *                                 'link'  => 'http://example.org'
 *                                 )
 *                              )
 *             );
 *
 *   to create a RDF document from this array do the following:
 *
 *   require_once 'XML/Serializer.php';
 *
 *   $options = array(
 *                     'indent'         => "\t",        // indent with tabs
 *                     'linebreak'      => "\n",        // use UNIX line breaks
 *                     'rootName'       => 'rdf:RDF',   // root tag
 *                     'defaultTagName' => 'item'       // tag for values with numeric keys
 *   );
 *
 *   $serializer = new XML_Serializer($options);
 *   $rdf        = $serializer->serialize($data);
 *
 * You will get a complete XML document that can be processed like any RDF document.
 *
 *
 * 2. this classes can be used to serialize any data structure in a way that it can
 *    later be unserialized again.
 *    XML_Serializer will store the type of the value and additional meta information
 *    in attributes of the surrounding tag. This meat information can later be used
 *    to restore the original data structure in PHP. If you want XML_Serializer
 *    to add meta information to the tags, add
 *
 *      'typeHints' => true
 *
 *    to the options array in the constructor.
 *
 *    Future versions of this package will include an XML_Unserializer, that does
 *    the unserialization automatically for you.
 *
 * @category XML
 * @package  XML_Serializer
 * @version  0.14.1
 * @author   Stephan Schmidt <schst@php.net>
 * @uses     XML_Util
 */
class XML_Serializer extends PEAR
{
   /**
    * default options for the serialization
    * @access private
    * @var array $_defaultOptions
    */
    var $_defaultOptions = array(
                         'indent'             => '',                    // string used for indentation
                         'linebreak'          => "\n",                  // string used for newlines
                         'typeHints'          => false,                 // automatically add type hin attributes
                         'addDecl'            => false,                 // add an XML declaration
                         'encoding'           => null,                  // encoding specified in the XML declaration
                         'defaultTagName'     => 'XML_Serializer_Tag',  // tag used for indexed arrays or invalid names
                         'classAsTagName'     => false,                 // use classname for objects in indexed arrays
                         'keyAttribute'       => '_originalKey',        // attribute where original key is stored
                         'typeAttribute'      => '_type',               // attribute for type (only if typeHints => true)
                         'classAttribute'     => '_class',              // attribute for class of objects (only if typeHints => true)
                         'scalarAsAttributes' => false,                 // scalar values (strings, ints,..) will be serialized as attribute
                         'prependAttributes'  => '',                    // prepend string for attributes
                         'indentAttributes'   => false,                 // indent the attributes, if set to '_auto', it will indent attributes so they all start at the same column
                         'mode'               => 'default',             // use 'simplexml' to use parent name as tagname if transforming an indexed array
                         'addDoctype'         => false,                 // add a doctype declaration
                         'doctype'            => null,                  // supply a string or an array with id and uri ({@see XML_Util::getDoctypeDeclaration()}
                         'rootName'           => null,                  // name of the root tag
                         'rootAttributes'     => array(),               // attributes of the root tag
                         'attributesArray'    => null,                  // all values in this key will be treated as attributes
                         'contentName'        => null,                  // this value will be used directly as content, instead of creating a new tag, may only be used in conjuction with attributesArray
                         'tagMap'             => array(),               // tag names that will be changed
                         'encodeFunction'     => null,                  // function that will be applied before serializing
                         'namespace'          => null,                  // namespace to use
                         'replaceEntities'    => XML_UTIL_ENTITIES_XML, // type of entities to replace,
                         'returnResult'       => false                  // serialize() returns the result of the serialization instead of true
                        );

   /**
    * options for the serialization
    * @access private
    * @var array $options
    */
    var $options = array();

   /**
    * current tag depth
    * @var integer $_tagDepth
    */
    var $_tagDepth = 0;

   /**
    * serilialized representation of the data
    * @var string $_serializedData
    */
    var $_serializedData = null;
    
   /**
    * constructor
    *
    * @access   public
    * @param    mixed   $options    array containing options for the serialization
    */
    function XML_Serializer( $options = null )
    {
        $this->PEAR();
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
    * @see      setOption(), XML_Unserializer()
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
    * @see      resetOption(), XML_Serializer()
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
    * serialize data
    *
    * @access   public
    * @param    mixed    $data data to serialize
    * @return   boolean  true on success, pear error on failure
    */
    function serialize($data, $options = null)
    {
        // if options have been specified, use them instead
        // of the previously defined ones
        if (is_array($options)) {
            $optionsBak = $this->options;
            if (isset($options['overrideOptions']) && $options['overrideOptions'] == true) {
                $this->options = array_merge($this->_defaultOptions, $options);
            } else {
                $this->options = array_merge($this->options, $options);
            }
        } else {
            $optionsBak = null;
        }
        
        // maintain BC
        if (isset($this->options['tagName'])) {
            $this->options['rootName'] = $this->options['tagName'];
        }
        
        //  start depth is zero
        $this->_tagDepth = 0;

        $rootAttributes = $this->options['rootAttributes'];
        if (is_array($this->options['namespace'])) {
        	$rootAttributes['xmlns:'.$this->options['namespace'][0]] = $this->options['namespace'][1];
        }
        
        $this->_serializedData = '';
        // serialize an array
        if (is_array($data)) {
            if (isset($this->options['rootName'])) {
                $tagName = $this->options['rootName'];
            } else {
                $tagName = 'array';
            }

            $this->_serializedData .= $this->_serializeArray($data, $tagName, $rootAttributes);
        }
        // serialize an object
        elseif (is_object($data)) {
            if (isset($this->options['rootName'])) {
                $tagName = $this->options['rootName'];
            } else {
                $tagName = get_class($data);
            }
            $this->_serializedData .= $this->_serializeObject($data, $tagName, $rootAttributes);
        }
        
        // add doctype declaration
        if ($this->options['addDoctype'] === true) {
            $this->_serializedData = XML_Util::getDoctypeDeclaration($tagName, $this->options['doctype'])
                                   . $this->options['linebreak']
                                   . $this->_serializedData;
        }

        //  build xml declaration
        if ($this->options['addDecl']) {
            $atts = array();
            $this->_serializedData = XML_Util::getXMLDeclaration('1.0', $this->options['encoding'])
                                   . $this->options['linebreak']
                                   . $this->_serializedData;
        }

        if ($this->options['returnResult'] === true) {
        	$result = $this->_serializedData;
        } else {
            $result = true;
        }
        
        if ($optionsBak !== null) {
            $this->options = $optionsBak;
        }

        return $result;
    }

   /**
    * get the result of the serialization
    *
    * @access public
    * @return string serialized XML
    */
    function getSerializedData()
    {
        if ($this->_serializedData == null ) {
            return  $this->raiseError('No serialized data available. Use XML_Serializer::serialize() first.', XML_SERIALIZER_ERROR_NO_SERIALIZATION);
        }
        return $this->_serializedData;
    }
    
   /**
    * serialize any value
    *
    * This method checks for the type of the value and calls the appropriate method
    *
    * @access private
    * @param  mixed     $value
    * @param  string    $tagName
    * @param  array     $attributes
    * @return string
    */
    function _serializeValue($value, $tagName = null, $attributes = array())
    {
        if (is_array($value)) {
            $xml = $this->_serializeArray($value, $tagName, $attributes);
        } elseif (is_object($value)) {
            $xml = $this->_serializeObject($value, $tagName);
        } else {
            $tag = array(
                          'qname'      => $tagName,
                          'attributes' => $attributes,
                          'content'    => $value
                        );
            $xml = $this->_createXMLTag($tag);
        }
        return $xml;
    }
    
   /**
    * serialize an array
    *
    * @access   private
    * @param    array   $array       array to serialize
    * @param    string  $tagName     name of the root tag
    * @param    array   $attributes  attributes for the root tag
    * @return   string  $string      serialized data
    * @uses     XML_Util::isValidName() to check, whether key has to be substituted
    */
    function _serializeArray(&$array, $tagName = null, $attributes = array())
    {
        $_content = null;
        
        /**
         * check for special attributes
         */
        if ($this->options['attributesArray'] !== null) {
            if (isset($array[$this->options['attributesArray']])) {
                $attributes = $array[$this->options['attributesArray']];
                unset($array[$this->options['attributesArray']]);
            }
            /**
             * check for special content
             */
            if ($this->options['contentName'] !== null) {
                if (isset($array[$this->options['contentName']])) {
                    $_content = $array[$this->options['contentName']];
                    unset($array[$this->options['contentName']]);
                }
            }
        }

        /*
        * if mode is set to simpleXML, check whether
        * the array is associative or indexed
        */
        if (is_array($array) && !empty($array) && $this->options['mode'] == 'simplexml') {
            $indexed = true;
            foreach ($array as $key => $val) {
                if (!is_int($key)) {
                    $indexed = false;
                    break;
                }
            }

            if ($indexed && $this->options['mode'] == 'simplexml') {
                $string = '';
                foreach ($array as $key => $val) {
                    $string .= $this->_serializeValue( $val, $tagName, $attributes);
                    
                    $string .= $this->options['linebreak'];
                    //    do indentation
                    if ($this->options['indent']!==null && $this->_tagDepth>0) {
                        $string .= str_repeat($this->options['indent'], $this->_tagDepth);
                    }
                }
                return rtrim($string);
            }
        }
        
        if ($this->options['scalarAsAttributes'] === true) {
            $this->expectError('*');
            foreach ($array as $key => $value) {
                if (is_scalar($value) && (XML_Util::isValidName($key) === true)) {
                    unset($array[$key]);
                    $attributes[$this->options['prependAttributes'].$key] = $value;
                }
            }
            $this->popExpect();
        }

        // check for empty array => create empty tag
        if (empty($array)) {
            $tag = array(
                            'qname'      => $tagName,
                            'content'    => $_content,
                            'attributes' => $attributes
                        );

        } else {
            $this->_tagDepth++;
            $tmp = $this->options['linebreak'];
            foreach ($array as $key => $value) {
                //    do indentation
                if ($this->options['indent']!==null && $this->_tagDepth>0) {
                    $tmp .= str_repeat($this->options['indent'], $this->_tagDepth);
                }

                if (isset($this->options['tagMap'][$key])) {
                    $key = $this->options['tagMap'][$key];
                }

                // copy key
                $origKey    =    $key;
                $this->expectError('*');
                // key cannot be used as tagname => use default tag
                $valid = XML_Util::isValidName($key);
                $this->popExpect();
                if (PEAR::isError($valid)) {
                    if ($this->options['classAsTagName'] && is_object($value)) {
                        $key = get_class($value);
                    } else {
                        $key = $this->options['defaultTagName'];
                    }
                    }
                $atts = array();
                if ($this->options['typeHints'] === true) {
                    $atts[$this->options['typeAttribute']] = gettype($value);
                    if ($key !== $origKey) {
                        $atts[$this->options['keyAttribute']] = (string)$origKey;
                    }
    
                }
                
                $tmp .= $this->_createXMLTag(array(
                                                    'qname'      => $key,
                                                    'attributes' => $atts,
                                                    'content'    => $value )
                                            );
                $tmp .= $this->options['linebreak'];
            }
            
            $this->_tagDepth--;
            if ($this->options['indent']!==null && $this->_tagDepth>0) {
                $tmp .= str_repeat($this->options['indent'], $this->_tagDepth);
            }
    
            if (trim($tmp) === '') {
                $tmp = null;
            }
            
            $tag = array(
                            'qname'      => $tagName,
                            'content'    => $tmp,
                            'attributes' => $attributes
                        );
        }
        if ($this->options['typeHints'] === true) {
            if (!isset($tag['attributes'][$this->options['typeAttribute']])) {
                $tag['attributes'][$this->options['typeAttribute']] = 'array';
            }
        }

        $string = $this->_createXMLTag($tag, false);
        return $string;
    }

   /**
    * serialize an object
    *
    * @access   private
    * @param    object  $object object to serialize
    * @return   string  $string serialized data
    */
    function _serializeObject(&$object, $tagName = null, $attributes = array())
    {
        //  check for magic function
        if (method_exists($object, '__sleep')) {
            $object->__sleep();
        }

        $tmp = $this->options['linebreak'];
        $properties = get_object_vars($object);
        if (empty($tagName)) {
            $tagName = get_class($object);
        }
        
        // typehints activated?
        if ($this->options['typeHints'] === true) {
            $attributes[$this->options['typeAttribute']]  = 'object';
            $attributes[$this->options['classAttribute']] =  get_class($object);
        }
        
        $string = $this->_serializeArray($properties, $tagName, $attributes);
        return $string;
    }
  
   /**
    * create a tag from an array
    * this method awaits an array in the following format
    * array(
    *       'qname'        => $tagName,
    *       'attributes'   => array(),
    *       'content'      => $content,      // optional
    *       'namespace'    => $namespace     // optional
    *       'namespaceUri' => $namespaceUri  // optional
    *   )
    *
    * @access   private
    * @param    array   $tag tag definition
    * @param    boolean $replaceEntities whether to replace XML entities in content or not
    * @return   string  $string XML tag
    */
    function _createXMLTag( $tag, $replaceEntities = true )
    {
        if ($this->options['namespace'] !== null) {
        	if (is_array($this->options['namespace'])) {
        		$tag['qname'] = $this->options['namespace'][0] . ':' . $tag['qname'];
        	} else {
        		$tag['qname'] = $this->options['namespace'] . ':' . $tag['qname'];
        	}
        }

        if ($this->options['indentAttributes'] !== false) {
            $multiline = true;
            $indent    = str_repeat($this->options['indent'], $this->_tagDepth);

            if ($this->options['indentAttributes'] == '_auto') {
                $indent .= str_repeat(' ', (strlen($tag['qname'])+2));

            } else {
                $indent .= $this->options['indentAttributes'];
            }
        } else {
            $multiline = false;
            $indent    = false;
        }

        if ($replaceEntities) {
           	$replaceEntities = $this->options['replaceEntities'];
        }
    
        if (is_array($tag['content'])) {
            if (empty($tag['content'])) {
                $tag['content'] =   '';
            }
        } elseif(is_scalar($tag['content']) && (string)$tag['content'] == '') {
            $tag['content'] =   '';
        }
    
        if (is_scalar($tag['content']) || is_null($tag['content'])) {
            if ($this->options['encodeFunction']) {
                if ($replaceEntities === true) {
                	$tag['content'] = call_user_func($this->options['encodeFunction'], $tag['content']);
                }
            	$tag['attributes'] = array_map($this->options['encodeFunction'], $tag['attributes']);
            }
            $tag = XML_Util::createTagFromArray($tag, $replaceEntities, $multiline, $indent, $this->options['linebreak']);
        } elseif (is_array($tag['content'])) {
            $tag    =   $this->_serializeArray($tag['content'], $tag['qname'], $tag['attributes']);
        } elseif (is_object($tag['content'])) {
            $tag    =   $this->_serializeObject($tag['content'], $tag['qname'], $tag['attributes']);
        } elseif (is_resource($tag['content'])) {
            settype($tag['content'], 'string');
            if ($this->options['encodeFunction']) {
            	if ($replaceEntities === true) {
                    $tag['content'] = call_user_func($this->options['encodeFunction'], $tag['content']);
            	}
            	$tag['attributes'] = array_map($this->options['encodeFunction'], $tag['attributes']);
            }
            $tag = XML_Util::createTagFromArray($tag, $replaceEntities);
        }
        return  $tag;
    }
}
?>