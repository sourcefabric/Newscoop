<?php
define('CAMPSITE_IMAGEARCHIVE_DIR', '/priv/imagearchive/');
$ImagesPerPage = 5;

function orE($p_input) {
	if (empty($p_input)) {
		return 'unknown';
	} else {
		return $p_input;
	}
} // fn orE


function array_get_value($p_array, $p_index, $p_defaultValue = null) {
	if (isset($p_array[$p_index])) {
		return $p_array[$p_index];
	}
	else {
		return $p_defaultValue;
	}
} // fn array_get_value


class ImageSearch {
	var $m_isSearch;
	var $m_orderBy;
	var $m_orderDirection;
	var $m_imageOffset;
	var $m_imagesPerPage;
	var $m_searchDescription;
	var $m_searchPhotographer;
	var $m_searchDate;
	var $m_searchPlace;
	var $m_searchInUse;
	var $m_imageData;
	var $m_numImagesFound;
	var $m_orderQuery;
	var $m_whereQuery;
		
	function ImageSearch($p_request) {
		global $Campsite;
		global $ImagesPerPage;
		$this->m_imagesPerPage = $ImagesPerPage;
		$this->m_orderBy = array_get_value($p_request, 'order_by', 'id');
		$this->m_orderDirection = array_get_value($p_request, 'order_direction', 'ASC');
		$this->m_imageOffset = array_get_value($p_request, 'image_offset', 0);		
		$this->m_searchDescription = array_get_value($p_request, 'search_description', '');
		$this->m_searchPhotographer = array_get_value($p_request, 'search_photographer', '');
		$this->m_searchPlace = array_get_value($p_request, 'search_place', '');
		$this->m_searchDate = array_get_value($p_request, 'search_date', '');
		$this->m_searchInUse = array_get_value($p_request, 'search_inuse', '');
		
		$this->m_whereQuery = '';
		if ($this->m_searchDescription || $this->m_searchPhotographer 
			|| $this->m_searchPlace || $this->m_searchDate || $this->m_searchInUse) {
			if ($this->m_searchDescription) {
				$this->m_whereQuery .= " AND i.Description LIKE '%$this->m_searchDescription%'";
			}
			if ($this->m_searchPhotographer) {
				$this->m_whereQuery .= " AND i.Photographer LIKE '%$this->m_searchPhotographer%'";
			}
			if ($this->m_searchPlace) {
				$this->m_whereQuery .= " AND i.Place LIKE '%$this->m_searchPlace%'";
			}
			if ($this->m_searchDate) {
				$this->m_whereQuery .= " AND i.Date LIKE '%$this->m_searchDate%'";
			}
			if ($this->m_searchInUse) {
				if ($this->m_searchInUse) {
		            $not = 'NOT';
		        }
		        $this->m_whereQuery .= " AND a.IdImage IS $not NULL";
			}
		}
		switch ($this->m_orderBy) {
		case 'description':
			$this->m_orderQuery .= 'ORDER BY i.Description ';
			break;
		case 'photographer':
			$this->m_orderQuery = 'ORDER BY i.Photographer ';
			break;
		case 'place':
			$this->m_orderQuery = 'ORDER BY i.Place ';
			break;
		case 'date':
			$this->m_orderQuery = 'ORDER BY i.Date ';
			break;
		case 'inuse':
			$this->m_orderQuery = 'ORDER BY inUse ';
			break;
		case 'id':
		default:
			$this->m_orderQuery = 'ORDER BY i.Id ';
			break;
		}
		if (!empty($this->m_orderQuery)) {
			$this->m_orderQuery .= ' '.$this->m_orderDirection;
		}
	} // constructor
	
	function run() {
		global $Campsite;
		$tmpImage =& new Image();
		foreach ($tmpImage->getColumnNames() as $columnName) {
			$columnNames[] = 'i.'.$columnName;
		}
		$columnNames = implode(',', $columnNames);
		$queryStr = 'SELECT '.$columnNames.', COUNT(a.IdImage) AS inUse'
				  	.' FROM Images AS i'
				  	.' LEFT JOIN ArticleImages AS a On i.Id=a.IdImage'
				  	." WHERE 1 $this->m_whereQuery"
				    .' GROUP BY i.Id'
				    ." $this->m_orderQuery LIMIT $this->m_imageOffset, ".$this->m_imagesPerPage;
				    
		$numImagesFoundQueryStr = 'SELECT COUNT(i.Id)'
				  	.' FROM Images as i'
				  	.' LEFT JOIN ArticleImages AS a On i.Id=a.IdImage'
				  	." WHERE 1 $this->m_whereQuery";
		$query = $Campsite['db']->Execute($queryStr);
		$this->m_numImagesFound = $Campsite['db']->GetOne($numImagesFoundQueryStr);
		
		// Create image templates
		$this->m_imageData = array();
		while ($row = $query->FetchRow()) {
			$tmpImage =& new Image();
			$tmpImage->fetch($row);
			$template = $tmpImage->toTemplate();
			$template['in_use'] = $row['inUse'];
			$this->m_imageData[] = $template;
		}	
	} // fn run
	
	function getImages() {
		return $this->m_imageData;
	}
	
	function getNumImagesFound() {
		return $this->m_numImagesFound;
	}
	
	function getImagesPerPage() {
		return $this->m_imagesPerPage;
	}
	
	function setImagesPerPage($p_value) {
		$this->m_imagesPerPage = $p_value;
	}
	
} // class ImageSearch


function Image_GetSearchUrl($p_request) {
	$input = array(
		'order_by' => array_get_value($p_request, 'order_by', 'id'),
		'order_direction' => array_get_value($p_request, 'order_direction', 'ASC'),
		'view' => array_get_value($p_request, 'view', 'thumbnail'),
		'image_offset' => array_get_value($p_request, 'image_offset', 0),
		'search_description' => array_get_value($p_request, 'search_description', null),
		'search_photographer' => array_get_value($p_request, 'search_photographer', null),
		'search_place' => array_get_value($p_request, 'search_place', null),
		'search_date' => array_get_value($p_request, 'search_date', null),
		'search_inuse' => array_get_value($p_request, 'search_inuse', null),
		);
	$url = array();
	foreach ($input as $varName => $value) {
		if (!is_null($value) && !empty($value)) {
			$url[] = $varName.'='.urlencode($value);
		}
	}	
	return implode('&', $url);
} // fn Image_GetSearchUrl


function CreateImageLinks($p_searchKeywords = null, $p_orderBy = null, $p_orderDirection = null, $p_imageOffset = -1, $p_imagesPerPage = 20, $p_view = "thumbnail")
{
	$Link = array();
	$Link['search'] = '';
	$Link['order_by'] = '';
	$Link['previous'] = '';
	$Link['next'] = '';
	
    if (!is_null($p_searchKeywords)) {
    	$keywordSearch = false;
    	foreach ($p_searchKeywords as $fieldName => $keyword) {
    		if (!is_null($keyword)) {
    			$Link['search'] .= '&'.$fieldName.'='.urlencode($keyword);
    			$keywordSearch = true;
    		}
    	}
    }

	// build the order statement ///////////////////////////////////////
	if (!is_null($p_orderBy)) {
		$Link['order_by'] = '&order_by='.$p_orderBy.'&order_direction='.$p_orderDirection;
	}
	if ($p_imageOffset < 0) {
		$p_imageOffset = 0;
	}

	// Prev/Next switch
	$Link['previous'] = 'image_offset='.($p_imageOffset - $p_imagesPerPage).$Link['search'].$Link['order_by'].'&view='.$p_view;
	$Link['next'] = 'image_offset='.($p_imageOffset + $p_imagesPerPage).$Link['search'].$Link['order_by'].'&view='.$p_view;

	$Link['search'] .= '&image_offset='.$p_imageOffset.'&view='.$p_view;
	return $Link;
} // fn cImgLink

//function handleRemoteImg ($cDescription, $cPhotographer, $cPlace, $cDate, $cURL, $Id=0)
//{
//    include_once('Yahc.class.php');
//    $data = new Yahc($cURL, 'CAMPWARE');
//    $data->request_protocol = 'HTTP/1.0';
//    $data->request_method = 'GET';
//    if ($data->connect()) {
//        // URL OK
//        #echo "connect<br>";
//        $data->send_request();
//        $data->get_response();
//            $hrows = explode ("\r\n", $data->response_HEADER);
//        foreach ($hrows as $row) {
//            if (preg_match('/Content-Type:/', $row)) {
//                $ctype = trim(substr($row, strlen('Content-Type:')));
//            }
//        }
//        #echo "ctype $ctype";
//
//        if (preg_match('/image/', $ctype)) {
//            // content-type = image
//            if ($Id) {
//                $query = "UPDATE Images
//                          SET Description='$cDescription', Photographer='$cPhotographer', Place='$cPlace', Date='$cDate', ContentType='$ctype', Location='remote', URL='$cURL'
//                          WHERE Id=$Id
//                          LIMIT 1";
//                query($query);    
//                $currId = $Id;
//            } else {
//                $query = "INSERT INTO Images
//                          (Description, Photographer, Place, Date, ContentType, Location, URL)
//                           VALUES
//                          ('$cDescription', '$cPhotographer', '$cPlace', '$cDate', '$ctype', 'remote', '$cURL')";
//                query($query);
//                $currId = mysql_insert_id();
//            }
//
//            if (_IMAGEMAGICK_) {
//                $tmpname =_TMP_DIR_.'img'.md5(rand());
//                if ($tmphandle = fopen($tmpname, 'w')) {
//                    fwrite($tmphandle, $data->response_HTML);
//                    fclose($tmphandle);
//                    $cmd = _TUMB_CMD_.' '.$tmpname.' '.$_SERVER[DOCUMENT_ROOT]._TUMB_PREFIX_.$currId;
//                    system($cmd);
//                    unlink($tmpname);
//                } else {
//                    return getGS('Cannot create <B>$1</B>', $tmpname);
//                }
//            }
//        } else {
//            // wrong URL
//            return getGS('URL <B>$1</B> have wrong content type <B>$2</B>', $cURL, $ctype);
//        }
//    } else {
//        // no connection
//        return getGS('Unable to read image from <B>$1</B>', $cURL);
//    }
//}
//
//function handleLocalImage($cImageTemp, $cDescription, $cPhotographer, $cPlace, $cDate, $cURL, $Id=0)
//{
// 	if ($Id) {
//        $query = "UPDATE Images
//                  SET Description='$cDescription', Photographer='$cPhotographer', Place='$cPlace', Date='$cDate', ContentType='$ctype', Location='local', URL=''
//                  WHERE Id=$Id
//                  LIMIT 1";
//        query($query); 
//        $currId = $Id;
//    } else {
//        $query = "INSERT INTO Images
//                  (Description, Photographer, Place, Date, ContentType, Location)
//                  VALUES
//                  ('$cDescription', '$cPhotographer', '$cPlace', '$cDate', '$cImageType', 'local')";
//        query($query);
//        $currId = mysql_insert_id();
//    }
//
//    $target = $_SERVER[DOCUMENT_ROOT]._IMG_PREFIX_.$currId;
//    $tumb   = $_SERVER[DOCUMENT_ROOT]._TUMB_PREFIX_.$currId;
//
//    if (!$Id) {
//        if (!move_uploaded_file ($cImageTemp, $target)) {
//             return getGS('Unable to move Image to <B>$1</B>', $target);
//        }
//
//        if (_IMAGEMAGICK_) {
//            $cmd = _TUMB_CMD_.' '.$target.' '.$tumb;
//            #echo $cmd;
//            system($cmd);
//        }
//    }
//}
?>