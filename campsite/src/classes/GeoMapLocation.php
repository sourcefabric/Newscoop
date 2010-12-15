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

	/**
     * @param mixed $arg
	 */
	public function __construct($arg)
	{
        parent::__construct($this->m_columnNames);

        if (is_array($arg)) {
            $this->m_data = $arg;
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
     * @return IGeoMapLocationContent
     */
    public function getContent($language)
    {
        $language = (int) $language;
        if (!isset($this->contents[$language])) {
            $this->contents[$language] = new Geo_MapLocationContent($this, $language);
        }
        return $this->contents[$language];
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
        $contentLanguage = new Geo_MapLocationLanguage($this, $language);
        return $contentLanguage->isEnabled();
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
            $tmpLoc->getColumnNames(true));
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

        // sets the ORDER BY condition
        $order = self::ProcessListOrder($p_order);
        foreach ($order as $orderDesc) {
            $orderColumn = $orderDesc['field'];
            $orderDirection = $orderDesc['dir'];
            $selectClauseObj->addOrderBy($orderColumn . ' ' . $orderDirection);
        }

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

        	// builds the array of image objects
        	foreach ($rows as $row) {
                $list[] = new self((array) $row);
        	}
        }

        return $list;
    }

    /**
     * Processes an order directive coming from template tags.
     *
     * @param array $p_order
     *      The array of order directives in the format:
     *      array('field'=>field_name, 'dir'=>order_direction)
     *      field_name can take one of the following values:
     *        bydescription, byphotographer, bydate, bylastdate
     *      order_direction can take one of the following values:
     *        asc, desc
     *
     * @return array
     *      The array containing processed values of the condition
     */
    private static function ProcessListOrder(array $p_order)
    {
        $order = array();
        foreach ($p_order as $orderDesc) {
            $field = $orderDesc['field'];
            $direction = $orderDesc['dir'];
            $dbField = null;
            switch (strtolower($field)) {
            	case 'default':
                    $dbField = self::TABLE . '.rank';
                    break;
            }

            if (!is_null($dbField)) {
                $direction = !empty($direction) ? $direction : 'asc';
                $order[] = array(
                    'field' => $dbField,
                    'dir' => $direction,
                );
            }
        }
        return $order;
    }
}
