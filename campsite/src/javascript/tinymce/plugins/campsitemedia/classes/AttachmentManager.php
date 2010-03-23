<?php
/**
 * ImageManager, list attachment files
 * @author $Author: holman $
 * @version $Id: AttachmentManager.php 5087 2009-04-10 08:34:08Z holman $
 */

require_once('Files.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Attachment.php');

/**
 * AttachmentManager Class.
 * @author $Author: holman $
 * @version $Id: AttachmentManager.php 5087 2009-04-10 08:34:08Z holman $
 */
class AttachmentManager
{
    /**
     * Configuration array.
     */
    var $m_config;

    /**
     * Array of directory information.
     */
    var $m_dirs;


    /**
     * Constructor. Create a new Attachment Manager instance.
     * @param array $config configuration array, see config.inc.php
     */
    function __construct($p_config)
    {
        $this->m_config = $p_config;
    }


    /**
     * Get the base directory.
     * @return string base dir, see config.inc.php
     */
    function getBaseDir()
    {
        return $this->m_config['base_dir'];
    }


    /**
     * Get the base URL.
     * @return string base url, see config.inc.php
     */
    function getBaseURL()
    {
        return $this->m_config['base_url'];
    }


    /**
     *
     */
    function isValidBase()
    {
        return is_dir($this->getBaseDir());
    }


    /**
     * Get all the files and directories of a relative path.
     *
     * @param string $p_articleId
     *
     * @return array of file and path information.
     * <code>
     *   array('url'=>'full url',
     *         'storage'=>'full file path')
     * </code>
     */
    function getFiles($p_articleId, $p_languageId = null, $p_filter = true)
    {
        $files = array();

	if ($this->isValidBase() == false)
	    return $files;

	$mediaFormats = array();
	if ($p_filter == true) {
	    $mediaFormats = explode(',', $this->m_config['media_formats']);
	}
	$articleAttachments = ArticleAttachment::GetAttachmentsByArticleNumber($p_articleId, $p_languageId);
	foreach ($articleAttachments as $articleAttachment) {
	    if (!$this->config['validate_files']) {
	        if (in_array($articleAttachment->getExtension(), $mediaFormats)) {
		  $file['attachment'] = $articleAttachment;
		  $file['url'] = $articleAttachment->getAttachmentUrl();
		  $file['storage'] = $articleAttachment->getStorageLocation();
		  $files[$articleAttachment->getAttachmentId()] = $file;
		}
	    }
	}

	ksort($files);
	return $files;
    } // fn getFiles


    /**
     * Count the number of files and directories in a given folder
     * minus the thumbnail folders and thumbnails.
     */
    function countFiles($path)
    {
        $total = 0;

	if (is_dir($path)) {
	    $d = @dir($path);

	    while (false !== ($entry = $d->read())) {
	        //echo $entry."<br>";
	        if (substr($entry,0,1) != '.'
		        && $this->isThumbDir($entry) == false
		        && $this->isTmpFile($entry) == false
		        && $this->isThumb($entry) == false) {
		    $total++;
		}
	    }
	    $d->close();
	}
	return $total;
    } // fn countFiles


    /**
     * Check if the given file is a tmp file.
     * @param string $file file name
     * @return boolean true if it is a tmp file, false otherwise
     */
    function isTmpFile($file)
    {
        $len = strlen($this->config['tmp_prefix']);
	if (substr($file,0,$len) == $this->config['tmp_prefix'])
	    return true;
	else
	    return false;
    }


    /**
     * Check if the given path is part of the subdirectories
     * under the base_dir.
     * @param string $path the relative path to be checked
     * @return boolean true if the path exists, false otherwise
     */
    function validRelativePath($path)
    {
        $dirs = $this->getDirs();
	if ($path == '/')
	    return true;
	//check the path given in the url against the
	//list of paths in the system.
	for($i = 0; $i < count($dirs); $i++) {
	    $key = key($dirs);
	    //we found the path
	    if ($key == $path)
	        return true;

	    next($dirs);
	}
	return false;
    }


    /**
     * Get the URL of the relative file.
     * basically appends the relative file to the
     * base_url given in config.inc.php
     * @param string $relative a file the relative to the base_dir
     * @return string the URL of the relative file.
     */
    function getFileURL($relative)
    {
        return Files::makeFile($this->getBaseURL(),$relative);
    }


    /**
     * Get the fullpath to a relative file.
     * @param string $relative the relative file.
     * @return string the full path, .ie. the base_dir + relative.
     */
    function getFullPath($relative)
    {
        return Files::makeFile($this->getBaseDir(),$relative);
    }


    /**
     * Get the default thumbnail.
     * @return string default thumbnail, empty string if
     * the thumbnail doesn't exist.
     */
    function getDefaultThumb()
    {
      if (is_file($this->config['default_thumbnail']))
	  return $this->config['default_thumbnail'];
      else
	  return '';
    }

} // class AttachmentManager

?>