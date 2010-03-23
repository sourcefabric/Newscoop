<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_FileBase.php');


/**
 * @package Campsite
 */
class Archive_TextFile extends Archive_FileBase
{
    protected $m_fileType = 'text';

    protected $m_metatagLabels = array(
        // generic tags for all file types
        'dc:title' => 'Title',
        'dc:format' => 'File type',
        'dc:description' => 'Description',
        'dc:rights' => 'Copyright',
        'ls:crc' => 'Checksum',
        'ls:filename' => 'File name',
        'ls:filesize' => 'File size',
        'ls:filetype' => 'File type',
        'ls:mtime' => 'Modified time',
        'ls:url' => 'URL',
    );

    protected $m_mask = array(
        'pages' => array(
            'Main'  => array(
                array(
                    'element' => 'dc:title',
                    'type' => 'text',
                    'required' => TRUE,
                ),
                array(
                    'element' => 'dc:format',
                    'type' => 'text',
                    'required' => TRUE,
                    'attributes'=> array('disabled' => 'on'),
                ),
                array(
                    'element' => 'ls:filesize',
                    'type' => 'text',
                    'attributes' => array('disabled' => 'on'),
                ),
                array(
                    'element' => 'ls:mtime',
                    'type' => 'text',
                    'attributes' => array('disabled' => 'on'),
                ),
            ),
            'Text'  => array(
                array(
                    'element' => 'dc:title',
                    'type' => 'text',
                ),
                array(
                    'element' => 'dc:description',
                    'type' => 'textarea',
                ),
                array(
                    'element' => 'dc:format',
                    'type' => 'text',
                    'attributes'=> array('disabled' => 'on'),
                ),
                array(
                    'element' => 'ls:crc',
                    'type' => 'text',
                    'rule' => 'numeric',
                ),
                array(
                    'element' => 'dc:rights',
                    'type' => 'text',
                ),
                array(
                    'element' => 'ls:url',
                    'type' => 'text',
                ),
            )
        )
    );

    protected $m_fileTypes = array(
        '.css'  => array('name' => 'CSS text file',
                         'icon' => 'filearchive_text_48x48.png'),
        '.htm'  => array('name' => 'HTML',
                         'icon' => 'filearchive_text_48x48.png'),
        '.html' => array('name' => 'HTML',
                         'icon' => 'filearchive_text_48x48.png'),
        '.asc'  => array('name' => 'Plain Text',
                         'icon' => 'filearchive_text_48x48.png'),
        '.txt'  => array('name' => 'Plain Text',
                         'icon' => 'filearchive_text_48x48.png'),
        '.sgm'  => array('name' => 'SGML',
                         'icon' => 'filearchive_text_48x48.png'),
        '.sgml' => array('name' => 'SGML',
                         'icon' => 'filearchive_text_48x48.png'),
        '.xml'  => array('name' => 'XML document',
                         'icon' => 'filearchive_text_48x48.png'),
        '.*'    => array('name' => 'Unknown',
                         'icon' => 'filearchive_unknown_48x48.png'),
    );


    /**
     * Constructor
     *
     * @param string $p_gunId
     *      The audio file gunid
     */
    public function __construct($p_gunId = null)
    {
        parent::__construct($p_gunId);
    } // constructor


    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->m_fileType;
    }


    /**
     * @return array
     */
    public function getMetatagLabels()
    {
        return $this->m_metatagLabels;
    }


    /**
     * @return array
     */
    public function getMask()
    {
        return $this->m_mask;
    }


    /**
     * Retrieve a list of Audioclip objects based on the given constraints
     *
     * @param array $conditions
     *      array of struct with fields:
     *          cat: string - metadata category name
     *          op: string - operator, meaningful values:
     *              'full', 'partial', 'prefix',
     *              '=', '<', '<=', '>', '>='
     *          val: string - search value
     * @param string $operator
     *      type of conditions join (any condition matches /
     *      all conditions match), meaningful values: 'and', 'or', ''
     *      (may be empty or ommited only with less then 2 items in
     *      "conditions" field)
     * @param int $limit
     *      limit for result arrays (0 means unlimited)
     * @param int $offset
     *      starting point (0 means without offset)
     * @param string $orderby
     *      string - metadata category for sorting (optional) or array
     *      of strings for multicolumn orderby
     *      [default: dc:creator, dc:source, dc:title]
     * @param bool $desc
     *      boolean - flag for descending order (optional) or array of
     *      boolean for multicolumn orderby (it corresponds to elements
     *      of orderby field)
     *      [default: all ascending]
     *
     * @return array
     *      Array of Audioclip objects
     */
    public static function SearchApplicationFiles($offset = 0, $limit = 0,
                                                  $conditions = array(),
                                                  $operator = 'and',
                                                  $orderby = 'dc:creator, dc:source, dc:title',
                                                  $desc = false)
    {
        $criteria = array(
            'filetype' => 'text',
            'operator' => $operator,
            'limit' => $limit,
            'offset' => $offset,
            'orderby' => $orderby,
            'desc' => $desc,
            'conditions' => $conditions
        );
        return parent::SearchFiles($criteria);
    } // fn SearchApplicationFiles


    /**
     * Retrieve a list of values of the give category that meet the given constraints
     *
     * @param string $p_category
     *
     * @param array $conditions
     *      array of struct with fields:
     *          cat: string - metadata category name
     *          op: string - operator, meaningful values:
     *              'full', 'partial', 'prefix',
     *              '=', '<', '<=', '>', '>='
     *          val: string - search value
     * @param string $operator
     *      type of conditions join (any condition matches /
     *      all conditions match), meaningful values: 'and', 'or', ''
     *      (may be empty or ommited only with less then 2 items in
     *      "conditions" field)
     * @param int $limit
     *      limit for result arrays (0 means unlimited)
     * @param int $offset
     *      starting point (0 means without offset)
     * @param string $orderby
     *      string - metadata category for sorting (optional) or array
     *      of strings for multicolumn orderby
     *      [default: dc:creator, dc:source, dc:title]
     * @param bool $desc
     *      boolean - flag for descending order (optional) or array of
     *      boolean for multicolumn orderby (it corresponds to elements
     *      of orderby field)
     *      [default: all ascending]
     *
     * @return array
     *      Array of Audioclip objects
     */
    public static function BrowseCategory($p_category, $offset = 0, $limit = 0,
                                          $conditions = array(),
                                          $operator = 'and',
                                          $orderby = 'dc:creator, dc:source, dc:title',
                                          $desc = false)
    {
        global $mdefs;

        $xrc = XR_CcClient::Factory($mdefs, true);
        if (PEAR::isError($xrc)) {
            return $xrc;
        }
        $sessid = camp_session_get(CS_FILEARCHIVE_SESSION_VAR_NAME, '');
        $criteria = array(
            'filetype' => 'text',
            'operator' => $operator,
            'limit' => $limit,
            'offset' => $offset,
            'orderby' => $orderby,
            'desc' => $desc,
            'conditions' => $conditions
        );
        return $xrc->xr_browseCategory($sessid, $p_category, $criteria);
    } // fn BrowseCategory


    /**
     * Use getid3 to retrieve all the metatags for the given audio file.
     *
     * @param string $p_file
     *      The file to analyze
     *
     * @return array
     *      An array with all the id3 metatags
     */
    public static function AnalyzeFile($p_file)
    {
        require_once($GLOBALS['g_campsiteDir'].'/include/getid3/getid3.php');

        $getid3Obj = new getID3;
        return $getid3Obj->analyze($p_file);
    } // fn AnalyzeFile

} // class Archive_TextFile

?>