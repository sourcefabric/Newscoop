<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * List component
 */
class BaseList
{
    /** @var string */
    protected $id = '';

    /** @var string */
    protected $web = '';

    /** @var string */
    protected $admin = '';

    /** @var bool */
    protected $search = FALSE;

    /** @var array */
    protected $searchCols = array();

    /** @var array */
    protected $cols = array();

    /** @var array */
    protected $ignoredCols = array();

    /** @var bool */
    protected $colVis = FALSE;

    /** @var mixed */
    protected $model = NULL;

    /** @var bool */
    protected $order = FALSE;

    /** @var array */
    protected $hidden = array();

    /** @var string */
    protected $defaultSorting = NULL;

    /** @var string */
    protected $defaultSortingDir = 'asc';

    /** @var array */
    protected $notSortable = array();

    /** @var array */
    protected $items = NULL;

    /** @var bool */
    protected $beforeRender = FALSE;

    /** @var bool */
    protected static $renderTable = FALSE;

    /** @var int */
    protected $inUseColumn = NULL;

    /** @var bool */
    protected $clickable = TRUE;

    /**
     */
    public function __construct()
    {
        global $Campsite, $ADMIN;

        // set paths
        $this->web = $Campsite['WEBSITE_URL'];
        $this->path = $this->web . '/admin/libs/ArticleList';

        camp_load_translation_strings('articles');
        camp_load_translation_strings('library');

        $this->id = substr(sha1(get_class($this)), -6);
    }

    /**
     * Get list id
     * @return string
     */
    public function getId()
    {
        return (string) $this->id;
    }

    /**
     * Get sDom property.
     * @return string
     */
    public function getSDom()
    {
        $colvis = $this->colVis ? 'C' : '';
        $search = $this->search ? 'f' : '';
        $paging = $this->items === NULL ? 'ip' : 'i';
        return sprintf('<"H"%s%s%s>t<"F"%s%s>',
            $colvis,
            $search,
            $paging,
            $paging,
            $this->items === NULL ? 'l' : ''
        );
    }

    /**
     * Get default sorting
     * @return string
     */
    public function getSorting()
    {
        return json_encode(array(
            (int) $this->defaultSorting,
            $this->defaultSortingDir,
        ));
    }

    /**
     * Set search.
     * @param bool $search
     * @return ArticleList
     */
    public function setSearch($search = FALSE)
    {
        $this->search = (bool) $search;
        return $this;
    }

    /**
     * Set links clickable
     * @param bool $clickable
     * @return BaseList
     */
    public function setClickable($clickable = TRUE)
    {
        $this->clickable = $clickable;
        return $this;
    }

    /**
     * Set ColVis.
     * @param bool $colVis
     * @return ArticleList
     */
    public function setColVis($colVis = FALSE)
    {
        $this->colVis = (bool) $colVis;
        return $this;
    }

    /**
     * Set order.
     * @param bool $order
     * @return ArticleList
     */
    public function setOrder($order = FALSE)
    {
        $this->order = (bool) $order;
        return $this;
    }

    /**
     * Set column to be hidden.
     * @param int|string $key
     * @return ArticleList
     */
    public function setHidden($key)
    {
        if (is_int($key)) {
            $this->hidden[] = (int) $key;
        } else {
            foreach(array_keys($this->cols) as $id => $val) {
                if ($key == $val) {
                    $this->hidden[] = $id;
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * Set items.
     * @param array $items
     * @return ArticleList
     */
    public function setItems($items)
    {
        if (isset($items[0]) && is_array($items[0])) {
            $items = $items[0];
        }
        $this->items = array();
        foreach ((array) $items as $item) {
            $this->items[] = $this->processItem($item);
        }
        return $this;
    }

    /**
     * Process item
     * @param mixed $item
     * @return mixed
     */
    public function processItem($item)
    {
        return array_values($item); // to be overriden in subclasses
    }

    /**
     * Get function arguments
     * @return array
     */
    final protected function getArgs()
    {
        $args = array();
        foreach ($_POST['args'] as $arg) {
            $args[$arg['name']] = $arg['value'];
        }
        return $args;
    }

    /**
     * Get path for file - try in subclass folder, fallback in baseclass folder
     * @param string $filename
     * @return string|NULL
     */
    final protected function getPath($filename)
    {
        $reflector = new ReflectionObject($this);
        $paths = array(
            dirname($reflector->getFileName()) . '/' . ((string) $filename),
            dirname(__FILE__) . '/' . ((string) $filename),
        );
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        return NULL;
    }


    /**
     * Data provider
     * @return array
     */
    public function doData()
    {
        global $g_ado_db;

        if (!isset($this->model)) {
            return array();
        }

        // get args
        $aoData = $this->getArgs();

        // order
        $dbCols = array_keys($this->cols);
        $order = array();
        for ($i = 0; $i < (int) $aoData['iSortingCols']; $i++) {
            $order[] = sprintf('%s %s',
                $dbCols[$aoData["iSortCol_$i"]],
                $aoData["sSortDir_$i"]);
        }

        // select columns
        $cols = array_diff(array_keys($this->cols), $this->ignoredCols);
        $queryStr = 'SELECT ' . implode(', ', $cols) . '
            FROM ' . $this->model->m_dbTableName;

        // set search
        if (!empty($aoData['sSearch'])) {
            $search = array();
            foreach ($this->searchCols as $col) {
                $search[] = sprintf('%s LIKE "%%%s%%"', $col,
                    mysql_real_escape_string($aoData['sSearch']));
            }
            $queryStr .= ' WHERE ' . implode(' OR ', $search);
        }

        // get filtered count (before ordering and limiting)
        $totalDisplayRecords = $this->getCount($queryStr);

        // set order
        if (!empty($order)) {
            $queryStr .= ' ORDER BY ' . implode(', ', $order);
        }

        // add limit
        $queryStr .= sprintf(' LIMIT %d,%d',
            $aoData['iDisplayStart'],
            $aoData['iDisplayLength']);

        $items = array();
        $rows = (array) $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $items[] = $this->processRow((array) $row);
        }

        return array(
            'iTotalRecords' => $this->getCount(),
            'iTotalDisplayRecords' => $totalDisplayRecords,
            'sEcho' => (int) $aoData['sEcho'],
            'aaData' => $items,
        );
    }

    /**
     * Get db rows count
     * @param string $from
     * @return int
     */
    public function getCount($from = NULL)
    {
        global $g_ado_db;

        $queryStr = 'SELECT COUNT(*)
            FROM ' . (isset($from) ? "($from) f" : $this->model->m_dbTableName);

        return (int) $g_ado_db->GetOne($queryStr);
    }

    /**
     * Process db row
     * @param array $row
     * @return array
     */
    public function processRow(array $row)
    {
        return array_values($row);
    }

    /**
     * Renders list id
     * @return void
     */
    public function beforeRender()
    {
        if (!$this->beforeRender) {
            echo '<div id="smartlist-' . $this->id . '" class="smartlist">';
        }
        $this->beforeRender = TRUE;
    }

    /**
     * Render table.
     * @return ArticleList
     */
    public function render()
    {
        $this->beforeRender();

        include $this->getPath('table.php');
        self::$renderTable = TRUE;
        echo '</div><!-- /#list-' . $this->id . ' -->';
        return $this;
    }

    /**
     * Handle delete
     * @param array $ids
     * @return bool
     */
    public function doDelete($ids)
    {
        $class = get_class($this->model);

        foreach ((array) $ids as $id) {
            $object = new $class($id);
            $object->delete();
        }

        return TRUE;
    }

    /**
     * Get human readable filesize
     * @credits joaoptm [http://php.net/manual/en/function.filesize.php]
     * @param int $size
     * @return string
     */
    public static function FormatFileSize($size)
    {
        static $units = array(' B', ' KB', ' MB', ' GB', ' TB');

        $size = (int) $size;
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $units[$i];
    }
}
