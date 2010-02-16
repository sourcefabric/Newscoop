<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/classes/Archive_FileBase.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');


/**
 * @package Campsite
 */
class Archive_ImageFile extends Archive_FileBase
{
    protected $m_fileType = 'image';

    protected $m_metatagLabels = array(
        // generic tags for all file types
        'dc:title' => 'File name',
        'dc:format' => 'File format',
        'dc:description' => 'Description',
        'dc:rights' => 'Copyright',
        'ls:crc' => 'Checksum',
        'ls:filename' => 'File name',
        'ls:filesize' => 'File size',
        'ls:filetype' => 'File type',
        'ls:mtime' => 'Modified time',
        'ls:url' => 'URL',
        // special tags
        'dc:maker' => 'Camera maker',
        'dc:maker_model' => 'Camera model',
        'dc:date_time' => 'Date/Time original',
        'ls:image_width' => 'Image width size',
        'ls:image_height' => 'Image height size',
        'ls:bitspersample' => 'Bits per sample',
        'ls:photographer' => 'Photographer',
        'ls:place' => 'Place',
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

    protected $m_fileTypes = array(
        '.jpeg' => array('name' => 'JPEG',
                         'icon' => 'filearchive_image_48x48.png'),
        '.jpg'  => array('name' => 'JPEG',
                         'icon' => 'filearchive_image_48x48.png'),
        '.png'  => array('name' => 'PNG (Portable Network Graphics)',
                         'icon' => 'filearchive_image_48x48.png'),
        '.gif'  => array('name' => 'GIF',
                         'icon' => 'filearchive_image_48x48.png'),
        '.bmp'  => array('name' => 'Microsoft Windows bitmap',
                         'icon' => 'filearchive_image_48x48.png'),
        '.tiff' => array('name' => 'TIFF',
                         'icon' => 'filearchive_image_48x48.png'),
        '.tif'  => array('name' => 'TIFF',
                         'icon' => 'filearchive_image_48x48.png'),
        '.swf'  => array('name' => 'SWF (Small Web Format)',
                         'icon' => 'filearchive_image_48x48.png'),
        '.pcd'  => array('name' => 'Kodak Photo-CD',
                         'icon' => 'filearchive_image_48x48.png'),
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
     * @return int
     */
    public function getImageId()
    {
        return $this->getGunId();
    } // fn getImageId


    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->m_fileType;
    } // fn getFileType


    /**
     * @return array
     */
    public function getMetatagLabels()
    {
        return $this->m_metatagLabels;
    } // fn getMetatagLabels


    /**
     * @return array
     */
    public function getMask()
    {
        return $this->m_mask;
    } // fn getMask


    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getMetatagValue('dc:description');
    } // fn getDescription


    /**
     * @return string
     */
    public function getPhotographer()
    {
        return $this->getMetatagValue('ls:photographer');
    } // fn getPhotographer


    /**
     * @return string
     */
    public function getPlace()
    {
        return $this->getMetatagValue('ls:place');
    } // fn getPlace


    /**
     * @return string
     */
    public function getDate()
    {
        return $this->getMetatagValue('dc:date_time');
    } // fn getDate


    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->getMetatagValue('dc:description');
    } // fn getLocation


    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->getMetatagValue('ls:url');
    } // fn getUrl


    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->getMimeType();
    } // fn getContentType


    /**
     * Return true if the image is being used by an article.
     *
     * @return boolean
     */
    public function inUse()
    {
        return ArticleImage::GetArticlesThatUseImage($this->getImageId(), true) > 0;
    } // fn inUse


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
      	$criteria = array(
      	    'filetype' => 'image',
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
    
    
    public static function Store($p_sessId, $p_filePath, $p_metaData, $p_fileType,
                                 $p_userId, $p_storeLocal = false)
    {
    	$gunId = parent::Store($p_sessId, $p_filePath, $p_metaData, $p_filePath, $p_userId, true);
    	if (PEAR::isError($gunId)) {
    		return $gunId;
    	}
    }


    private function __ImageTypeToExtension($p_imageType)
    {
        $extension = '';
        switch($p_imageType) {
           case IMAGETYPE_GIF: $extension = 'gif'; break;
           case IMAGETYPE_JPEG: $extension = 'jpg'; break;
           case IMAGETYPE_PNG: $extension = 'png'; break;
           case IMAGETYPE_SWF: $extension = 'swf'; break;
           case IMAGETYPE_PSD: $extension = 'psd'; break;
           case IMAGETYPE_BMP: $extension = 'bmp'; break;
           case IMAGETYPE_TIFF_II: $extension = 'tiff'; break;
           case IMAGETYPE_TIFF_MM: $extension = 'tiff'; break;
           case IMAGETYPE_JPC: $extension = 'jpc'; break;
           case IMAGETYPE_JP2: $extension = 'jp2'; break;
           case IMAGETYPE_JPX: $extension = 'jpx'; break;
           case IMAGETYPE_JB2: $extension = 'jb2'; break;
           case IMAGETYPE_SWC: $extension = 'swc'; break;
           case IMAGETYPE_IFF: $extension = 'aiff'; break;
           case IMAGETYPE_WBMP: $extension = 'wbmp'; break;
           case IMAGETYPE_XBM: $extension = 'xbm'; break;
        }
        return $extension;
    } // fn __ImageTypeToExtension


    private function __GetImageTypeCreateMethod($p_imageType)
    {
        $method = null;
        switch ($p_imageType) {
           case IMAGETYPE_GIF: $method = 'imagecreatefromgif'; break;
           case IMAGETYPE_JPEG: $method = 'imagecreatefromjpeg'; break;
           case IMAGETYPE_PNG: $method = 'imagecreatefrompng'; break;
           case IMAGETYPE_SWF: $method = null; break;
           case IMAGETYPE_PSD: $method = null; break;
           case IMAGETYPE_BMP: $method = null; break;
           case IMAGETYPE_TIFF_II: $method = null; break;
           case IMAGETYPE_TIFF_MM: $method = null; break;
           case IMAGETYPE_JPC: $method = null; break;
           case IMAGETYPE_JP2: $method = null; break;
           case IMAGETYPE_JPX: $method = null; break;
           case IMAGETYPE_JB2: $method = null; break;
           case IMAGETYPE_SWC: $method = null; break;
           case IMAGETYPE_IFF: $method = null; break;
           case IMAGETYPE_WBMP: $method = 'imagecreatefromwbmp'; break;
           case IMAGETYPE_XBM: $method = 'imagecreatefromxbm'; break;
        }
        return $method;
    } // fn __GetImageTypeCreateMethod


    /**
     * Resizes the given image
     *
     * @param resource $p_image
     *      The image resource handler
     * @param int $p_maxWidth
     *      The maximum width of the resized image
     * @param int $p_maxHeight
     *      The maximum height of the resized image
     * @param bool $p_keepRatio
     *      If true keep the image ratio
     * @return int
     *      Return the new image resource handler.
     */
    public static function ResizeImage($p_image, $p_maxWidth, $p_maxHeight,
                                       $p_keepRatio = true)
    {
        $origImageWidth = imagesx($p_image);
        $origImageHeight = imagesy($p_image);
        if ($origImageWidth <= 0 || $origImageHeight <= 0) {
            return new PEAR_Error(getGS("The file uploaded is not an image."));
        }

        $p_maxWidth = is_numeric($p_maxWidth) ? (int) $p_maxWidth : 0;
        $p_maxHeight = is_numeric($p_maxHeight) ? (int) $p_maxHeight : 0;
        if ($p_maxWidth <= 0 || $p_maxHeight <= 0) {
            return new PEAR_Error(getGS("Invalid resize width/height."));
        }
        if ($p_keepRatio) {
            $ratioOrig = $origImageWidth / $origImageHeight;
            $ratioNew = $p_maxWidth / $p_maxHeight;
            if ($ratioNew > $ratioOrig) {
                $newImageWidth = $p_maxHeight * $ratioOrig;
                $newImageHeight = $p_maxHeight;
            } else {
                $newImageWidth = $p_maxWidth;
                $newImageHeight = $p_maxWidth / $ratioOrig;
            }
        } else {
            $newImageWidth = $p_maxWidth;
            $newImageHeight = $p_maxHeight;
        }
        $newImage = imagecreatetruecolor($newImageWidth, $newImageHeight);
        imagecopyresampled($newImage, $p_image, 0, 0, 0, 0, $newImageWidth, $newImageHeight,
                           $origImageWidth, $origImageHeight);
        return $newImage;
    } // fn ResizeImage


    /**
     * Saves the image refered by the resource handler to a file
     *
     * @param resource $p_image
     *      Image resource handler
     * @param string $p_fileName
     *      The full path of the file
     * @param int $p_type
     *      The image type
     * @param bool $p_addExtension
     *      If true it will add the proper extension to the file name.
     * @return mixed
     *      true if successful, PEAR_Error object in case of error
     */
    public static function SaveImageToFile($p_image, $p_fileName,
                                           $p_imageType, $p_addExtension = true)
    {
        $method = null;
        switch ($p_imageType) {
           case IMAGETYPE_GIF: $method = 'imagegif'; break;
           case IMAGETYPE_JPEG: $method = 'imagejpeg'; break;
           case IMAGETYPE_PNG: $method = 'imagepng'; break;
           case IMAGETYPE_WBMP: $method = 'imagewbmp'; break;
           case IMAGETYPE_XBM: $method = 'imagexbm'; break;
        } // these are the supported image types
        if ($method == null) {
            return new PEAR_Error(getGS("Image type $1 is not supported.",
                                  image_type_to_mime_type($p_imageType)));
        }
        if (!$method($p_image, $p_fileName)) {
            return new PEAR_Error(camp_get_error_message(CAMP_ERROR_CREATE_FILE, $p_fileName),
                                  CAMP_ERROR_CREATE_FILE);
        }
        return true;
    } // SaveImageToFile

} // class Archive_ImageFile

?>