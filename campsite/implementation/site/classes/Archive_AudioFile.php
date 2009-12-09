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
class Archive_AudioFile extends Archive_FileBase
{
    protected $m_fileType = 'audio';

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
	'ls:bpm' => 'BPM',
	'ls:rating' => 'Rating',
	'dcterms:extent' => 'Length',
	'ls:encoded_by' => 'Encoded by',
	'ls:track_num' => 'Track number',
	'ls:disc_num' => 'Disc number',
	'ls:mood' => 'Mood',
	'dc:publisher' => 'Label',
	'ls:composer' => 'Composer',
	'ls:bitrate' => 'Bitrate',
	'ls:channels' => 'Channels',
	'ls:samplerate' => 'Sample rate',
	'ls:encoder' => 'Encoder software used',
	'ls:crc' => 'Checksum',
	'ls:lyrics' => 'Lyrics',
	'ls:orchestra' => 'Orchestra or band',
	'ls:conductor' => 'Conductor',
	'ls:lyricist' => 'Lyricist',
	'ls:originallyricist' => 'Original lyricist',
	'ls:radiostationname' => 'Radio station name',
	'ls:audiofileinfourl' => 'Audio file information web page',
	'ls:artisturl' => 'Artist web page',
	'ls:audiosourceurl' => 'Audio source web page',
	'ls:radiostationurl' => 'Radio station web page',
	'ls:buycdurl' => 'Buy CD web page',
	'ls:isrcnumber' => 'ISRC number',
	'ls:catalognumber' => 'Catalog number',
	'ls:originalartist' => 'Original artist',
	'dc:rights' => 'Copyright',
	'dc:title' => 'Title',
	'dcterms:temporal' => 'Report date/time',
	'dcterms:spatial' => 'Report location',
	'dcterms:entity' => 'Report organizations',
	'dc:description' => 'Description',
	'dc:creator' => 'Creator',
	'dc:subject' => 'Subject',
	'dc:type' => 'Genre',
	'dc:format' => 'Format',
	'dc:contributor' => 'Contributor',
	'dc:language' => 'Language',
	'dc:rights' => 'Copyright',
	'dc:title' => 'Title',
	'dc:creator' => 'Creator',
	'dcterms:extent' => 'Length',
	'dc:description' => 'Description',
	'ls:url' => 'Stream URL',
	'ls:mtime' => 'Modified time',
	'ls:filesize' => 'File size'
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
		        'File' => 'Audioclip',
			'live stream' => 'Webstream'
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
	    'Music'  => array(
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
		        'File' => 'Audioclip',
			'live stream' => 'Webtream'
		    ),
		    'attributes'=> array('disabled' => 'on'),
	        ),
		array(
		    'element' => 'ls:bpm',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:rating',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'dcterms:extent',
		    'type' => 'text',
		    'attributes' => array('disabled' => 'on'),
		),
		array(
		    'element' => 'ls:encoded_by',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:track_num',
		    'type' => 'select',
		    'options' => '', //_getNumArr(0, 99),
		),
		array(
		    'element' => 'ls:disc_num',
		    'type' => 'select',
		    'options' => '', //_getNumArr(0, 20),
		),
		array(
		    'element' => 'ls:mood',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:publisher',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:composer',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:bitrate',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:channels',
		    'type' => 'select',
		    'options' => array(
		        '' => '',
			1 => 'Mono',
			2 => 'Stereo',
			6 => '5.1'
		    ),
		),
		array(
		    'element' => 'ls:samplerate',
		    'type' => 'text',
		    'rule' => 'numeric',
		    'attributes'=> array('disabled' => 'on'),
		),
		array(
		    'element' => 'ls:encoder',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:crc',
		    'type' => 'text',
		    'rule' => 'numeric',
	        ),
		array(
		    'element' => 'ls:lyrics',
		    'type' => 'textarea',
		),
		array(
		    'element' => 'ls:orchestra',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:conductor',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:lyricist',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:originallyricist',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:radiostationname',
		    'type' => 'text',
		),
		array(
		    'element' => 'ls:audiofileinfourl',
		    'type' => 'text',
		    'attributes' => array('maxlength' => 256)
	        ),
		//array(
		//    'rule' => 'regex',
		//    'element' => 'ls:audiofileinfourl',
		//    'format' => '', //UI_REGEX_URL,
		//    'rulemsg' => 'Audio file information web page seems not to be valid URL'
		//),
		array(
		    'element' => 'ls:artisturl',
		    'type' => 'text',
		    'attributes' => array('maxlength' => 256)
		),
		//array(
		//    'rule' => 'regex',
		//    'element' => 'ls:artisturl',
		//    'format' => '', //UI_REGEX_URL,
		//    'rulemsg' => 'Artist web page seems not to be valid URL'
		//),
		array(
		    'element' => 'ls:audiosourceurl',
		    'type' => 'text',
		    'attributes' => array('maxlength' => 256)
		),
		//array(
		//    'rule' => 'regex',
		//    'element' => 'ls:audiosourceurl',
		//    'format' => '', //UI_REGEX_URL,
		//    'rulemsg' => 'Audio source web page seems not to be valid URL'
		//),
		array(
		    'element' => 'ls:radiostationurl',
		    'type' => 'text',
		    'attributes' => array('maxlength' => 256)
		),
		//array(
		//    'rule' => 'regex',
		//    'element' => 'ls:radiostationurl',
		//    'format' => '', //UI_REGEX_URL,
		//    'rulemsg' => 'Radio station web page seems not to be valid URL'
		//),
		array(
		    'element' => 'ls:buycdurl',
		    'type' => 'text',
		    'attributes' => array('maxlength' => 256)
	        ),
		//array(
		//    'rule' => 'regex',
		//    'element' => 'ls:buycdurl',
		//    'format' => '', //UI_REGEX_URL,
		//    'rulemsg' => 'Buy CD web page seems not to be valid URL'
		//),
		array(
		    'element' => 'ls:isrcnumber',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:catalognumber',
		    'type' => 'text',
		    'rule' => 'numeric',
		),
		array(
		    'element' => 'ls:originalartist',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:rights',
		    'type' => 'text',
		),
	    ),
	    'Voice' => array(
	        array(
		    'element' => 'dc:title',
		    'type' => 'text',
		),
		array(
		    'element' => 'dcterms:temporal',
		    'type' => 'text',
		),
		array(
		    'element' => 'dcterms:spatial',
		    'type' => 'textarea',
		),
		array(
		    'element' => 'dcterms:entity',
		    'type' => 'textarea',
		),
		array(
		    'element' => 'dc:description',
		    'type' => 'textarea',
		),
		array(
		    'element' => 'dc:creator',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:subject',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:type',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:format',
		    'type' => 'select',
		    'options' => array(
		        'File' => 'Audioclip',
			'live stream' => 'Webstream'
		    ),
		    'attributes'=> array('disabled' => 'on')
		),
		array(
		    'element' => 'dc:contributor',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:language',
		    'type' => 'text',
		),
		array(
		    'element' => 'dc:rights',
		    'type' => 'text',
	        ),
	    )
	)
    );

    protected $m_fileTypes = array('.mp3','.ogg','.wav','.aiff','.aif',
				   '.flac','.mid','.au','.aac','.ra','.spx');


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
      	$criteria = array('filetype' => 'audio',
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

} // class Archive_AudioFile

?>