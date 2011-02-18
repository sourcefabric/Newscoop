<?php
/**
 * @package Campsite
 */

require_once dirname(__FILE__) . '/DatabaseObject.php';
require_once dirname(__FILE__) . '/GeoLocation.php';
require_once dirname(__FILE__) . '/GeoMultimedia.php';
require_once dirname(__FILE__) . '/GeoMapLocationContent.php';
require_once dirname(__FILE__) . '/GeoMapLocationLanguage.php';
require_once dirname(__FILE__) . '/IGeoMap.php';
require_once dirname(__FILE__) . '/IGeoMapLocation.php';

require_once($GLOBALS['g_campsiteDir'].'/classes/CampCacheList.php');

/**
 */
class Geo_MapLocation extends DatabaseObject implements IGeoMapLocation
{
    const TABLE = 'MapLocations';

    /** @var string */
	public $m_dbTableName = self::TABLE;

    /** @var array */
	public $m_keyColumnNames = array('id');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'fk_map_id',
        'fk_location_id',
        'poi_style',
        'rank',
    );

    /** @var IGeoLocation */
    private $location = NULL;

    /** @var array of IGeoMapLocationContent */
    private $contents = array();

    /** @var array of IGeoMultimedia */
    private $multimedia = NULL;

    /** @var IGeoMapLocationLanguage */
    private $languages = array();

	/**
     * @param mixed $arg
	 */
	public function __construct($arg = NULL, $p_forceExists = false)
	{

        parent::__construct($this->m_columnNames);

        if (is_array($arg)) {
            //echo "has array";
        	$this->fetch($arg, $p_forceExists);
        } else if (is_numeric($arg)) {
            $this->m_data['id'] = (int) $arg;
            $this->fetch();
        }

	}

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * Get latitude
     * @return float
     */
    public function getLatitude()
    {
        return $this->getLocation()->getLatitude();
    }

    /**
     * Get longitude
     * @return float
     */
    public function getLongitude()
    {
        return $this->getLocation()->getLongitude();
    }

    /**
     * Get content
     * @param int $language
     * @return IGeoMapLocationLanguage
     */
    public function getLanguage($language)
    {
        $language = (int) $language;
        if (!isset($this->languages[$language])) {
            $this->languages[$language] = new Geo_MapLocationLanguage($this, $language);
        }
        return $this->languages[$language];
    }

    public function setLanguage($p_languageId, IGeoMapLocationLanguage $p_languageObj)
    {
        if ((!$p_languageId) || (!$p_languageObj)) {return;}

        $language = (int) $p_languageId;

        $this->languages[$language] = $p_languageObj;
    }


    /**
     * Get content
     * @param int $language
     * @return IGeoMapLocationContent
     */
    public function getContent($language)
    {
        $language = (int) $language;
        if (!isset($this->contents[$language])) {
            $this->contents[$language] = new Geo_MapLocationContent($this, $this->getLanguage($language));
        }
        return $this->contents[$language];
    }

    public function setContent($p_languageId, IGeoMapLocationContent $p_contentObj)
    {
        if ((!$p_languageId) || (!$p_contentObj)) {return;}

        $language = (int) $p_languageId;

        $this->contents[$language] = $p_contentObj;
    }


    /**
     * Get location
     * @return IGeoLocation
     */
    private function getLocation()
    {
        if ($this->location === NULL) {
            echo "--- f*ck ---";
            $this->location = new Geo_Location($this->m_data['fk_location_id']);
        }
        return $this->location;
    }

    /**
     * Get multimedia
     * @return array of IGeoMultimedia
     */
    public function getMultimedia()
    {
        if ($this->multimedia === NULL) {
            $this->multimedia = Geo_Multimedia::GetByMapLocation($this);
        }
        return $this->multimedia;
    }

    /**
     * Point is displayable?
     * @return bool
     */
    public function isEnabled($language)
    {
        $language = (int) $language;

        return $this->getLanguage($language)->isEnabled();
    }

    /**
     * Get locations by map
     * @param IGeoMap
     * @return array of IGeoMapLocation
     */
    public static function GetByMap(IGeoMap $map)
    {
        global $g_ado_db;

        $queryStr = 'SELECT ml.*, l.*, X(l.poi_location) as latitude, Y(l.poi_location) as longitude, ml.id as id
            FROM ' . self::TABLE . ' ml
                INNER JOIN ' . Geo_Location::TABLE . ' l
                    ON ml.fk_location_id = l.id
            WHERE ml.fk_map_id = ' . $map->getId() . '
            ORDER BY ml.rank, ml.id';
        $rows = $g_ado_db->GetAll($queryStr);

        $mapLocations = array();
        foreach ((array) $rows as $row) {
            $mapLocation = new self($row);
            $row['id'] = $row['fk_location_id'];
            $mapLocation->location = new Geo_Location($row);
            $mapLocations[] = $mapLocation;
        }
        return $mapLocations;
    }

    /**
     * Returns map locations list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparionOperation objects
     * @param string $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset. How many records from $p_start will be retrieved.
     *
     * @return array of IGeoMapLocation
     */
    public static function GetList(array $p_parameters, array $p_order = array(),
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
    {
        global $g_ado_db;

        $selectClauseObj = new SQLSelectClause();
        $countClauseObj = new SQLSelectClause();

        // set columns
        $tmpMapLoc = new self(NULL);
        $tmpLoc = new Geo_Location(NULL);
        $columnNames = array_merge($tmpMapLoc->getColumnNames(true),
            array_diff($tmpLoc->getColumnNames(true), array('Locations.id')));
        foreach ($columnNames as $columnName) {
            $selectClauseObj->addColumn($columnName);
        }
        $selectClauseObj->addColumn('X(poi_location) as latitude');
        $selectClauseObj->addColumn('Y(poi_location) as longitude');
        $countClauseObj->addColumn('COUNT(*)');

        // sets the base table
        $selectClauseObj->setTable($tmpMapLoc->getDbTableName());
        $selectClauseObj->addJoin(sprintf('INNER JOIN `%s` ON fk_location_id = %s.id',
            $tmpLoc->getDbTableName(),
            $tmpLoc->getDbTableName()));
        $countClauseObj->setTable($tmpMapLoc->getDbTableName());
        unset($tmpMapLoc);
        unset($tmpLoc);

        // process params
        foreach ($p_parameters as $param) {
            switch ($param->getLeftOperand()) {
                case 'article':
                    $searchQuery = sprintf('fk_map_id IN (SELECT id FROM %s WHERE fk_article_number = %d)',
                        Geo_Map::TABLE,
                        $param->getRightOperand());
                    $selectClauseObj->addWhere($searchQuery);
                    $countClauseObj->addWhere($searchQuery);
                    break;
            }
        }

        // set order by rank and id
        $selectClauseObj->addOrderBy(self::TABLE . '.rank');
        $selectClauseObj->addOrderBy(self::TABLE . '.id');

        // sets the limit
        $selectClauseObj->setLimit($p_start, $p_limit);

        // builds the query and executes it
        $selectQuery = $selectClauseObj->buildQuery();
        $rows = $g_ado_db->GetAll($selectQuery);

        $list = array();
        $p_count = 0;
        if (is_array($rows)) {
        	$countQuery = $countClauseObj->buildQuery();
        	$p_count = $g_ado_db->GetOne($countQuery);

        	foreach ($rows as $row) {
                $map_loc = new self((array) $row, true);

                $row['id'] = $row['fk_location_id'];
                $map_loc->location = new Geo_Location($row, true);

                $list[] = $map_loc;

        	}
        }

        return $list;
    }

    /**
     * Provides information on map's points
     *
     * @param int $p_mapId
     * @param int $p_languageId
     * @param bool $p_preview
     * @param bool $p_textOnly
     *
     * @return array
     */
	//public static function ReadMapPoints($p_mapId, $p_languageId, $p_preview = false, $p_textOnly = false, $p_mapCons = null)
	//public static function GetListExt($p_mapId, $p_languageId, $p_preview = false, $p_textOnly = false, $p_mapCons = null)
    public static function GetListExt(array $p_parameters, array $p_order = array(),
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false)
	{
        //echo "\n<br>\nxxxxx ---- \n<br>\n";
        //var_dump($p_parameters);

        $ps_asArray = true;
        //$ps_articleNumber = 0;
        $ps_mapId = 0;
        $ps_languageId = 0;
        $ps_preview = false;
        $ps_textOnly = false;

        $mc_mapCons = array();
        $mc_articles_yes = array();
        $mc_articles_no = array();
        $mc_issues = array();
        $mc_sections = array();
        $mc_topics = array();
        $mc_topics_matchall = false;
        $mc_multimedia = array();
        $mc_areas = array();
        $mc_dates = array();


//        if (null or true) {
        // process params
        foreach ($p_parameters as $param) {
            switch ($param->getLeftOperand()) {
                case 'as_array':
                    $ps_asArray = $param->getRightOperand();
                    break;
/*
                case 'article':
                    $ps_articleNumber = $param->getRightOperand();
                    //$searchQuery = sprintf('fk_map_id IN (SELECT id FROM %s WHERE fk_article_number = %d)',
                    //    Geo_Map::TABLE,
                    //    $param->getRightOperand());
                    //$selectClauseObj->addWhere($searchQuery);
                    //$countClauseObj->addWhere($searchQuery);
                    break;
*/
                case 'map':
                    $ps_mapId = $param->getRightOperand();
                    break;
                case 'language':
                    $ps_languageId = $param->getRightOperand();
                    break;
                case 'preview':
                    $ps_preview = $param->getRightOperand();
                    break;
                case 'text_only':
                    $ps_textOnly = $param->getRightOperand();
                    break;
                case 'article':
                    $one_article_value = $param->getRightOperand();
                    $one_article_type = $param->getOperator();
                    if ("is" == $one_article_type) {
                        $mc_articles_yes[] = $one_article_value;
                        $mc_mapCons = true;
                    }
                    if ("not" == $one_article_type) {
                        $mc_articles_no[] = $one_article_value;
                        $mc_mapCons = true;
                    }
                    break;
                case 'issue':
                    $mc_issues[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'section':
                    $mc_sections[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'topic':
                    $mc_topics[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'matchalltopics':
                    $mc_topics_matchall = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'matchanytopic':
                    $mc_topics_matchall = !$param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'multimedia':
                    $mc_multimedia[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'area':
                    $mc_areas[] = json_decode($param->getRightOperand());
                    $mc_mapCons = true;
                    break;
                case 'date':
                    $mc_dates[$param->getOperator()->getName()] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                default:
                    break;
            }
        }

//        }

        //$Context = CampTemplate::singleton()->context();
        //$Context = CampTemplate::singleton();
        //var_dump($Context);
        //$ps_languageId = -1;
        if ((!$ps_languageId) || (0 >= $ps_languageId)) {
            $Context = CampTemplate::singleton()->context();
            $ps_languageId = $Context->language->number;
            //var_dump($Context->language);
        }
        //var_dump($ps_languageId);

        if ((!$ps_languageId) || (0 >= $ps_languageId)) {
            return array();
        }

/*
        var_dump($mc_topics);
        var_dump($mc_articles);
        var_dump($mc_issues);
        var_dump($mc_sections);
*/

/*
        $p_asArray = $p_parameters["asArray"];
        $p_mapId = $p_parameters["mapId"];
        $p_languageId = $p_parameters["languageId"];
        $p_preview = $p_parameters["preview"];
        $p_textOnly = $p_parameters["textOnly"];
        $p_mapCons = $p_parameters["mapCons"];
        if (!$p_mapCons) {$p_mapCons = array();}
*/

        if (!$p_order) {$p_order = array();}

        //if (((0 == $p_mapId) || (!$p_mapId)) && (!$p_mapCons)) {return array();}
        if (((0 == $ps_mapId) || (!$ps_mapId)) && (!$mc_mapCons)) {return array();}

        $mc_limit = 0 + $p_limit;
        if (0 > $mc_limit) {$mc_limit = 0;}

        $mc_start = 0 + $p_start;
        if (0 > $mc_start) {$mc_start = 0;}

		$dataArray = array();
		$objsArray = array();
        $cachedData = null;
        //$omit_text = false;

        //$gotObjData = false;
        //$gotArrData = false;

        $paramsArray_arr = array();
        $paramsArray_obj = array();

        $cacheList_arr = null;
        $cacheList_obj = null;

        //if (!$p_skipCache) {
        if (false && (!$p_skipCache)) {
            $paramsArray_arr['mapId'] = $p_mapId;
            $paramsArray_obj['mapId'] = $p_mapId;
            //$paramsArray_arr['textData'] = true;
            //$paramsArray_obj['textData'] = true;
            $paramsArray_arr['asArray'] = true;
            $paramsArray_obj['asArray'] = false;

            $paramsArray_arr['languageId'] = $p_languageId;
            $paramsArray_obj['languageId'] = $p_languageId;
            $paramsArray_arr['preview'] = $p_preview;
            $paramsArray_obj['preview'] = $p_preview;
            $paramsArray_arr['mapCons'] = serialize($p_mapCons);
            $paramsArray_obj['mapCons'] = serialize($p_mapCons);

        	$paramsArray_arr['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray_obj['order'] = (is_null($p_order)) ? 'null' : $p_order;
        	$paramsArray_arr['start'] = $p_start;
        	$paramsArray_obj['start'] = $p_start;
        	$paramsArray_arr['limit'] = $p_limit;
        	$paramsArray_obj['limit'] = $p_limit;

        	//$paramsArray_arr['form'] = "array";
        	//$paramsArray_obj['form'] = "object";

        	$cacheList_arr = new CampCacheList($paramsArray_arr, __METHOD__);
        	$cacheList_obj = new CampCacheList($paramsArray_obj, __METHOD__);

            if ($p_asArray) {
                $cachedData = $cacheList_arr->fetchFromCache();
                if ($cachedData !== false && is_array($cachedData)) {
                    //$gotArrData = true;
                    //$dataArray = $cachedData["points"];
                    //if ($p_textOnly || $cachedData["multimedia"]) {
                    //    return $dataArray;
                    //} else {
                    //    $omit_text = true;
                    //}
                    return $cachedData;
                }
            } else {
                $cachedData = $cacheList_obj->fetchFromCache();
                if ($cachedData !== false && is_array($cachedData)) {
                    //$gotObjData = true;
                    //return $cachedData["points"];
                    return $cachedData;
                }
            }

        }

		global $g_ado_db;

/*
        $selectClauseObj = new SQLSelectClause();

        $selectClauseObj->addColumn("ml.id AS ml_id");
        $selectClauseObj->addColumn("mll.id as mll_id");
        $selectClauseObj->addColumn("ml.fk_location_id AS loc_id");
        $selectClauseObj->addColumn("mll.fk_content_id AS con_id");
        $selectClauseObj->addColumn("ml.poi_style AS poi_style");
        $selectClauseObj->addColumn("ml.rank AS rank");
        $selectClauseObj->addColumn("mll.poi_display AS poi_display");

        $selectClauseObj->addColumn("AsText(l.poi_location) AS loc");
        $selectClauseObj->addColumn("l.poi_type AS poi_type");
        $selectClauseObj->addColumn("l.poi_type_style AS poi_type_style");

        $selectClauseObj->addColumn("c.poi_name AS poi_name");
        $selectClauseObj->addColumn("c.poi_link AS poi_link");
        $selectClauseObj->addColumn("c.poi_perex AS poi_perex");
        $selectClauseObj->addColumn("c.poi_content_type AS poi_content_type");
        $selectClauseObj->addColumn("c.poi_content AS poi_content");
        $selectClauseObj->addColumn("c.poi_text AS poi_text");
*/

		$sql_params = array();

        //$list_fill = "%%id_list%%";

		$queryStr = "SELECT ml.id AS ml_id, mll.id as mll_id, ml.fk_location_id AS loc_id, mll.fk_content_id AS con_id, ";
        $queryStr .= "ml.poi_style AS poi_style, ml.rank AS rank, mll.poi_display AS poi_display, ";

        // these few lines below are just for data for list-of-objects array
        $queryStr .= "l.poi_radius AS l_radius, l.IdUser AS l_user, l.time_updated AS l_updated, ";
        //if ($p_mapCons)
        if ($mc_mapCons)
        {
            $queryStr .= "m.id AS m_id, ";
        }
        $queryStr .= "c.IdUser AS c_user, c.time_updated AS c_updated, ";

        $queryStr .= "AsText(l.poi_location) AS loc, l.poi_type AS poi_type, l.poi_type_style AS poi_type_style, ";

        $queryStr .= "c.poi_name AS poi_name, c.poi_link AS poi_link, c.poi_perex AS poi_perex, ";
        $queryStr .= "c.poi_content_type AS poi_content_type, c.poi_content AS poi_content, c.poi_text AS poi_text ";

        $queryStr .= "FROM MapLocations AS ml INNER JOIN MapLocationLanguages AS mll ON ml.id = mll.fk_maplocation_id ";
        $queryStr .= "INNER JOIN Locations AS l ON l.id = ml.fk_location_id ";
        $queryStr .= "INNER JOIN LocationContents AS c ON c.id = mll.fk_content_id ";

        $query_mcons = "";
        $article_mcons = false;
        //$mc_limit = false;
        $to_filter = false;
        $mc_filter_mm = false;
        $mc_filter_image = false;
        $mc_filter_video = false;

        // this is for making the caching easier
        $p_textOnly = false;

        //if ($p_mapCons)
        if ($mc_mapCons)
        {
            $queryStr .= "INNER JOIN Maps AS m ON m.id = ml.fk_map_id ";
            $queryStr .= "INNER JOIN Articles AS a ON m.fk_article_number = a.Number ";
            $query_mcons = "";
            $article_mcons = false;

            //$mc_order_type = strtolower($p_mapCons["order"]);
            $mc_order_type = "";
            $mc_order = "DESC";
            if ("asc" == $mc_order_type) {
                $mc_order = "ASC";
            }
            //$mc_limit = 0 + $p_mapCons["limit"];
            //if (0 > $mc_limit) {$mc_limit = 0;}

/*
            $mc_multimedia = $p_mapCons["multimedia"];
            $mc_articles = $p_mapCons["articles"];
            $mc_issues = $p_mapCons["issues"];
            $mc_sections = $p_mapCons["sections"];
            $mc_dates = $p_mapCons["dates"];
            $mc_topics = $p_mapCons["topics"];
            $mc_areas = $p_mapCons["areas"];
            $mc_correct = true;
*/

            //var_dump($mc_multimedia);
            //if (0 < count($mc_multimedia)) {
            foreach ($mc_multimedia as $one_multimedia) {
                if ("any" == $one_multimedia) {$mc_filter_mm = true;}
                if ("image" == $one_multimedia) {$mc_filter_image = true;}
                if ("video" == $one_multimedia) {$mc_filter_video = true;}
                //$mc_filter_mm = $mc_multimedia["any"];
                //$mc_filter_image = $mc_multimedia["image"];
                //$mc_filter_video = $mc_multimedia["video"];
                if ($mc_filter_mm || $mc_filter_image || $mc_filter_video) {$to_filter = true;}
            }

            if (0 < count($mc_articles_yes)) {
                $mc_correct = true;
                foreach ($mc_articles_yes as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= "a.Number IN (" . implode(", ", $mc_articles_yes) . ") AND ";
                    $article_mcons = true;
                }
            }
            if (0 < count($mc_articles_no)) {
                $mc_correct = true;
                foreach ($mc_articles_no as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= "a.Number NOT IN (" . implode(", ", $mc_articles_no) . ") AND ";
                    $article_mcons = true;
                }
            }
            if (0 < count($mc_issues)) {
                $mc_correct = true;
                foreach ($mc_issues as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= "a.NrIssue IN (" . implode(", ", $mc_issues) . ") AND ";
                    $article_mcons = true;
                }
            }
            if (0 < count($mc_sections)) {
                $mc_correct = true;
                foreach ($mc_sections as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= "a.NrSection IN (" . implode(", ", $mc_sections) . ") AND ";
                    $article_mcons = true;
                }
            }

            //if (2 == count($mc_dates)) {
            foreach ($mc_dates as $one_date_type => $one_date_value) {
                //$date_start = str_replace("'", "\"", $mc_dates[0]);
                //$date_stop = str_replace("'", "\"", $mc_dates[1]);
                $one_date_value = str_replace("'", "\"", $one_date_value);
                $one_date_usage = "a.PublishDate ";
                $one_date_known = false;
                if ("smaller_equal" == $one_date_type) {$one_date_usage .= "<= "; $one_date_known = true;}
                if ("smaller" == $one_date_type) {$one_date_usage .= "< "; $one_date_known = true;}
                if ("greater_equal" == $one_date_type) {$one_date_usage .= ">= "; $one_date_known = true;}
                if ("greater" == $one_date_type) {$one_date_usage .= "> "; $one_date_known = true;}
                if ("is" == $one_date_type) {$one_date_usage .= "= "; $one_date_known = true;}
                if ("not" == $one_date_type) {$one_date_usage .= "<> "; $one_date_known = true;}
                if (!$one_date_known) {continue;}
                $one_date_usage .= "'$one_date_value' AND ";
    
                //$query_mcons .= "a.PublishDate >= '$date_start' AND a.PublishDate <= '$date_stop' AND ";
                $query_mcons .= $one_date_usage;
                $article_mcons = true;
            }

            if (0 < count($mc_topics))
            {
/*
                $mc_correct = true;
                foreach ($mc_topics as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $queryStr .= "INNER JOIN ArticleTopics AS at ON a.Number = at.NrArticle ";
                    $query_mcons .= "at.TopicId IN (" . implode(", ", $mc_topics) . ") AND ";
                    $article_mcons = true;
                }
*/
                //$mc_topics_str = "INNER JOIN ArticleTopics AS at ON a.Number = at.NrArticle ";
                //$mc_topics_par = "";
                $mc_topics_list = array();

                $mc_topics_conn = "OR";
                if ($mc_topics_matchall) {
                    $mc_topics_conn = "AND";
                }

                //var_dump($mc_topics);
                foreach ($mc_topics as $one_topic) {
                    if (!is_numeric($one_topic)) {continue;}
                    $mc_topics_list[] = "at.TopicId IN (" . Topic::BuildAllSubtopicsQuery($one_topic, false) . ")";
                }

                if (0 < count($mc_topics_list)) {
                    $queryStr .= "INNER JOIN ArticleTopics AS at ON a.Number = at.NrArticle ";
                    $query_mcons .= "(" . implode(" $mc_topics_conn ", $mc_topics_list) . ") AND ";
                    $article_mcons = true;
                }


            }

            //if ($mc_areas) {
            foreach ($mc_areas as $one_area) {
                if (is_object($one_area)) {
                    $one_area = get_object_vars($one_area);
                }

                $mc_rectangle = $one_area["rectangle"];
                $mc_clockwise = $one_area["clockwise"];
                $mc_counterclockwise = $one_area["counterclockwise"];

                if ($mc_rectangle && (2 == count($mc_rectangle))) {
                    $area_cons_res = Geo_MapLocation::GetGeoSearchSQLCons($mc_rectangle, "rectangle", "l");
                    if (!$area_cons_res["error"]) {
                        $query_mcons .= $area_cons_res["cons"] . " AND ";
                        $article_mcons = true;
                    }    
                }

                if ($mc_clockwise && (3 <= count($mc_clockwise))) {
                    $area_cons_res = Geo_MapLocation::GetGeoSearchSQLCons($mc_clockwise, "clockwise", "l");
                    if (!$area_cons_res["error"]) {
                        $query_mcons .= $area_cons_res["cons"] . " AND ";
                        $article_mcons = true;
                    }    
                }

                if ($mc_counterclockwise && (3 <= count($mc_counterclockwise))) {
                    $area_cons_res = Geo_MapLocation::GetGeoSearchSQLCons($mc_counterclockwise, "counterclockwise", "l");
                    if (!$area_cons_res["error"]) {
                        $query_mcons .= $area_cons_res["cons"] . " AND ";
                        $article_mcons = true;
                    }    
                }
            }

            $mmu_test_join = "%%mmu_test_join%%";
            $mmu_test_spec = "%%mmu_test_spec%%";
            $multimedia_test_common = "EXISTS (SELECT mlmu.id FROM MapLocationMultimedia AS mlmu $mmu_test_join WHERE mlmu.fk_maplocation_id = ml.id $mmu_test_spec) AND ";

            $multimedia_test_basic = $multimedia_test_common;
            $multimedia_test_basic = str_replace($mmu_test_join, "", $multimedia_test_basic);
            $multimedia_test_basic = str_replace($mmu_test_spec, "", $multimedia_test_basic);

            $multimedia_test_spec = $multimedia_test_common;
            $multimedia_test_spec = str_replace($mmu_test_join, "INNER JOIN Multimedia AS mu ON mlmu.fk_multimedia_id = mu.id ", $multimedia_test_spec);

            $multimedia_test_image = $multimedia_test_spec;
            $multimedia_test_image = str_replace($mmu_test_spec, "AND mu.media_type = 'image'", $multimedia_test_image);
            $multimedia_test_video = $multimedia_test_spec;
            $multimedia_test_video = str_replace($mmu_test_spec, "AND mu.media_type = 'video'", $multimedia_test_video);


            if ($mc_filter_image) {
                $query_mcons .= $multimedia_test_image;
            }
            if ($mc_filter_video) {
                $query_mcons .= $multimedia_test_video;
            }
            if ($mc_filter_mm) {
                $query_mcons .= $multimedia_test_basic;
            }


            $queryStr .= "WHERE ";
            if ($article_mcons) {
                $queryStr .= $query_mcons;
            }

            $queryStr .= "a.Published = 'Y' AND a.IdLanguage = ? ";
            //$sql_params[] = $p_languageId;
            $sql_params[] = $ps_languageId;

        }
        else
        {
            $queryStr .= "WHERE ml.fk_map_id = ? ";
            $sql_params[] = $ps_mapId;
        }

        $queryStr .= "AND mll.fk_language_id = ? ";
        //$sql_params[] = $p_languageId;
        $sql_params[] = $ps_languageId;

        //if ($p_preview)
        if ($ps_preview)
        {
            $queryStr .= "AND mll.poi_display = 1 ";
        }

        $queryStr .= "ORDER BY ";
        //if ($p_mapCons)
        if ($mc_mapCons)
        {
            $queryStr .= "a.Number $mc_order, m.id $mc_order, ";
        }
        $queryStr .= "ml.rank, ml.id, mll.id";

        //if ($p_mapCons && $mc_limit && (!$to_filter))
        //if ($p_mapCons && $mc_limit)
        //if ($mc_limit)
        if (false)
        {
            $queryStr .= " LIMIT ?";
            $sql_params[] = $mc_limit;
            if ($mc_start)
            {
                $queryStr .= " OFFSET ?";
                $sql_params[] = $mc_start;
            }
        }

        $tmp_name = "tmp_poi_ids_" . mt_rand();

        $queryStr_mm = "SELECT m.id AS m_id, mlm.id AS mlm_id, ml.id AS ml_id, ";
        $queryStr_mm .= "m.media_type AS media_type, m.media_spec AS media_spec, ";
        $queryStr_mm .= "m.media_src AS media_src, m.media_height AS media_height, m.media_width AS media_width ";
        $queryStr_mm .= "FROM Multimedia AS m INNER JOIN MapLocationMultimedia AS mlm ON m.id = mlm.fk_multimedia_id ";
        $queryStr_mm .= "INNER JOIN MapLocations AS ml ON ml.id = mlm.fk_maplocation_id ";
        $queryStr_mm .= "INNER JOIN $tmp_name AS p ON p.id = ml.id ";

        $queryStr_tt_cr = "CREATE TEMPORARY TABLE $tmp_name (id int(10) unsigned) ENGINE=MEMORY;";
        $queryStr_tt_in = "INSERT INTO $tmp_name (id) VALUES (?);";
        $queryStr_tt_rm = "DROP TABLE $tmp_name;";

        //if ($to_filter || (!$p_textOnly)) {
        //if (!$p_textOnly) {
        if (!$ps_textOnly) {
            $success = $g_ado_db->Execute($queryStr_tt_cr);
        }

		//$dataArray = array();

        //echo "\n<br>\n$queryStr\n<br>\n";
        //var_dump($sql_params);
		$rows = $g_ado_db->GetAll($queryStr, $sql_params);

		if (is_array($rows)) {
			foreach ($rows as $row) {
                $tmp_loc = trim(strtolower($row['loc']));
                $loc_matches = array();
                if (!preg_match('/^point\((?P<latitude>[\d.-]+)\s(?P<longitude>[\d.-]+)\)$/', $tmp_loc, $loc_matches)) {continue;}
                $tmp_latitude = $loc_matches['latitude'];
                $tmp_longitude = $loc_matches['longitude'];

                $tmpPoint = array();
				$tmpPoint['latitude'] = $tmp_latitude;
				$tmpPoint['longitude'] = $tmp_longitude;

                $tmpPoint['loc_id'] = $row['ml_id'];
                $tmpPoint['con_id'] = $row['mll_id'];

                $tmpPoint['style'] = $row['poi_style'];
                $tmpPoint['rank'] = $row['rank'];
                $tmpPoint['display'] = $row['poi_display'];

				$tmpPoint['title'] = $row['poi_name'];
				$tmpPoint['link'] = $row['poi_link'];

				$tmpPoint['perex'] = $row['poi_perex'];
				$tmpPoint['content_type'] = $row['poi_content_type'];
				$tmpPoint['content'] = $row['poi_content'];
				$tmpPoint['text'] = $row['poi_text'];

				$tmpPoint['image_mm'] = 0;
				$tmpPoint['image_src'] = "";
				$tmpPoint['image_width'] = "";
				$tmpPoint['image_height'] = "";

				$tmpPoint['video_mm'] = 0;
				$tmpPoint['video_id'] = "";
				$tmpPoint['video_type'] = "";
				$tmpPoint['video_width'] = "";
				$tmpPoint['video_height'] = "";

                // for the list-of-objects array
                $tmpPoint['map_id'] = $p_mapId;
                //if ($p_mapCons) {
                if ($mc_mapCons) {
                    $tmpPoint['map_id'] = $row['m_id'];
                }
                $tmpPoint['geo_id'] = $row['loc_id'];
                $tmpPoint['geo_type'] = $row['poi_type'];
                $tmpPoint['geo_style'] = $row['poi_type_style'];
                $tmpPoint['geo_radius'] = $row['l_radius'];
                $tmpPoint['geo_user'] = $row['l_user'];
                $tmpPoint['geo_updated'] = $row['l_updated'];
                $tmpPoint['txt_id'] = $row['con_id'];
                $tmpPoint['txt_user'] = $row['c_user'];
                $tmpPoint['txt_updated'] = $row['c_updated'];

                $dataArray[] = $tmpPoint;

                //if ($to_filter || (!$p_textOnly)) {
                //if (!$p_textOnly) {
                if (!$ps_textOnly) {
                    $success = $g_ado_db->Execute($queryStr_tt_in, array($row['ml_id']));
                }

            }
        }

        //var_dump($dataArray);

        if (0 == count($dataArray)) {return $dataArray;}
        //if ((!$to_filter) && $p_textOnly) {return $dataArray;}
        //if ($p_textOnly) {return $dataArray;}
        if ($ps_textOnly) {return $dataArray;}

        {
            $imagesArray = array();
            $videosArray = array();
    
            $rows = $g_ado_db->GetAll($queryStr_mm);
    
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $tmpPoint = array();
                    $tmpPoint["m_id"] = $row["m_id"];
                    $tmpPoint["mlm_id"] = $row["mlm_id"];
                    $tmpPoint["ml_id"] = $row["ml_id"];
                    $tmpPoint["type"] = $row["media_type"];
                    $tmpPoint["spec"] = $row["media_spec"];
                    $tmpPoint["src"] = $row["media_src"];
                    $tmpPoint["width"] = $row["media_width"];
                    $tmpPoint["height"] = $row["media_height"];
    
                    $tmp_id = $row["ml_id"];
                    $tmp_type = $row["media_type"];
                    if ("image" == $tmp_type)
                    {
                        $imagesArray[$tmp_id] = $tmpPoint;
                    }
                    if ("video" == $tmp_type)
                    {
                        $videosArray[$tmp_id] = $tmpPoint;
                    }
                }
            }

        }

        //$mm_filter = array();
        //$required_mm = ($mc_filter_mm) ? true : false;
        //$required_image = ($mc_filter_image) ? true : false;
        //$required_video = ($mc_filter_video) ? true : false;

        foreach ($dataArray AS $index => $poi)
        {
            //$with_image = false;
            //$with_video = false;

            $ml_id = $poi["loc_id"];
            if (array_key_exists($ml_id, $imagesArray))
            {
                //$with_image = true;
                //if (!$p_textOnly) {
                    $dataArray[$index]["image_mm"] = $imagesArray[$ml_id]["mlm_id"];
                    $dataArray[$index]["image_src"] = $imagesArray[$ml_id]["src"];
                    $dataArray[$index]["image_width"] = $imagesArray[$ml_id]["width"];
                    $dataArray[$index]["image_height"] = $imagesArray[$ml_id]["height"];
                //}
            }
            if (array_key_exists($ml_id, $videosArray))
            {
                //$with_video = true;
                //if (!$p_textOnly) {
                    $dataArray[$index]["video_mm"] = $videosArray[$ml_id]["mlm_id"];
                    $dataArray[$index]["video_id"] = $videosArray[$ml_id]["src"];
                    $dataArray[$index]["video_type"] = $videosArray[$ml_id]["spec"];
                    $dataArray[$index]["video_width"] = $videosArray[$ml_id]["width"];
                    $dataArray[$index]["video_height"] = $videosArray[$ml_id]["height"];
                //}
            }

/*
            if ($to_filter)
            {
                $mm_satis = true;
                if ($required_mm) {
                    if ((!$with_image) && (!$with_video)) {$mm_satis = false;}
                }
                if ($required_image) {
                    if (!$with_image) {$mm_satis = false;}
                }
                if ($required_video) {
                    if (!$with_video) {$mm_satis = false;}
                }
                if ($mm_satis) {$mm_filter[$index] = true;}
            }
*/
        }

        //if ($to_filter) {
        //    $dataArray = array_intersect_key($dataArray, $mm_filter);
        //}

        //if (!$p_textOnly) {
            $success = $g_ado_db->Execute($queryStr_tt_rm);
        //}

        //if ($mc_limit) {
        //    $dataArray = array_splice($dataArray, 0, $mc_limit);
        //}

        $dataArray_tmp = $dataArray;
        $objsArray = array();
        $dataArray = array();

        //echo "\n<br>xxxxxxxxxxxx<br>\n";
        //var_dump($dataArray_tmp[0]);
        //echo "\n<br>yyyyyyyyyyyy<br>\n";


        foreach ($dataArray_tmp as $one_poi)
        //foreach (array() as $one_poi)
        {
            $one_poi_source = array(
                'id' => $one_poi['loc_id'],
                'fk_map_id' => $one_poi['map_id'],
                'fk_location_id' => $one_poi['geo_id'],
                'poi_style' => $one_poi['style'],
                'rank' => $one_poi['rank'],
            );
            //$one_poi_obj = new self($one_poi_source, true);

            $one_geo_source = array(
                'poi_location' => null,
                'poi_type' => $one_poi['geo_type'],
                'poi_type_style' => $one_poi['geo_style'],
                'poi_center' => null,
                'poi_radius' => $one_poi['geo_radius'],
                'IdUser' => $one_poi['geo_user'],
                'time_updated' => $one_poi['geo_updated'],
                'latitude' => $one_poi['latitude'],
                'longitude' => $one_poi['longitude'],
            );
            //$one_poi_obj->location = new Geo_Location($one_geo_source, true);

            $one_lan_source = array(
                'id' => $one_poi['con_id'],
                'fk_maplocation_id' => $one_poi['loc_id'],
                'fk_language_id' => $p_languageId,
                'fk_content_id' => $one_poi['txt_id'],
                'poi_display' => $one_poi['display'],
            );
            //$one_poi_obj->setLanguage($p_languageId, new Geo_MapLocationLanguage(NULL, 0, $one_geo_source, true));

            $one_txt_source = array(
                'id' => $one_poi['txt_id'],
                'poi_name' => $one_poi['title'],
                'poi_link' => $one_poi['link'],
                'poi_perex' => $one_poi['perex'],
                'poi_content_type' => $one_poi['content_type'],
                'poi_content' => $one_poi['content'],
                'poi_text' => $one_poi['text'],
                'IdUser' => $one_poi['txt_user'],
                'time_updated' => $one_poi['txt_updated'],
            );

            //$one_poi_obj->setContent($p_languageId, new Geo_MapLocationContent(NULL, NULL, $one_txt_source, true));

            //$objsArray[] = $one_poi_obj;
            $dataArray[] = $one_poi;
        }

/*
        if (!$p_skipCache && CampCache::IsEnabled()) {
        	$cacheList_arr->storeInCache($dataArray);
        	$cacheList_obj->storeInCache($objsArray);
        }
*/

        //if (!$p_asArray) {
        if (!$ps_asArray) {
            return $objsArray;
        }

		return $dataArray;

	} // fn ReadMapPoints



    public static function GetGeoSearchSQLCons($p_coordinates, $p_polygonType = "rectangle", $p_tableAlias = "l")
    {
        if (is_object($p_coordinates)) {
            $p_coordinates = get_object_vars($p_coordinates);
        }

        $queryCons = "";
        $paramError = false;

        if (!ctype_alnum($p_tableAlias)) {
            $paramError = true;
            return array("error" => true, "cons" => "");
        }

        $p_polygonType = strtolower("" . $p_polygonType);
        if (!array_key_exists($p_polygonType, array("rectangle" => 1, "clockwise" => 1, "counterclockwise" => 1))) {
            $paramError = true;
            return array("error" => true, "cons" => "");
        }

        if ("rectangle" == $p_polygonType)
        {
            $queryCons_1 = "";
            $queryCons_2 = "";

            $queryCons_1 .= "Intersects(GeomFromText('Polygon((%%x0%% %%y0%%,%%x0%% %%y1%%,%%x1%% %%y1%%,%%x1%% %%y0%%,%%x0%% %%y0%%))'),$p_tableAlias.poi_location) ";
            $queryCons_2 .= "(Intersects(GeomFromText('Polygon((%%x0%% %%y0%%,%%x0%% 180,%%x1%% 180,%%x1%% %%y0%%,%%x0%% %%y0%%))'),$p_tableAlias.poi_location) OR Intersects(GeomFromText('Polygon((%%x0%% -180,%%x0%% %%y1%%,%%x1%% %%y1%%,%%x1%% -180,%%x0%% -180))'),$p_tableAlias.poi_location)) ";
    
            $loc_left = $p_coordinates[0];
            $loc_right = $p_coordinates[1];
            if (is_object($loc_right)) {
                $loc_right = get_object_vars($loc_right);
            }
            if (is_object($loc_left)) {
                $loc_left = get_object_vars($loc_left);
            }

            $left_lon = "" . $loc_left["longitude"];
            $left_lat = "" . $loc_left["latitude"];
            $right_lon = "" . $loc_right["longitude"];
            $right_lat = "" . $loc_right["latitude"];
    
            if (!is_numeric($left_lon)) {$left_lon = "0";}
            if (!is_numeric($left_lat)) {$left_lat = "0";}
            if (!is_numeric($right_lon)) {$right_lon = "0";}
            if (!is_numeric($right_lat)) {$right_lat = "0";}
    
            $south_lat = $right_lat;
            $north_lat = $left_lat;
            if ($south_lat > $north_lat)
            {
                $south_lat = $left_lat;
                $north_lat = $right_lat;
            }
    
            $east_lon = $left_lon;
            $west_lon = $right_lon;
    
            if ($east_lon > $west_lon)
            {
                $queryCons .= $queryCons_2;
            }
            else
            {
                $queryCons .= $queryCons_1;
            }
    
            $queryCons = str_replace("%%y0%%", $east_lon, $queryCons);
            $queryCons = str_replace("%%y1%%", $west_lon, $queryCons);
            $queryCons = str_replace("%%x0%%", $south_lat, $queryCons);
            $queryCons = str_replace("%%x1%%", $north_lat, $queryCons);

        }

        if ((0 < count($p_polygonType)) && (("clockwise" == $p_polygonType) || ("counterclockwise" == $p_polygonType)))
        {
            $polygon_spec = "";

            $ind_start = 0;
            $ind_stop = count($p_coordinates) - 1;
            $ind_step = 1;
            if ("counterclockwise" == $p_polygonType) {
                $ind_start = count($p_coordinates) - 1;
                $ind_stop = 0;
                $ind_step = -1;
            }

            $first_lon = $p_polygonType[$ind_start]["longitude"];
            $first_lat = $p_polygonType[$ind_start]["latitude"];

            for ($ind = $ind_start; ; $ind += $ind_step) {

                $corner = $p_coordinates[$ind];
                $one_lon = $corner["longitude"];
                $one_lat = $corner["latitude"];
                if ((!is_numeric($one_lon)) || (!is_numeric($one_lat))) {
                    $paramError = true;
                    break;
                }

                $polygon_spec .= "$one_lon $one_lat,";

                if ($ind == $ind_stop) {break;}
            }
            $polygon_spec .= "$first_lon $first_lat";

            $queryCons .= "Intersects(GeomFromText('Polygon(($polygon_spec))'),$p_tableAlias.poi_location) ";

        }

        if ($paramError) {
            return array("error" => true, "cons" => "");
        }

        return array("error" => false, "cons" => $queryCons);
    } // fn GetGeoSearchSQLCons




} // class Geo_MapLocation





