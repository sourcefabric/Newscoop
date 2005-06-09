<?php

class ImageSearch {
	var $m_isSearch;
	var $m_orderBy;
	var $m_orderDirection;
	var $m_imageOffset;
	var $m_searchDescription;
	var $m_searchPhotographer;
	var $m_searchDate;
	var $m_searchPlace;
	var $m_searchInUse;
	var $m_searchUploadedBy;
	var $m_imageData;
	var $m_numImagesFound;
	var $m_orderQuery;
	var $m_whereQuery;
	var $m_imagesPerPage = 10;
		
	/**
	 * This class can search for images matching specific criteria.
	 * Give the search criteria in the contructor, then call the run()
	 * function to execute the search and get an array of the images found.  
	 *
	 * @param array p_request
	 *		This array may contain the following values:
	 *		"order_by" => ["description"|"photographer"|"place"|"date"|
	 *					   "inuse"|"id"|"time_create"|"last_modified"]
	 *			Which column to order the results by.
	 *		"order_direction" => ["ASC"|"DESC"]
	 *			Order by increasing or decreasing values.
	 *		"image_offset" => int
	 *			Only return results starting from the given offset.
	 *		"search_description" => string
	 *			The description to search for.
	 *		"search_photographer" => string
	 *			The photographer to search for.
	 *		"search_place" => string
	 *			The place to search for.
	 *		"search_date" => string
	 *			The date to search for.
	 *		"search_inuse" => boolean
	 *			Search to see if the image is in use.
	 *
	 */
	function ImageSearch($p_imagesPerPage = 0) 
	{
		global $Campsite;
		$this->m_orderBy = Input::Get('order_by', 'string', 'id', true);
		$this->m_orderDirection = Input::Get('order_direction', 'string', 'ASC', true);
		$this->m_imageOffset = Input::Get('image_offset', 'int', 0, true);		
		$this->m_searchDescription = Input::Get('search_description', 'string', '', true);
		$this->m_searchPhotographer = Input::Get('search_photographer', 'string', '', true);
		$this->m_searchPlace = Input::Get('search_place', 'string', '', true);
		$this->m_searchDate = Input::Get('search_date', 'string', '', true);
		$this->m_searchInUse = Input::Get('search_inuse', 'int', '', true);
		$this->m_searchUploadedBy = Input::Get('search_uploadedby', 'string', '', true);
		if ($p_imagesPerPage > 0) {
			$this->m_imagesPerPage = $p_imagesPerPage;
		}

		// "Search by" sql
		$this->m_whereQuery = '';
		if (!empty($this->m_searchDescription)) {
			$this->m_whereQuery .= " AND Images.Description LIKE '%$this->m_searchDescription%'";
		}
		if (!empty($this->m_searchPhotographer)) {
			$this->m_whereQuery .= " AND Images.Photographer LIKE '%$this->m_searchPhotographer%'";
		}
		if (!empty($this->m_searchPlace)) {
			$this->m_whereQuery .= " AND Images.Place LIKE '%$this->m_searchPlace%'";
		}
		if (!empty($this->m_searchDate)) {
			$this->m_whereQuery .= " AND Images.Date LIKE '%$this->m_searchDate%'";
		}
		if (!empty($this->m_searchInUse)) {
			if ($this->m_searchInUse) {
	            $not = 'NOT';
	        }
	        $this->m_whereQuery .= " AND ArticleImages.IdImage IS $not NULL";
		}
		if ($this->m_searchUploadedBy) {
			$this->m_whereQuery .= " AND Images.UploadedByUser=".$this->m_searchUploadedBy;
		}
		
		// "Order by" sql
		switch ($this->m_orderBy) {
		case 'description':
			$this->m_orderQuery .= 'ORDER BY Images.Description ';
			break;
		case 'photographer':
			$this->m_orderQuery = 'ORDER BY Images.Photographer ';
			break;
		case 'place':
			$this->m_orderQuery = 'ORDER BY Images.Place ';
			break;
		case 'date':
			$this->m_orderQuery = 'ORDER BY Images.Date ';
			break;
		case 'inuse':
			$this->m_orderQuery = 'ORDER BY inUse ';
			break;
		case 'time_created':
			$this->m_orderQuery = 'ORDER BY TimeCreated ';
			$this->m_orderDirection = 'DESC';
			break;
		case 'last_modified':
			$this->m_orderQuery = 'ORDER BY LastModified ';
			$this->m_orderDirection = 'DESC';
			break;
		case 'id':
		default:
			$this->m_orderQuery = 'ORDER BY Images.Id ';
			break;
		}
		if (!empty($this->m_orderQuery)) {
			$this->m_orderQuery .= ' '.$this->m_orderDirection;
		}
	} // constructor
	
	
	/**
	 * Execute the search and return the results.
	 *
	 * @return array
	 *		An array of Image objects.
	 */
	function run() 
	{
		global $Campsite;
		$tmpImage =& new Image();
		$columnNames = $tmpImage->getColumnNames(true);
		$columnNames = implode(',', $columnNames);
		$queryStr = 'SELECT '.$columnNames.', COUNT(ArticleImages.IdImage) AS inUse'
				  	.' FROM Images '
				  	.' LEFT JOIN ArticleImages On Images.Id=ArticleImages.IdImage'
				  	." WHERE 1 $this->m_whereQuery"
				    .' GROUP BY Images.Id'
				    ." $this->m_orderQuery LIMIT $this->m_imageOffset, ".$this->m_imagesPerPage;
				    
		$numImagesFoundQueryStr = 'SELECT COUNT(DISTINCT(Images.Id))'
				  	.' FROM Images '
				  	.' LEFT JOIN ArticleImages On Images.Id=ArticleImages.IdImage'
				  	." WHERE 1 $this->m_whereQuery";
		$rows = $Campsite['db']->GetAll($queryStr);
		$this->m_numImagesFound = $Campsite['db']->GetOne($numImagesFoundQueryStr);

		$this->m_imageData = array();
		if (is_array($rows)) {
			// Get "In Use" information
			$imageIds = array();
			foreach ($rows as $row) {
				$imageIds[$row['Id']] = '(IdImage='.$row['Id'].')';
			}
			$inUseQuery = "SELECT ArticleImages.IdImage, COUNT(Articles.Number) as in_use "
							." FROM Articles, ArticleImages "
							." WHERE (Articles.Number=ArticleImages.NrArticle) " 
							." AND (".implode(' OR ', $imageIds).")"
							." GROUP By ArticleImages.IdImage";
			$tmpInUseArray = $Campsite['db']->GetAll($inUseQuery);
			$inUseArray = array();
			// Make it an associative array for easy lookup in the next loop.
			if (is_array($tmpInUseArray)) {
				foreach ($tmpInUseArray as $inUseItem) {
					$inUseArray[$inUseItem['IdImage']] = $inUseItem['in_use'];
				}
			}
			// Create image templates
			foreach ($rows as $row) {
				$tmpImage =& new Image();
				$tmpImage->fetch($row);
				$template = $tmpImage->toTemplate();
				$template['in_use'] = 0;
				if (isset($inUseArray[$row['Id']])) {
					$template['in_use'] = $inUseArray[$row['Id']];
				}
				$this->m_imageData[] = $template;
			}
		}
		return $this->m_imageData;
	} // fn run
	
	
	/**
	 * Return the images that were found.
	 * @return array
	 */
	function getImages() 
	{
		return $this->m_imageData;
	} // fn getImages
	
	
	/**
	 * Return the total number of images that match the search.
	 * Note that this may be different than the total number of 
	 * images returned by run() or getImages() because that array
	 * is limited to the set "images per page".  The number returned
	 * by this function is the total number of images without that 
	 * restriction.
	 *
	 * @return int
	 */
	function getNumImagesFound() 
	{
		return $this->m_numImagesFound;
	} // fn getNumImagesFound
	
	
	/**
	 * The current value for the number of images shown per page.
	 * @return int
	 */
	function getImagesPerPage() 
	{
		return $this->m_imagesPerPage;
	} // fn getImagesPerPage
	
	
	/**
	 * Set the max number of images to return from the run() function.
	 *
	 * @param int p_value
	 *
	 * @return void
	 */
	function setImagesPerPage($p_value) 
	{
		$this->m_imagesPerPage = $p_value;
	} // fn setImagesPerPage
	
} // class ImageSearch


class ImageNav {
	var $m_staticSearchLink = '';
	var $m_keywordSearchLink = '';
	var $m_previousLink = '';
	var $m_nextLink = '';
	var $m_orderByLink = '';
	var $m_imagesPerPage = 10;
	var $m_view = 'thumbnail';
	var $m_input = array();
	var $m_searchStrings = array("search_description", 
								 "search_photographer",
								 "search_place",
							   	 "search_date",
							   	 "search_inuse",
							   	 "search_uploadedby");
	
	/**
	 * This class is used to create links, which will be parsed by the ImageSearch class.
	 * @param int p_imagesPerPage
	 *		Number of images to show on a page.
	 * @param string p_view
	 *		Can be: ["thumbnail"|"gallery"|"flat"].  The type of display.
	 */
	function ImageNav($p_imagesPerPage = 0, $p_view = "thumbnail") 
	{
		if ($p_imagesPerPage > 0) {
			$this->m_imagesPerPage = $p_imagesPerPage;
		}
		$this->m_view = $p_view;
		foreach ($this->m_searchStrings as $key) {
			$tmp = Input::Get($key, 'string', '', true);
			if ($tmp != '') {
				$this->m_input[$key] = $tmp;
			}
		}
		$this->m_input['order_by'] = Input::Get('order_by', 'string', '', true);
		$this->m_input['order_direction'] = Input::Get('order_direction', 'string', '', true);
		$this->m_input['image_offset'] = Input::Get('image_offset', 'int', 0, true);
		$this->__buildLinks();		
	} // constructor

	
	/**
	 * Change a property of the links and rebuild the links.
	 * @param string p_name
	 * @param string p_value
	 * @return void
	 */
	function setProperty($p_name, $p_value) 
	{
		$this->m_input[$p_name] = $p_value;
		$this->__buildLinks();	
	} // fn setProperty
	
	
	function clearSearchStrings() 
	{
		foreach ($this->m_searchStrings as $searchString) {
			$this->m_input[$searchString] = '';
		}
		$this->__buildLinks();
	} // fn clearSearchStrings
	
	
	/**
	 * Build the links based on the input.
	 * @return void
	 */
	function __buildLinks() 
	{
		$this->m_staticSearchLink = '';
		$this->m_keywordSearchLink = '';
		$this->m_orderByLink = '';
		$this->m_previousLink = '';
		$this->m_nextLink = '';		
    	$keywordSearch = false;
    	foreach ($this->m_input as $fieldName => $keyword) {
    		if (!empty($this->m_input[$fieldName]) && in_array($fieldName, $this->m_searchStrings))  {
    			$this->m_keywordSearchLink .= '&'.$fieldName.'='.urlencode($keyword);
    			$keywordSearch = true;
    		}
    	}

		// Build the order statement.
		if (!empty($this->m_input['order_by'])) {
			$this->m_orderByLink .= '&order_by='.$this->m_input['order_by'];
		}
		if (!empty($this->m_input['order_direction'])) {
			$this->m_orderByLink .= '&order_direction='.$this->m_input['order_direction'];
		}
		if ($this->m_input['image_offset'] < 0) {
			$this->m_input['image_offset'] = 0;
		}
		
		// Prev/Next switch
		$this->m_previousLink = 'image_offset='.($this->m_input['image_offset'] - $this->m_imagesPerPage) 
			.$this->m_keywordSearchLink.$this->m_orderByLink.'&view='.$this->m_view;
		$this->m_nextLink = 'image_offset='.($this->m_input['image_offset'] + $this->m_imagesPerPage)
			.$this->m_keywordSearchLink.$this->m_orderByLink.'&view='.$this->m_view;
		$this->m_keywordSearchLink .= '&image_offset='.$this->m_input['image_offset'].'&view='.$this->m_view;
		$this->m_staticSearchLink = $this->m_keywordSearchLink . $this->m_orderByLink;		
	} // fn __buildLinks
	
	
	/**
	 * @return string
	 */
	function getKeywordSearchLink() 
	{
		return $this->m_keywordSearchLink;
	} // fn getKeywordSearchLink
	
	
	/**
	 * @return string
	 */
	function getPreviousLink() 
	{
		return $this->m_previousLink;
	} // fn getPreviousLink
	
	
	/**
	 * @return string
	 */
	function getNextLink() 
	{
		return $this->m_nextLink;
	} // fn getNextLink
	
	
	/**
	 * Produces a link to reproduce the same search.
	 * @return string
	 */
	function getSearchLink() 
	{
		return $this->m_staticSearchLink;
	} // fn getSearchLink
		
} // class ImageNav

?>