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

    /** @var bool */
    protected $colVis = FALSE;

    /** @var bool */
    protected $order = FALSE;

    /** @var array */
    protected $hidden = array();

    /** @var array */
    protected $items = NULL;

    /** @var bool */
    protected static $renderTable = FALSE;

    /**
     * @param bool $quiet
     */
    public function __construct($quiet = FALSE)
    {
        global $Campsite, $ADMIN;

        // set paths
        $this->web = $Campsite['WEBSITE_URL'];
        $this->path = $this->web . '/admin/libs/ArticleList';

        camp_load_translation_strings('articles');
        camp_load_translation_strings('universal_list');

        $this->id = substr(sha1((string) mt_rand()), -6);

        if (!$quiet) {
            echo '<div id="smartlist-' . $this->id . '" class="smartlist">';
        }
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
     * @param int $id
     * @return ArticleList
     */
    public function setHidden($id)
    {
        $this->hidden[] = (int) $id;
        return $this;
    }

    /**
     * Set items.
     * @param array $items
     * @return ArticleList
     */
    public function setItems($items)
    {
        if (is_array($items[0])) {
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
        return $item; // to be overriden in subclasses
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
     * Render table.
     * @return ArticleList
     */
    public function render()
    {
        include $this->getPath('table.php');
        self::$renderTable = TRUE;
        echo '</div><!-- /#list-' . $this->id . ' -->';
        return $this;
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
}
