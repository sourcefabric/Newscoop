<?php
/**
 * @package Campsite
 */


/**
 * FileTextSearch provides flexible find and replace features
 * within files of a given path.
 *
 * @package Campsite
 */
class FileTextSearch {
	var $m_fileExtensions = array();
	var $m_searchKey = '';
	var $m_replacementKey = '';
	var $m_caseSensitive = 0;
	var $m_findAllExtensions = 1;
	var $m_isReplaceEnabled = 0;
	var $m_totalFound = 0;

	/**
	 * Add a valid search extension.
	 * @param string $p_extension
	 */
	function addExtension($p_extension)
	{
		array_push($this->m_fileExtensions, $p_extension);
		$this->m_findAllExtensions = 0;
	} // fn addExtension


	/**
	 * Sets extensions to search for.
	 * @param array $p_extensions
	 */
	function setExtensions($p_extensions = array())
	{
		$this->m_fileExtensions = $p_extensions;
		if (count($this->m_fileExtensions) > 0) {
			$this->m_findAllExtensions = 0;
		}
	} // fn setExtensions


	/**
	 * Sets the pattern and case sensitivity.
	 * @param string $p_searchKey
	 * @param int $p_caseSensitive
	 */
	function setSearchKey($p_searchKey, $p_caseSensitive = 0)
	{
		$this->m_searchKey = $p_searchKey;
		if ($p_caseSensitive == 1) {
			$this->m_caseSensitive = 1;
		}
	} // fn setSearchKey


	/**
	 * Sets the replacement to replace pattern with.
	 * @param string $p_replacementKey
	 */
	function setReplacementKey($p_replacementKey)
	{
		$this->m_replacementKey = $p_replacementKey;
		$this->m_isReplaceEnabled = 1;
	} // fn setReplacementKey


	/**
	 * searchDirFiles() wrapper function.
	 * @param string $p_path
	 */
	function findReplace($p_path)
	{
		$this->searchDirFiles($p_path);
	} // fn findReplace


	/**
	 * Runs recursively all into the path.
	 * @param string $p_path
	 */
	function searchDirFiles($p_path)
	{
		$dirHandle = opendir($p_path);
		while ($file = readdir($dirHandle)) {
			if (($file == '.') || ($file == '..')) {
				continue;
			}

			$filePath = $p_path.'/'.$file;
			if (filetype($filePath) == 'dir') {
				$this->searchDirFiles($filePath);
			} elseif ($this->matchedExtension($file)) {
				if (filesize($filePath)) {
					$this->searchFileData($filePath);
				}
			}
		}
		closedir($dirHandle);
	} // fn searchDirFiles


	/**
	 * Finds the extension for the given file.
	 * @param string $p_file
	 * @return mixed the extension on success or NULL on failure
	 */
	function findExtension($p_file)
	{
		return array_pop(explode('.', $p_file));
	} // fn findExtension


	/**
	 * Checks if a file extension is one the extensions
	 * we are going to search.
	 * @param string $p_file
	 * @return bool true on success or false on failure
	 */
	function matchedExtension($p_file)
	{
		if ($this->m_findAllExtensions) {
			return true;
		} elseif (count(array_keys($this->m_fileExtensions, $this->findExtension($p_file))) == 1) {
			return true;
		}
		return false;
	} // fn matchedExtension


	/**
	 * Searches file data and replaces with given pattern.
	 * @param string $p_file
	 */
	function searchFileData($p_file)
	{
		$pattern = preg_quote($this->m_searchKey, '/');
		if ($this->m_caseSensitive) {
			$pattern = "/$pattern/U";
		} else {
			$pattern = "/$pattern/Ui";
		}
		$content = file_get_contents($p_file);
		$found = 0;
		$found = preg_match_all($pattern, $content, $matches, PREG_PATTERN_ORDER);
		$this->m_totalFound += $found;
		if ($this->m_isReplaceEnabled && $this->m_replacementKey && $found) {
			$content = preg_replace($pattern, $this->m_replacementKey, $content);
			$this->filePutContents($p_file, $content);
		}
	} // fn searchFileData


	/**
	 * Puts data (after replacement) to given file.
	 * @param string $p_file
	 * @param mixed $p_data
	 * @return mixed
	 */
	function filePutContents($p_file, $p_data)
	{
		$handle = @fopen($p_file, 'w');
		if ($handle === false) {
			return false;
		} else {
			if (is_array($p_data)) $p_data = implode($p_data);
			$bytes_written = fwrite($handle, $p_data);
			fclose($handle);
			return $bytes_written;
		}
	} // fn filePutContents

} // class FileTextSearch

?>
