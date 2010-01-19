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
class Archive_ApplicationFile extends Archive_FileBase
{
    protected $m_fileType = 'application';

    protected $m_metatagLabels = array(
        // generic tags for all file types
        'dc:title' => 'Title',
        'dc:format' => 'File format',
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
            'Application'  => array(
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
        '.mp3' => array(
            'name' => 'MP3 audio',
            'icon' => 'filearchive_application_48x48.png'),
        '.ai' => array(
            'name' => 'PostScript',
            'icon' => 'filearchive_application_48x48.png'),
        '.eps' => array(
            'name' => 'PostScript',
            'icon' => 'filearchive_application_48x48.png'),
        '.ps' => array(
            'name' => 'PostScript',
            'icon' => 'filearchive_application_48x48.png'),
        '.rtf' => array(
            'name' => 'Rich Text Format',
            'icon' => 'filearchive_application_48x48.png'),
        '.pdf' => array(
            'name' => 'PDF',
            'icon' => 'filearchive_application_48x48.png'),
        '.latex' => array(
            'name' => 'LaTeX document',
            'icon' => 'filearchive_application_48x48.png'),
        '.tex' => array(
            'name' => 'Tex/LateX document',
            'icon' => 'filearchive_application_48x48.png'),
        '.texinfo' => array(
            'name' => 'GNU Texinfo document',
            'icon' => 'filearchive_application_48x48.png'),
        '.texi' => array(
            'name' => 'GNU Texinfo document',
            'icon' => 'filearchive_application_48x48.png'),
        '.dvi' => array(
            'name' => 'TeX dvi format',
            'icon' => 'filearchive_application_48x48.png'),
        '.rar' => array(
            'name' => 'RAR Archive',
            'icon' => 'filearchive_application_48x48.png'),
        '.gtar' => array(
            'name' => 'GNU tar format',
            'icon' => 'filearchive_application_48x48.png'),
        '.tar' => array(
            'name' => 'BSD tar format',
            'icon' => 'filearchive_application_48x48.png'),
        '.ustar' => array(
            'name' => 'POSIX tar format',
            'icon' => 'filearchive_application_48x48.png'),
        '.bcpio' => array(
            'name' => 'Old CPIO format',
            'icon' => 'filearchive_application_48x48.png'),
        '.cpio' => array(
            'name' => 'POSIX CPIO format',
            'icon' => 'filearchive_application_48x48.png'),
        '.shar' => array(
            'name' => 'UNIX sh shell archive',
            'icon' => 'filearchive_application_48x48.png'),
        '.zip' => array(
            'name' => 'DOS/PC Pkzipped archive',
            'icon' => 'filearchive_application_48x48.png'),
        '.gz' => array(
            'name' => 'GNU Zip',
            'icon' => 'filearchive_application_48x48.png'),
        '.hqx' => array(
            'name' => 'Mac binhexed archive',
            'icon' => 'filearchive_application_48x48.png'),
        '.sti' => array(
            'name' => 'Mac Stuffit archive',
            'icon' => 'filearchive_application_48x48.png'),
        '.sea' => array(
            'name' => 'Mac Stuffit archive',
            'icon' => 'filearchive_application_48x48.png'),
        '.fif' => array(
            'name' => 'Fractal Image format',
            'icon' => 'filearchive_application_48x48.png'),
        '.bin' => array(
            'name' => 'Binary',
            'icon' => 'filearchive_application_48x48.png'),
        '.uu' => array(
            'name' => 'UUencoded',
            'icon' => 'filearchive_application_48x48.png'),
        '.exe' => array(
            'name' => 'PC executable',
            'icon' => 'filearchive_application_48x48.png'),
        '.hdf' => array(
            'name' => 'NCSA HDF data format',
            'icon' => 'filearchive_application_48x48.png'),
        '.js' => array(
            'name' => 'Javascript program',
            'icon' => 'filearchive_application_48x48.png'),
        '.sh' => array(
            'name' => 'UNIX bourne shell program',
            'icon' => 'filearchive_application_48x48.png'),
        '.csh' => array(
            'name' => 'UNIX c-shell program',
            'icon' => 'filearchive_application_48x48.png'),
        '.pl' => array(
            'name' => 'Perl program',
            'icon' => 'filearchive_application_48x48.png'),
        '.tcl' => array(
            'name' => 'Tcl program',
            'icon' => 'filearchive_application_48x48.png'),
        '.*'   => array(
            'name' => 'Unknown',
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
            'filetype' => 'application',
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
            'filetype' => 'audioclip',
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

} // class Archive_ApplicationFile

?>