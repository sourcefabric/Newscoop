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
	 *		"order_by" => ["description"|"photographer"|"place"|"date"|"inuse"|"id"]
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
	function ImageSearch($p_request, $p_imagesPerPage = 0) {
		global $Campsite;
		$this->m_orderBy = array_get_value($p_request, 'order_by', 'id');
		$this->m_orderDirection = array_get_value($p_request, 'order_direction', 'ASC');
		$this->m_imageOffset = array_get_value($p_request, 'image_offset', 0);		
		$this->m_searchDescription = array_get_value($p_request, 'search_description', '');
		$this->m_searchPhotographer = array_get_value($p_request, 'search_photographer', '');
		$this->m_searchPlace = array_get_value($p_request, 'search_place', '');
		$this->m_searchDate = array_get_value($p_request, 'search_date', '');
		$this->m_searchInUse = array_get_value($p_request, 'search_inuse', '');
		$this->m_searchUploadedBy = array_get_value($p_request, 'search_uploadedby', '');
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
	function run() {
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
	function getImages() {
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
	function getNumImagesFound() {
		return $this->m_numImagesFound;
	} // fn getNumImagesFound
	
	
	/**
	 * The current value for the number of images shown per page.
	 * @return int
	 */
	function getImagesPerPage() {
		return $this->m_imagesPerPage;
	} // fn getImagesPerPage
	
	
	/**
	 * Set the max number of images to return from the run() function.
	 *
	 * @param int p_value
	 *
	 * @return void
	 */
	function setImagesPerPage($p_value) {
		$this->m_imagesPerPage = $p_value;
	} // fn setImagesPerPage
	
} // class ImageSearch


class ImageNav {
	var $m_staticSearchLink;
	var $m_keywordSearchLink;
	var $m_previousLink;
	var $m_nextLink;
	var $m_orderByLink;
	var $m_imagesPerPage = 10;

	function ImageNav($p_request, $p_imagesPerPage = 0, $p_view = "thumbnail") {
		$this->m_staticSearchLink = '';
		$this->m_keywordSearchLink = '';
		$this->m_orderByLink = '';
		$this->m_previousLink = '';
		$this->m_nextLink = '';
		if ($p_imagesPerPage > 0) {
			$this->m_imagesPerPage = $p_imagesPerPage;
		}
		
		$searchStrings = array("search_description", 
							   "search_photographer",
							   "search_place",
							   "search_date",
							   "search_inuse",
							   "search_uploadedby");
    	$keywordSearch = false;
    	foreach ($p_request as $fieldName => $keyword) {
    		if (in_array($fieldName, $searchStrings))  {
    			$this->m_keywordSearchLink .= '&'.$fieldName.'='.urlencode($keyword);
    			$keywordSearch = true;
    		}
    	}
	
		// build the order statement ///////////////////////////////////////
		if (isset($p_request['order_by'])) {
			$this->m_orderByLink .= '&order_by='.$p_request['order_by'];
		}
		if (isset($p_request['order_direction'])) {
			$this->m_orderByLink .= '&order_direction='.$p_request['order_direction'];
		}
		if (!isset($p_request['image_offset']) || ($p_request['image_offset'] < 0)) {
			$imageOffset = 0;
		}
		else {
			$imageOffset = $p_request['image_offset'];
		}
		
		// Prev/Next switch
		$this->m_previousLink = 'image_offset='.($imageOffset - $this->m_imagesPerPage) 
			.$this->m_keywordSearchLink.$this->m_orderByLink.'&view='.$p_view;
		$this->m_nextLink = 'image_offset='.($imageOffset + $this->m_imagesPerPage)
			.$this->m_keywordSearchLink.$this->m_orderByLink.'&view='.$p_view;
	
		$this->m_keywordSearchLink .= '&image_offset='.$imageOffset.'&view='.$p_view;
		$this->m_staticSearchLink = $this->m_keywordSearchLink . $this->m_orderByLink;
	} // constructor

	
	/**
	 * @return string
	 */
	function getKeywordSearchLink() {
		return $this->m_keywordSearchLink;
	}
	
	
	/**
	 * @return string
	 */
	function getPreviousLink() {
		return $this->m_previousLink;
	}
	
	
	/**
	 * @return string
	 */
	function getNextLink() {
		return $this->m_nextLink;
	}
	
	
	/**
	 * Produces a link to reproduce the same search.
	 * @return string
	 */
	function getSearchLink() {
		return $this->m_staticSearchLink;
	}
		
} // class ImageNav



?>