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
class Archive_ImageFile extends Archive_FileBase
{
    protected $m_fileType = 'image';

    protected $m_metatagLabels = array(
	'dc:title' => 'File name',
	'dc:format' => 'File format',
	'dc:description' => 'Description',
	'dc:format' => 'Format',
	'dc:maker' => 'Camera maker',
	'dc:maker_model' => 'Camera model',
	'dc:date_time' => 'Date/Time original',
	'ls:filename' => 'File name',
	'ls:filesize' => 'File size',
	'ls:filetype' => 'File type',
	'ls:image_width' => 'Image width size',
	'ls:image_height' => 'Image height size',
	'ls:bitspersample' => 'Bits per sample',
	'ls:mtime' => 'Modified time',
	'ls:photographer' => 'Photographer',
	'ls:place' => 'Place',
	'ls:url' => 'URL'
    );

    protected $m_mask = array(
        'pages' => array(
	    'Main'  => array(
	        array(
		    'element' => 'dc:title',
		    'type' => 'text',
		    'required' => TRUE,
		    'attributes' => array('disabled' => 'on'),
		),
		array(
		    'element' => 'dc:description',
		    'type' => 'textarea',
		),
		array(
		    'element' => 'ls:photographer',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:format',
		    'type' => 'select',
		    'required' => TRUE,
		    'options' => array(
		        'File' => 'Audioclip',
			'live stream' => 'Webstream'
		    ),
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
	    'Image'  => array(
	        array(
		    'element' => 'ls:filename',
		    'type' => 'text',
		    'attributes' => array('disabled' => 'on'),
		),
		array(
		    'element' => 'ls:photographer',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:place',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:url',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:maker',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:maker_model',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:date_time',
		    'type' => 'text',
		    'attributes' => array('disabled' => 'on'),
		),
		array(
		    'element' => 'ls:image_width',
		    'type' => 'text',
		    'attributes' => array('disabled' => 'on'),
		),
		array(
		    'element' => 'ls:image_height',
		    'type' => 'text',
		    'attributes' => array('disabled' => 'on'),
		),
		array(
		    'element' => 'ls:bitspersample',
		    'type' => 'text',
		    'rule' => 'numeric',
		    'attributes' => array('disabled' => 'on'),
		),
	    )
	)
    );

    protected $m_fileTypes = array('.jpeg','.jpg','.png','.gif','.bmp',
				   '.tiff','.tif','.swf','.pcd');


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


    public function getFileType()
    {
      return $this->m_fileType;
    }


    /**
     *
     */
    public function getMetatagLabels()
    {
        return $this->m_metatagLabels;
    }


    /**
     *
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
    public static function SearchImageFiles($offset = 0, $limit = 0,
                                            $conditions = array(),
                                            $operator = 'and',
					    $orderby = 'dc:creator, dc:source, dc:title',
                                            $desc = false)
    {
      	$criteria = array('filetype' => 'image',
			  'operator' => $operator,
			  'limit' => $limit,
			  'offset' => $offset,
			  'orderby' => $orderby,
			  'desc' => $desc,
			  'conditions' => $conditions
			  );
	return parent::SearchFiles($criteria);
    } // fn SearchImageFile


    /**
     * Retrieve a list of values of the given category that meet the
     * given constraints.
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
	$criteria = array('filetype' => 'audioclip',
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
     * Use getid3 to retrieve all the metatags for the given file.
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

} // class Archive_ImageFile

?>