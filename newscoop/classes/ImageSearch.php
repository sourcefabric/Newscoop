<?php

/**
 * @package Campsite
 */
class ImageSearch {
    var $m_isSearch;
    var $m_orderBy;
    var $m_orderDirection;
    var $m_imageOffset;
    var $m_searchString;
    var $m_imageData;
    var $m_numImagesFound;
    var $m_orderQuery;
    var $m_whereQuery;
    var $m_itemsPerPage = 10;
    var $m_whereGroups = array();

    /**
     * This class can search for images matching specific criteria.
     * Give the search criteria in the contructor, then call the run()
     * function to execute the search and get an array of the images found.
     *
     * @param string $p_searchString
     *          The string to search for.
     *
     * @param string $p_orderBy
     *      Which column to order the results by.
     *      Can be one of ["description"|"photographer"|"place"|"date"|
     *                     "inuse"|"id"|"time_create"|"last_modified"]
     *
     * @param string $p_orderDirection
     *      Order by increasing or decreasing values.
     *      Can be  ["ASC"|"DESC"]
     *
     * @param int $p_offset
     *      Return results starting from the given offset.
     *
     * @param int $p_itemsPerPage
     *      The number of results to return.
     *
     */
    public function ImageSearch($p_searchString, $p_orderBy,
                                $p_orderDirection = 'ASC', $p_offset = 0,
                                $p_itemsPerPage = 0)
    {
        $this->m_orderBy = $p_orderBy;
        $this->m_orderDirection = $p_orderDirection;
        $this->m_imageOffset = $p_offset;
        $this->m_searchString = $p_searchString;
        if ($p_itemsPerPage > 0) {
            $this->m_itemsPerPage = $p_itemsPerPage;
        }

        $whereGroups = array();

        // "Search by" sql
        $this->m_whereQuery = '';
        if (!empty($this->m_searchString)) {
            $this->m_whereGroups[] = '('.implode( array
            (
                "Images.Description LIKE '%$this->m_searchString%'"
            ,   "Images.Photographer LIKE '%$this->m_searchString%'"
            ,   "Images.Place LIKE '%$this->m_searchString%'"
            ,   "Images.Date LIKE '%$this->m_searchString%'"
            ,   "Images.UploadedByUser LIKE '%$this->m_searchString%'"
            ), " OR " ).')';
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
            // Reverse the order direction because "last modified ASC"
            // means that the last modified article should be on top.
            // Since the last_modified field is a date, it has the
            // opposite meaning when you are sorting.
            if ($p_orderDirection == 'ASC') {
                $this->m_orderDirection = 'DESC';
            } else {
                $this->m_orderDirection = 'ASC';
            }
            break;
        case 'last_modified':
            $this->m_orderQuery = 'ORDER BY LastModified ';
            // Reverse the order direction because "last modified ASC"
            // means that the last modified article should be on top.
            // Since the last_modified field is a date, it has the
            // opposite meaning when you are sorting.
            if ($p_orderDirection == 'ASC') {
                $this->m_orderDirection = 'DESC';
            } else {
                $this->m_orderDirection = 'ASC';
            }
            break;
        case 'id':
        default:
            $this->m_orderQuery = 'ORDER BY Images.Id ';
            if ($p_orderDirection == 'ASC') {
                $this->m_orderDirection = 'DESC';
            } else {
                $this->m_orderDirection = 'ASC';
            }
            break;
        }
        if (!empty($this->m_orderQuery)) {
            $this->m_orderQuery .= ' '.$this->m_orderDirection;
        }
    } // constructor


    public function setFilter($col, $val, $out=false)
    {
        $this->m_whereGroups[] = $col.( $out ? " <> " : " = " )."'$val'";
    }

    /**
     * Execute the search and return the results.
     *
     * @return array
     *      An array of Image objects.
     */
    public function run()
    {
        global $g_ado_db;
        $tmpImage = new Image();
        $columnNames = $tmpImage->getColumnNames(true);
        $columnNames = implode(',', $columnNames);

        $this->m_whereQuery = count($this->m_whereGroups) ? ' WHERE '.implode($this->m_whereGroups, ' AND ') : null;

        $queryStr = 'SELECT '.$columnNames.', COUNT(ArticleImages.IdImage) AS inUse'
                    .' FROM Images '
                    .' LEFT JOIN ArticleImages On Images.Id=ArticleImages.IdImage'
                    .' '.$this->m_whereQuery
                    .' GROUP BY Images.Id'
                    ." $this->m_orderQuery LIMIT $this->m_imageOffset, ".$this->m_itemsPerPage;
        $numImagesFoundQueryStr = 'SELECT COUNT(DISTINCT(Images.Id))'
                    .' FROM Images '
                    .' LEFT JOIN ArticleImages On Images.Id=ArticleImages.IdImage'
                    ." $this->m_whereQuery";
        $rows = $g_ado_db->GetAll($queryStr);
        $this->m_numImagesFound = $g_ado_db->GetOne($numImagesFoundQueryStr);

        $this->m_imageData = array();
        if (is_array($rows)) {
            // Get "In Use" information
            $imageIds = array();
            foreach ($rows as $row) {
                $imageIds[$row['Id']] = '(IdImage='.$row['Id'].')';
            }

            if (count($imageIds) > 0) {
                $imagesIdsQuery = " AND (".implode(' OR ', $imageIds).")";
            } else {
                $imagesIdsQuery = "";
            } 

            $inUseQuery = "SELECT ArticleImages.IdImage, COUNT(Articles.Number) as in_use "
                            ." FROM Articles, ArticleImages "
                            ." WHERE (Articles.Number=ArticleImages.NrArticle) "
                            . $imagesIdsQuery
                            ." GROUP By ArticleImages.IdImage";
            $tmpInUseArray = $g_ado_db->GetAll($inUseQuery);
            $inUseArray = array();
            // Make it an associative array for easy lookup in the next loop.
            if (is_array($tmpInUseArray)) {
                foreach ($tmpInUseArray as $inUseItem) {
                    $inUseArray[$inUseItem['IdImage']] = $inUseItem['in_use'];
                }
            }
            // Create image templates
            foreach ($rows as $row) {
                $tmpImage = new Image();
                $tmpImage->fetch($row);
                $template = $tmpImage->toTemplate();
                $template['in_use'] = 0;
                if (isset($inUseArray[$row['Id']])) {
                    $template['in_use'] = $inUseArray[$row['Id']];
                }

                $imageSize = @getimagesize($tmpImage->getImageStorageLocation()) ? : array(0, 0);
                $template['width'] = $imageSize[0];
                $template['height'] = $imageSize[1];
                $this->m_imageData[] = $template;
            }
        }
        return $this->m_imageData;
    } // fn run


    /**
     * Return the images that were found.
     * @return array
     */
    public function getImages()
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
    public function getNumImagesFound()
    {
        return $this->m_numImagesFound;
    } // fn getNumImagesFound


    /**
     * The current value for the number of images shown per page.
     * @return int
     */
    public function getImagesPerPage()
    {
        return $this->m_itemsPerPage;
    } // fn getImagesPerPage


    /**
     * Set the max number of images to return from the run() function.
     *
     * @param int $p_value
     *
     * @return void
     */
    public function setImagesPerPage($p_value)
    {
        $this->m_itemsPerPage = $p_value;
    } // fn setImagesPerPage

} // class ImageSearch

?>