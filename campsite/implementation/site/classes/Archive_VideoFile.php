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
class Archive_VideoFile extends Archive_FileBase
{
    protected $m_fileType = 'video';

    protected $m_metatagLabels = array(
        'dc:title' => 'Title',
	'dc:creator' => 'Creator',
	'dc:type' => 'Genre',
	'dc:format' => 'File format',
	'dcterms:extent' => 'Length',
	'dc:title' => 'Title',
	'dc:creator' => 'Creator',
	'dc:source' => 'Album',
	'ls:year' => 'Year',
	'dc:type' => 'Genre',
	'dc:description' => 'Description',
	'dc:format' => 'Format',
	'dcterms:extent' => 'Length',
	'ls:audio_encoded_by' => 'Encoded by',
	'ls:composer' => 'Composer',
	'ls:bitrate' => 'Bitrate',
	'ls:video_bitrate' => 'Video Bitrate',
	'ls:audio_bitrate' => 'Audio Bitrate',
	'ls:audio_channels' => 'Channels',
	'ls:audio_samplerate' => 'Sample rate',
	'ls:video_encoder' => 'Video encoder software used',
	'ls:audio_encoder' => 'Audio encoder software used',
	'dc:title' => 'Title',
	'dc:description' => 'Description',
	'dc:creator' => 'Creator',
	'dc:subject' => 'Subject',
	'dc:type' => 'Genre',
	'dc:format' => 'Format',
	'dc:title' => 'Title',
	'dc:creator' => 'Creator',
	'dcterms:extent' => 'Length',
	'dc:description' => 'Description',
	'ls:url' => 'Stream URL',
	'ls:mtime' => 'Modified time',
	'ls:filesize' => 'File size',
	'ls:video_total_frames',
	'ls:video_frame_rate',
	'ls:video_frame_width',
	'ls:video_frame_height',
	'ls:video_bgcolor'
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
		    'element' => 'dc:creator',
		    'type' => 'text',
		    'required' => TRUE,
		),
		array(
		    'element'=> 'dc:type',
		    'type' => 'text',
		    'required' => TRUE,
		),
		array(
		    'element' => 'dc:format',
		    'type' => 'select',
		    'required' => TRUE,
		    'options' => array(
		        'File' => 'Video',
		    ),
		    'attributes'=> array('disabled' => 'on'),
		),
		array(
		    'element' => 'dcterms:extent',
		    'type' => 'text',
		    'attributes' => array('disabled' => 'on'),
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
	    'Audio'  => array(
	        array(
		    'element' => 'dc:title',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:creator',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:source',
		    'type' => 'text',
		    'id3' => array('Album')
		),
		array(
		    'element' => 'ls:year',
		    'type' => 'select',
		    'options' => '', //_getNumArr(1900, date('Y')+5),
		),
		array(
		    'element' => 'dc:type',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:description',
		    'type' => 'textarea',
		),
		array(
		    'element' => 'dc:format',
		    'type' => 'select',
		    'options' => array(
		        'File' => 'Video',
		    ),
		    'attributes'=> array('disabled' => 'on'),
	        ),
	    ),
	    'Video' => array(
	        array(
		    'element' => 'dc:title',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:video_bitrate',
		    'type' => 'text',
		    'rule' => 'numeric',
		    'attributes'=> array('disabled' => 'on'),
		),
		array(
		    'element' => 'ls:video_encoder',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:video_total_frames',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:video_frame_rate',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:video_frame_width',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:video_frame_height',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:video_bgcolor',
		    'type' => 'text',
		),
	    )
	)
    );

    protected $m_fileTypes = array('.avi','.wmv','.wma','.mpg','.mpeg',
				   '.mov','.rm','.mp4','.mkv','.nsv');


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
    public static function SearchAudioFiles($offset = 0, $limit = 0,
                                            $conditions = array(),
                                            $operator = 'and',
					    $orderby = 'dc:creator, dc:source, dc:title',
                                            $desc = false)
    {
      	$criteria = array('filetype' => 'video',
			  'operator' => $operator,
			  'limit' => $limit,
			  'offset' => $offset,
			  'orderby' => $orderby,
			  'desc' => $desc,
			  'conditions' => $conditions
			  );
	return parent::SearchFiles($criteria);
    } // fn SearchAudioFile


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
	$criteria = array('filetype' => 'video',
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

} // class Archive_VideoFile

?>