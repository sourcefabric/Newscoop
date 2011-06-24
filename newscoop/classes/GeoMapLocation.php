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
     * @param array $p_order
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
     * Returns map locations list based on the given parameters.
     *
     * @param array $p_parameters
     *    An array of ComparionOperation objects
     * @param array $p_order
     *    An array of columns and directions to order by
     * @param integer $p_start
     *    The record number to start the list
     * @param integer $p_limit
     *    The offset, how many records from $p_start will be retrieved
     * @param integer $p_count
     *    Total count of POIs without p_start/p_limit limitations
     * @param bool $p_skipCache
     *    Whether to skip caching
     * @param array $p_rawData
     *    The variable for returning read points as raw array, used for the JS processing
     *
     * @return array
     */
    public static function GetListExt(array $p_parameters, array $p_order = array(),
                                   $p_start = 0, $p_limit = 0, &$p_count, $p_skipCache = false, &$p_rawData = null)
    {
        $p_count = 0;

        if (!is_numeric($p_limit)) {$p_limit = 0;}
        if (!is_numeric($p_start)) {$p_start = 0;}

        $ps_saveArray = false;
        if (!is_null($p_rawData)) {
            $ps_saveArray = true;
            $p_rawData = array();
        }
        $ps_mapId = 0;
        $ps_languageId = 0;
        $ps_preview = false;
        // read all the info, mostly cached at the upstream
        $ps_publicationId = 0;

        $mc_mapCons = false;
        $mc_authors_yes = array();
        $mc_authors_no = array();
        $mc_users_yes = array();
        $mc_users_no = array();
        $mc_articles_yes = array();
        $mc_articles_no = array();
        $mc_issues = array();
        $mc_sections = array();
        $mc_section_names = array();
        $mc_topics = array();
        $mc_topic_names = array();
        $mc_topics_matchall = false;
        $mc_multimedia = array();
        $mc_areas = array();
        $mc_areas_matchall = false;
        $mc_areas_exact = true;
        $mc_dates = array();
        $mc_icons = array();

        // process params
        foreach ($p_parameters as $param) {
            switch ($param->getLeftOperand()) {
                case 'constrained':
                    $mc_mapCons = true;
                case 'map':
                    if (is_numeric($param->getRightOperand())) {
                        $ps_mapId = $param->getRightOperand();
                    }
                    break;
                case 'language':
                    if (is_numeric($param->getRightOperand())) {
                        $ps_languageId = 0 + $param->getRightOperand();
                    }
                    break;
                case 'active_only':
                case 'preview':
                    $ps_preview = $param->getRightOperand();
                    break;
                case 'publication':
                    $ps_publicationId = (int) $param->getRightOperand();
                    break;
                case 'author':
                    $one_user_value = $param->getRightOperand();
                    $one_user_type = $param->getOperator()->getName();
                    if ('is' == $one_user_type) {
                        $mc_authors_yes[] = $one_user_value;
                        $mc_mapCons = true;
                    }
                    if ('not' == $one_user_type) {
                        $mc_authors_no[] = $one_user_value;
                        $mc_mapCons = true;
                    }
                    break;
                case 'article':
                    $one_article_value = $param->getRightOperand();
                    $one_article_type = $param->getOperator()->getName();
                    if ('is' == $one_article_type) {
                        $mc_articles_yes[] = $one_article_value;
                        $mc_mapCons = true;
                    }
                    if ('not' == $one_article_type) {
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
                case 'section_name':
                    $mc_section_names[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'topic':
                    $mc_topics[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'topic_name':
                    $mc_topic_names[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'matchalltopics':
                    $mc_topics_matchall = $param->getRightOperand();
                    break;
                case 'matchanytopic':
                    $mc_topics_matchall = !$param->getRightOperand();
                    break;
                case 'multimedia':
                    $mc_multimedia[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'area':
                    $mc_areas[] = json_decode($param->getRightOperand());
                    $mc_mapCons = true;
                    break;
                case 'matchallareas':
                    $mc_areas_matchall = $param->getRightOperand();
                    break;
                case 'matchanyarea':
                    $mc_areas_matchall = !$param->getRightOperand();
                    break;
                case 'exactarea':
                    $mc_areas_exact = $param->getRightOperand();
                    break;
                case 'date':
                    $mc_dates[$param->getOperator()->getName()] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                case 'icon':
                    $mc_icons[] = $param->getRightOperand();
                    $mc_mapCons = true;
                    break;
                default:
                    break;
            }
        }
        
        // constrained maps ned to have set publication
        if ($mc_mapCons && (!$ps_publicationId)) {
            return array();
        }

        if ((!$ps_languageId) || (0 >= $ps_languageId)) {
            $ps_languageId = $Context->language->number;
        }


        if ((!$ps_languageId) || (0 >= $ps_languageId)) {
            return array();
        }

        if (!CampCache::IsEnabled()) {$p_skipCache = true;}

        $ps_orders = array();
        if ((!$p_order) || (!is_array($p_order)) || (0 == count($p_order))) {
            if ($mc_mapCons) {
                $ps_orders = array(array('a.Number' => 'DESC'), array('m.id' => 'DESC'));
            }
        } else {
            $allowed_order_dirs = array('DESC' => true, 'ASC' => true);
            foreach ($p_order as $one_order_column => $one_order_dir) {
                $one_dir = strtoupper($one_order_dir);
                if (!array_key_exists($one_dir, $allowed_order_dirs)) {continue;}
                switch(strtolower($one_order_column)) {
                    case 'article':
                        if (!$mc_mapCons) {break;}
                        $ps_orders[] = array('a.Number' => $one_dir);
                        break;
                    case 'map':
                        $ps_orders[] = array('m.id' => $one_dir);
                        break;
                    case 'name':
                        $ps_orders[] = array('c.poi_name' => $one_dir);
                        break;
                }
            }
        }

        if (((0 == $ps_mapId) || (!$ps_mapId)) && (!$mc_mapCons)) {return array();}

        $mc_limit = 0 + $p_limit;
        if (0 > $mc_limit) {$mc_limit = 0;}

        $mc_start = 0 + $p_start;
        if (0 > $mc_start) {$mc_start = 0;}

        $dataArray = array();
        $objsArray = array();
        $cachedData = null;

        $paramsArray_arr = array();
        $paramsArray_obj = array();

        $cacheList_arr = null;
        $cacheList_obj = null;

        if (!$p_skipCache) {
            $paramsArray_arr['as_array'] = true;
            $paramsArray_arr['map_id'] = $ps_mapId;
            $paramsArray_arr['publication_id'] = $ps_publicationId;
            $paramsArray_arr['language_id'] = $ps_languageId;
            $paramsArray_arr['preview'] = $ps_preview;
    
            $paramsArray_arr['map_cons'] = $mc_mapCons;
            $paramsArray_arr['authors_yes'] = $mc_authors_yes;
            $paramsArray_arr['authors_no'] = $mc_authors_no;
            $paramsArray_arr['articles_yes'] = $mc_articles_yes;
            $paramsArray_arr['articles_no'] = $mc_articles_no;
            $paramsArray_arr['issues'] = $mc_issues;
            $paramsArray_arr['sections'] = $mc_sections;
            $paramsArray_arr['section_names'] = $mc_section_names;
            $paramsArray_arr['topics'] = $mc_topics;
            $paramsArray_arr['topic_names'] = $mc_topic_names;
            $paramsArray_arr['topics_matchall'] = $mc_topics_matchall;
            $paramsArray_arr['multimedia'] = $mc_multimedia;
            $paramsArray_arr['areas'] = $mc_areas;
            $paramsArray_arr['areas_matchall'] = $mc_areas_matchall;
            $paramsArray_arr['areas_exact'] = $mc_areas_exact;
            $paramsArray_arr['dates'] = $mc_dates;
            $paramsArray_arr['icons'] = $mc_icons;

            $paramsArray_arr['orders'] = $ps_orders;
            $paramsArray_arr['limit'] = $mc_limit;
            $paramsArray_arr['start'] = $mc_start;

            $paramsArray_obj = $paramsArray_arr;
            $paramsArray_obj['as_array'] = false;

            $cacheList_arr = new CampCacheList($paramsArray_arr, __METHOD__);
            $cacheList_obj = new CampCacheList($paramsArray_obj, __METHOD__);

            if ($ps_saveArray) {
                $cachedData = $cacheList_arr->fetchFromCache();
                if ($cachedData !== false && is_array($cachedData)) {
                    $p_count = $cachedData['count'];
                    $p_rawData = $cachedData['data'];
                }
            }
            {
                $cachedData = $cacheList_obj->fetchFromCache();
                if ($cachedData !== false && is_array($cachedData)) {
                    $p_count = $cachedData['count'];
                    return $cachedData['data'];
                }
            }

        }

        global $g_ado_db;

        if (0 < count($mc_authors_yes)) {
            $author_ids_req_names = ArticleAuthor::BuildAuthorIdsQuery($mc_authors_yes);
            $author_ids_req_alias = AuthorAlias::BuildAuthorIdsQuery($mc_authors_yes);

            if ($author_ids_req_names) {
                $rows = $g_ado_db->GetAll($author_ids_req_names);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $mc_users_yes[] = trim(strtolower($row['id']));
                    }
                }
            }
            if ($author_ids_req_alias) {
                $rows = $g_ado_db->GetAll($author_ids_req_alias);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $mc_users_yes[] = trim(strtolower($row['id']));
                    }
                }
            }

            if (0 == count($mc_users_yes)) {return array();}
        }
        if (0 < count($mc_authors_no)) {
            $author_ids_req_names = ArticleAuthor::BuildAuthorIdsQuery($mc_authors_no);
            $author_ids_req_alias = AuthorAlias::BuildAuthorIdsQuery($mc_authors_no);

            if ($author_ids_req_names) {
                $rows = $g_ado_db->GetAll($author_ids_req_names);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $mc_users_no[] = trim(strtolower($row['id']));
                    }
                }
            }
            if ($author_ids_req_alias) {
                $rows = $g_ado_db->GetAll($author_ids_req_alias);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $mc_users_no[] = trim(strtolower($row['id']));
                    }
                }
            }
        }

        if (0 < count($mc_section_names)) {
            $section_ids_req_names = Section::BuildSectionIdsQuery($mc_section_names, $ps_publicationId);

            if ($section_ids_req_names) {
                $rows = $g_ado_db->GetAll($section_ids_req_names);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $mc_sections[] = trim(strtolower($row['id']));
                    }
                }
            }

            if (0 == count($mc_sections)) {return array();}
        }

        if (0 < count($mc_topic_names)) {
            $topic_ids_req_names = TopicName::BuildTopicIdsQuery($mc_topic_names);

            if ($topic_ids_req_names) {
                $rows = $g_ado_db->GetAll($topic_ids_req_names);
                if (is_array($rows)) {
                    foreach ($rows as $row) {
                        $mc_topics[] = trim(strtolower($row['id']));
                    }
                }
            }

            if (0 == count($mc_topics)) {return array();}
        }

        $ps_orders = array();
        if ((!$p_order) || (!is_array($p_order)) || (0 == count($p_order))) {
            if ($mc_mapCons) {
                $ps_orders = array(array('a.Number' => 'DESC'), array('m.id' => 'DESC'));
            }
        } else {
            $allowed_order_dirs = array('DESC' => true, 'ASC' => true);
            foreach ($p_order as $one_order_column => $one_order_dir) {
                $one_dir = strtoupper($one_order_dir);
                if (!array_key_exists($one_dir, $allowed_order_dirs)) {continue;}
                switch(strtolower($one_order_column)) {
                    case 'article':
                        if (!$mc_mapCons) {break;}
                        $ps_orders[] = array('a.Number' => $one_dir);
                        break;
                    case 'map':
                        $ps_orders[] = array('m.id' => $one_dir);
                        break;
                    case 'name':
                        $ps_orders[] = array('c.poi_name' => $one_dir);
                        break;
                }
            }
        }

        if (((0 == $ps_mapId) || (!$ps_mapId)) && (!$mc_mapCons)) {return array();}

        $queryStr = '';
        $queryStr_start = '';

        $queryStr_start .= 'SELECT DISTINCT ml.id AS ml_id, mll.id as mll_id, ml.fk_location_id AS loc_id, mll.fk_content_id AS con_id, ';
        $queryStr_start .= 'ml.poi_style AS poi_style, ml.rank AS rank, mll.poi_display AS poi_display, ';

        // these few lines below are just for data for list-of-objects array
        $queryStr_start .= 'l.poi_radius AS l_radius, l.IdUser AS l_user, l.time_updated AS l_updated, ';
        if ($mc_mapCons)
        {
            $queryStr_start .= 'm.id AS m_id, m.IdUser AS m_user, m.fk_article_number AS art_number, GROUP_CONCAT(DISTINCT m.fk_article_number ORDER BY m.fk_article_number DESC) AS art_numbers, ';
        }

        $multimedia_def = "CONCAT(mu.id, \"-\", mlmu.id)";
        if ($mc_mapCons) {
            $multimedia_def = 'mu.id';
        }

        $queryStr_start .= "(SELECT $multimedia_def FROM MapLocationMultimedia AS mlmu INNER JOIN Multimedia AS mu ON mlmu.fk_multimedia_id = mu.id WHERE mlmu.fk_maplocation_id = ml.id AND mu.media_type = 'image' ORDER BY mu.id LIMIT 1) AS image_mm, ";
        $queryStr_start .= "(SELECT $multimedia_def FROM MapLocationMultimedia AS mlmu INNER JOIN Multimedia AS mu ON mlmu.fk_multimedia_id = mu.id WHERE mlmu.fk_maplocation_id = ml.id AND mu.media_type = 'video' ORDER BY mu.id LIMIT 1) AS video_mm, ";

        $queryStr_start .= 'c.IdUser AS c_user, c.time_updated AS c_updated, ';

        $queryStr_start .= 'AsText(l.poi_location) AS loc, l.poi_type AS poi_type, l.poi_type_style AS poi_type_style, ';

        $queryStr_start .= 'c.poi_name AS poi_name, c.poi_link AS poi_link, c.poi_perex AS poi_perex, ';
        $queryStr_start .= 'c.poi_content_type AS poi_content_type, c.poi_content AS poi_content, c.poi_text AS poi_text ';

        $queryStr .= 'FROM MapLocations AS ml INNER JOIN MapLocationLanguages AS mll ON ml.id = mll.fk_maplocation_id ';
        $queryStr .= 'INNER JOIN Locations AS l ON l.id = ml.fk_location_id ';
        $queryStr .= 'INNER JOIN LocationContents AS c ON c.id = mll.fk_content_id ';

        $query_mcons = '';
        $article_mcons = false;
        $mc_filter_mm = false;
        $mc_filter_image = false;
        $mc_filter_video = false;

        if ($mc_mapCons)
        {
            $queryStr .= 'INNER JOIN Maps AS m ON m.id = ml.fk_map_id ';
            $queryStr .= 'INNER JOIN Articles AS a ON m.fk_article_number = a.Number ';
            if ((0 < count($mc_users_yes)) || (0 < count($mc_users_no))) {
                $queryStr .= 'INNER JOIN ArticleAuthors AS aa ON aa.fk_article_number = a.Number ';
            }

            $query_mcons = "a.IdPublication = $ps_publicationId AND ";
            $article_mcons = false;

            foreach ($mc_multimedia as $one_multimedia) {
                if ('any' == $one_multimedia) {$mc_filter_mm = true;}
                if ('image' == $one_multimedia) {$mc_filter_image = true;}
                if ('video' == $one_multimedia) {$mc_filter_video = true;}
            }

            if (0 < count($mc_users_yes)) {
                $query_mcons .= 'aa.fk_author_id IN (' . implode(', ', $mc_users_yes) . ') AND ';
                $article_mcons = true;
            }
            if (0 < count($mc_users_no)) {
                $query_mcons .= 'aa.fk_author_id NOT IN (' . implode(', ', $mc_users_no) . ') AND ';
                $article_mcons = true;
            }

            if (0 < count($mc_articles_yes)) {
                $mc_correct = true;
                foreach ($mc_articles_yes as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= 'a.Number IN (' . implode(', ', $mc_articles_yes) . ') AND ';
                    $article_mcons = true;
                }
            }
            if (0 < count($mc_articles_no)) {
                $mc_correct = true;
                foreach ($mc_articles_no as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= 'a.Number NOT IN (' . implode(', ', $mc_articles_no) . ') AND ';
                    $article_mcons = true;
                }
            }

            if (0 < count($mc_issues)) {
                $mc_correct = true;
                foreach ($mc_issues as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= 'a.NrIssue IN (' . implode(', ', $mc_issues) . ') AND ';
                    $article_mcons = true;
                }
            }
            if (0 < count($mc_sections)) {
                $mc_correct = true;
                foreach ($mc_sections as $val) {
                    if (!is_numeric($val)) {$mc_correct = false;}
                }
                if ($mc_correct) {
                    $query_mcons .= 'a.NrSection IN (' . implode(', ', $mc_sections) . ') AND ';
                    $article_mcons = true;
                }
            }

            foreach ($mc_dates as $one_date_type => $one_date_value_arr) {
                if (0 == count($one_date_value_arr)) {continue;}

                $one_date_value = $one_date_value_arr[0];
                $one_date_value = trim($one_date_value);
                $one_date_value = trim($one_date_value, "\"'");

                $one_date_value = str_replace("'", "\"", $one_date_value);
                $one_date_usage = 'CAST(a.PublishDate AS DATE) ';
                $one_date_known = false;
                if ('smaller_equal' == $one_date_type) {$one_date_usage .= '<= '; $one_date_known = true;}
                if ('smaller' == $one_date_type) {$one_date_usage .= '< '; $one_date_known = true;}
                if ('greater_equal' == $one_date_type) {$one_date_usage .= '>= '; $one_date_known = true;}
                if ('greater' == $one_date_type) {$one_date_usage .= '> '; $one_date_known = true;}
                if ('is' == $one_date_type) {$one_date_usage .= '= '; $one_date_known = true;}
                if ('not' == $one_date_type) {$one_date_usage .= '<> '; $one_date_known = true;}
                if (!$one_date_known) {continue;}
                $one_date_usage .= "'$one_date_value' AND ";
    
                $query_mcons .= $one_date_usage;
                $article_mcons = true;
            }

            $mc_icons_usage = array();
            foreach ($mc_icons as $one_icon_value) {
                $one_icon_value = str_replace("'", "\"", $one_icon_value);
                if (0 < strlen($one_icon_value)) {
                    $mc_icons_usage[] = $one_icon_value;
                }
            }
            if (0 < count($mc_icons_usage)) {
                $query_mcons .= "ml.poi_style IN ('" . implode("', '", $mc_icons_usage) . "') AND ";
                $article_mcons = true;
            }

            if (0 < count($mc_topics))
            {
                $mc_topics_list = array();

                $mc_topics_conn = 'OR';
                if ($mc_topics_matchall) {
                    $mc_topics_conn = 'AND';
                }

                foreach ($mc_topics as $one_topic) {
                    if (!is_numeric($one_topic)) {continue;}
                    $mc_topics_list[] = 'EXISTS (SELECT at.TopicId FROM ArticleTopics AS at WHERE a.Number = at.NrArticle AND at.TopicId IN (' . Topic::BuildAllSubtopicsQuery($one_topic, false) . '))';
                }

                if (0 < count($mc_topics_list)) {
                    $query_mcons .= '(' . implode(" $mc_topics_conn ", $mc_topics_list) . ') AND ';
                    $article_mcons = true;
                }


            }

            $mc_areas_list = array();

            $mc_areas_conn = 'OR';
            if ($mc_areas_matchall) {
                $mc_areas_conn = 'AND';
            }

            foreach ($mc_areas as $one_area) {
                if (is_object($one_area)) {
                    $one_area = get_object_vars($one_area);
                }

                $mc_rectangle = $one_area['rectangle'];
                $mc_polygon = $one_area['polygon'];

                if ($mc_rectangle && (2 == count($mc_rectangle))) {
                    $area_cons_res = Geo_MapLocation::GetGeoSearchSQLCons($mc_rectangle, 'rectangle', 'l');
                    if (!$area_cons_res['error']) {
                        $mc_areas_list[] = $area_cons_res['cons'];
                        $article_mcons = true;
                    }    
                }

                if ($mc_polygon && (3 <= count($mc_polygon))) {
                    $area_cons_res = Geo_MapLocation::GetGeoSearchSQLCons($mc_polygon, 'polygon', 'l');

                    $area_cons_res_finer = null;
                    if ($mc_areas_exact) {
                        $area_cons_res_finer = Geo_MapLocation::GetGeoSearchPointInPolygon($mc_polygon, 'l');
                    }

                    if (!$area_cons_res['error']) {
                        $one_area_cons = $area_cons_res['cons'];

                        if ($mc_areas_exact && (!$area_cons_res_finer['error'])) {
                            $one_area_cons = "($one_area_cons AND " . $area_cons_res_finer['cons'] . ')';
                        }

                        $mc_areas_list[] = $one_area_cons;
                        $article_mcons = true;
                    }
                }
            }

            if (0 < count($mc_areas_list)) {
                $query_mcons .= '(' . implode(" $mc_areas_conn ", $mc_areas_list) . ') AND ';
                $article_mcons = true;
            }

            $mmu_test_join = '%%mmu_test_join%%';
            $mmu_test_spec = '%%mmu_test_spec%%';
            $multimedia_test_common = "EXISTS (SELECT mlmu.id FROM MapLocationMultimedia AS mlmu $mmu_test_join WHERE mlmu.fk_maplocation_id = ml.id $mmu_test_spec) AND ";

            $multimedia_test_basic = $multimedia_test_common;
            $multimedia_test_basic = str_replace($mmu_test_join, '', $multimedia_test_basic);
            $multimedia_test_basic = str_replace($mmu_test_spec, '', $multimedia_test_basic);

            $multimedia_test_spec = $multimedia_test_common;
            $multimedia_test_spec = str_replace($mmu_test_join, 'INNER JOIN Multimedia AS mu ON mlmu.fk_multimedia_id = mu.id ', $multimedia_test_spec);

            $multimedia_test_image = $multimedia_test_spec;
            $multimedia_test_image = str_replace($mmu_test_spec, "AND mu.media_type = 'image'", $multimedia_test_image);
            $multimedia_test_video = $multimedia_test_spec;
            $multimedia_test_video = str_replace($mmu_test_spec, "AND mu.media_type = 'video'", $multimedia_test_video);

            if ($mc_filter_image) {
                $query_mcons .= $multimedia_test_image;
                $article_mcons = true;
            }
            if ($mc_filter_video) {
                $query_mcons .= $multimedia_test_video;
                $article_mcons = true;
            }
            if ($mc_filter_mm) {
                $query_mcons .= $multimedia_test_basic;
                $article_mcons = true;
           }

            $queryStr .= 'WHERE ';

            if ($article_mcons) {
                $queryStr .= $query_mcons;
            }

            $queryStr .= "a.Published = 'Y' AND a.IdLanguage = $ps_languageId ";

        }
        else
        {

            $queryStr .= "WHERE ml.fk_map_id = $ps_mapId ";

        }

        $queryStr_mid = "";
        $queryStr_mid .= "AND mll.fk_language_id = $ps_languageId ";

        if ($ps_preview)
        {
            $queryStr_mid .= "AND mll.poi_display = 1 ";
        }

        if ($mc_mapCons)
        {
            $queryStr_mid .= 'GROUP BY ml.fk_location_id, mll.fk_content_id, ml.poi_style, mll.poi_display, image_mm, video_mm ';
        }

        $queryStr .= $queryStr_mid;

        $queryStr = $queryStr_start . $queryStr;

        $queryStr .= 'ORDER BY ';
        foreach ($ps_orders as $one_order) {
            foreach ($one_order as $cur_order_col => $cur_order_dir) {
                $queryStr .= "$cur_order_col $cur_order_dir, ";
            }
        }
        $queryStr .= 'ml.rank, ml.id, mll.id';

        $rows = $g_ado_db->GetAll($queryStr);
        if (is_array($rows)) {
            $p_count = count($rows);
            if ($mc_limit) {
                $rows = array_slice($rows, $mc_start, $mc_limit);
            }
            if ($mc_start && (!$mc_limit)) {
                $rows = array_slice($rows, $mc_start);
            }
        }

        $mm_objs = array();

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
                $tmpPoint['image_src'] = '';
                $tmpPoint['image_width'] = '';
                $tmpPoint['image_height'] = '';

                $tmp_image = null;
                $tmp_video = null;

                if ($row['image_mm']) {
                    $multimedia_spec = $row['image_mm'];
                    $multimedia_link = $multimedia_spec;

                    // the dynamic maps (i.e. with mc_mapCons) have grouping by multimedia,
                    // while article maps have all multimedia separated (and read with concat)
                    if (!$mc_mapCons) {
                        $multimedia_spec_arr = explode('-', $multimedia_spec);
                        if (2 == count($multimedia_spec_arr)) {
                            $multimedia_spec = 0 + $multimedia_spec_arr[0];
                            $multimedia_link = 0 + $multimedia_spec_arr[1];
                        }
                    }

                    $tmpPoint['image_mm'] = $multimedia_link;

                    $tmp_image = new Geo_Multimedia($multimedia_spec);
                    if ($tmp_image) {
                        $tmpPoint['image_src'] = $tmp_image->getSrc();
                        $tmpPoint['image_width'] = $tmp_image->getWidth();
                        $tmpPoint['image_height'] = $tmp_image->getHeight();
                    }
                }

                $tmpPoint['video_mm'] = 0;
                $tmpPoint['video_id'] = '';
                $tmpPoint['video_type'] = '';
                $tmpPoint['video_width'] = '';
                $tmpPoint['video_height'] = '';

                if ($row['video_mm']) {
                    $multimedia_spec = $row['video_mm'];
                    $multimedia_link = $multimedia_spec;
                    // the dynamic maps (i.e. with mc_mapCons) have grouping by multimedia,
                    // while article maps have all multimedia separated (and read with concat)
                    if (!$mc_mapCons) {
                        $multimedia_spec_arr = explode('-', $multimedia_spec);
                        if (2 == count($multimedia_spec_arr)) {
                            $multimedia_spec = 0 + $multimedia_spec_arr[0];
                            $multimedia_link = 0 + $multimedia_spec_arr[1];
                        }
                    }

                    $tmpPoint['video_mm'] = $multimedia_link;

                    $tmp_video = new Geo_Multimedia($multimedia_spec);
                    if ($tmp_video) {
                        $tmpPoint['video_id'] = $tmp_video->getSrc();
                        $tmpPoint['video_type'] = $tmp_video->getSpec();
                        $tmpPoint['video_width'] = $tmp_video->getWidth();
                        $tmpPoint['video_height'] = $tmp_video->getHeight();
                    }
                }

                if ($tmp_image || $tmp_video) {
                    $mm_objs[$row['ml_id']] = array('image' => $tmp_image, 'video' => $tmp_video);
                }

                // for the list-of-objects array
                $tmpPoint['map_id'] = $ps_mapId;
                $tmpPoint['art_number'] = 0;
                $tmpPoint['art_numbers'] = '';
                if ($mc_mapCons) {
                    $tmpPoint['map_id'] = $row['m_id'];
                    $tmpPoint['art_number'] = $row['art_number'];
                    $tmpPoint['art_numbers'] = $row['art_numbers'];
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

            }
        }

        if (0 == count($dataArray)) {return array();}

        $dataArray_tmp = $dataArray;
        $objsArray = array();
        $dataArray = array();

        foreach ($dataArray_tmp as $one_poi)
        {
            {
                $one_poi_source = array(
                    'id' => $one_poi['loc_id'],
                    'fk_map_id' => $one_poi['map_id'],
                    'fk_location_id' => $one_poi['geo_id'],
                    'poi_style' => $one_poi['style'],
                    'rank' => $one_poi['rank'],
                );
                $one_poi_obj = new self($one_poi_source, true);
    
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
                $one_poi_obj->location = new Geo_Location($one_geo_source, true);
    
                $one_lan_source = array(
                    'id' => $one_poi['con_id'],
                    'fk_maplocation_id' => $one_poi['loc_id'],
                    'fk_language_id' => $ps_languageId,
                    'fk_content_id' => $one_poi['txt_id'],
                    'poi_display' => $one_poi['display'],
                );
                $one_poi_obj->setLanguage($ps_languageId, new Geo_MapLocationLanguage(NULL, 0, $one_geo_source, true));
    
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
                $one_poi_obj->setContent($ps_languageId, new Geo_MapLocationContent(NULL, NULL, $one_txt_source, true));

                if(array_key_exists($one_poi['loc_id'], $mm_objs)) {
                    $poi_mm = $mm_objs[$one_poi['loc_id']];
                    $one_poi_obj->multimedia = array();
                    foreach ($poi_mm as $one_mm) {
                        if ($one_mm) {
                            $one_poi_obj->multimedia[] = $one_mm;
                        }
                    }
                }

                $objsArray[] = $one_poi_obj;
            }

            if ((!$p_skipCache) || ($ps_saveArray)) {
                $dataArray[] = $one_poi;
            }
        }

        if ((!$p_skipCache) && CampCache::IsEnabled()) {
            $cacheList_arr->storeInCache(array('count' => $p_count, 'data' => $dataArray));
            $cacheList_obj->storeInCache(array('count' => $p_count, 'data' => $objsArray));
        }

        if ($ps_saveArray) {
            $p_rawData = $dataArray;
        }

        return $objsArray;

    } // fn GetListExt

    /**
     * Returns SQL query for limiting POIs on a given polygon
     *
     * @param mixed $p_coordinates
     *    An array of coordinate lon/lat pairs
     * @param string $p_polygonType
     *    Polygon type: rectangle (two corners), or polygons with clockwise or counterclockwise corners
     * @param string $p_tableAlias
     *    Table prefix for the SQL query
     *
     * @return mixed
     */
    public static function GetGeoSearchSQLCons($p_coordinates, $p_polygonType = 'rectangle', $p_tableAlias = 'l')
    {
        if (is_object($p_coordinates)) {
            $p_coordinates = get_object_vars($p_coordinates);
        }

        $queryCons = '';
        $paramError = false;

        if (!ctype_alnum($p_tableAlias)) {
            $paramError = true;
            return array('error' => true, 'cons' => '');
        }

        $p_polygonType = strtolower('' . $p_polygonType);
        if (!array_key_exists($p_polygonType, array('rectangle' => 1, 'polygon' => 1))) {
            $paramError = true;
            return array('error' => true, 'cons' => '');
        }

        if ('rectangle' == $p_polygonType)
        {
            $queryCons_1 = '';
            $queryCons_2 = '';

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

            $left_lon = '' . $loc_left['longitude'];
            $left_lat = '' . $loc_left['latitude'];
            $right_lon = '' . $loc_right['longitude'];
            $right_lat = '' . $loc_right['latitude'];
    
            if (!is_numeric($left_lon)) {$left_lon = '0';}
            if (!is_numeric($left_lat)) {$left_lat = '0';}
            if (!is_numeric($right_lon)) {$right_lon = '0';}
            if (!is_numeric($right_lat)) {$right_lat = '0';}
    
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
    
            $queryCons = str_replace('%%y0%%', $east_lon, $queryCons);
            $queryCons = str_replace('%%y1%%', $west_lon, $queryCons);
            $queryCons = str_replace('%%x0%%', $south_lat, $queryCons);
            $queryCons = str_replace('%%x1%%', $north_lat, $queryCons);

        }

        if ((0 < count($p_polygonType)) && ('polygon' == $p_polygonType))
        {
            $polygon_spec = '';

            $ind_start = 0;
            $ind_stop = count($p_coordinates) - 1;
            $ind_step = 1;

            $first_corner = $p_coordinates[$ind_start];
            if (is_object($first_corner)) {
                $first_corner = get_object_vars($first_corner);
            }

            $first_lon = $first_corner['longitude'];
            $first_lat = $first_corner['latitude'];

            for ($ind = $ind_start; ; $ind += $ind_step) {

                $corner = $p_coordinates[$ind];
                if (is_object($corner)) {
                    $corner = get_object_vars($corner);
                }

                $one_lon = $corner['longitude'];
                $one_lat = $corner['latitude'];
                if ((!is_numeric($one_lon)) || (!is_numeric($one_lat))) {
                    $paramError = true;
                    break;
                }

                $polygon_spec .= "$one_lat $one_lon,";

                if ($ind == $ind_stop) {break;}
            }
            $polygon_spec .= "$first_lat $first_lon";

            $queryCons .= "Intersects(GeomFromText('Polygon(($polygon_spec))'),$p_tableAlias.poi_location) ";

        }

        if ($paramError) {
            return array('error' => true, 'cons' => '');
        }

        return array('error' => false, 'cons' => $queryCons);
    } // fn GetGeoSearchSQLCons

    /**
     * Returns SQL query for limiting POIs on a given polygon by a previously defined stored function
     *
     * @param mixed $p_coordinates
     *    An array of coordinate lon/lat pairs
     * @param string $p_tableAlias
     *    Table prefix for the SQL query
     *
     * @return mixed
     */
    public static function GetGeoSearchPointInPolygon($p_coordinates, $p_tableAlias = 'l')
    {
        if (is_object($p_coordinates)) {
            $p_coordinates = get_object_vars($p_coordinates);
        }

        $paramError = false;

        // reasonable limit is +/-180 (used as lat/lon in degrees), but let it work even for intentionally larger (say +-1000 spaced) polygons
        $min_val = -9000;
        $max_val = 9000;

        $p_count = count($p_coordinates);
        $sql_part = 'CheckPolygonPoint(' . $p_tableAlias . '.poi_location, 20, ' . $p_count . ', "';

        if (1000 < $p_count) {
            $paramError = true;
        }

        if (!$paramError) {
            foreach ($p_coordinates as $corner) {
                if (is_object($corner)) {
                    $corner = get_object_vars($corner);
                }
    
                $one_lon = $corner['longitude'];
                $one_lat = $corner['latitude'];
                if ((!is_numeric($one_lon)) || (!is_numeric($one_lat))) {
                    $paramError = true;
                    break;
                }
    
                $one_lon = 0 + $one_lon;
                $one_lat = 0 + $one_lat;
    
                $correct = true;
                if (($one_lon < $min_val) || ($one_lon > $max_val)) {
                    $correct = false;
                }
                if (($one_lat < $min_val) || ($one_lat > $max_val)) {
                    $correct = false;
                }
                if (!$correct) {
                    $paramError = true;
                    break;
                }
    
                $lat_part = str_pad(trim(substr(sprintf('%1.13f', $one_lat), 0, 19)) . ',', 20, ' ');
                $lon_part = str_pad(trim(substr(sprintf('%1.13f', $one_lon), 0, 19)) . ',', 20, ' ');
    
                $sql_part .= $lat_part . $lon_part;
    
            }
        }

        $sql_part .= '")';

        if ($paramError) {
            $sql_part = '1';
        }

        return array('error' => $paramError, 'cons' => $sql_part);

    } // fn GetGeoSearchPointInPolygon

} // class Geo_MapLocation


