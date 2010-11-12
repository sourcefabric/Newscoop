<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/../DatabaseObject.php';
require_once dirname(__FILE__) . '/IWidget.php';

/**
 * Widget manager decorator class
 */
abstract class WidgetManagerDecorator extends DatabaseObject implements IWidget
{
    /** @var string */
    public $m_dbTableName = 'widget';

    /** @var array */
    public $m_keyColumnNames = array('path', 'class');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'path',
        'class',
    );

    /** @var IWidget */
    protected $widget = NULL;

    /** @var array */
    private $meta = NULL;

    /**
     * @param IWidget $widget
     * @param array $data
     */
    public function __construct(IWidget $widget, array $data = array())
    {
        $this->widget = $widget;
        $this->m_data = $data;

        if (!empty($data) && empty($data['id'])) {
            $this->fetch(); // load id
        }
    }

    /**
     * Get author
     */
    public function getAuthor()
    {
        return $this->getMeta('author');
    }

    /**
     * Get class name.
     * @return string
     */
    public function getClass()
    {
        if (empty($this->m_data['class'])) {
            $this->m_data['class'] = get_class($this->widget);
        }

        return $this->m_data['class'];
    }

    /**
     * Get description
     * @return string
     */
    public function getDescription()
    {
        return $this->getMeta('description');
    }

    /**
     * Get homepage
     * @return string
     */
    public function getHomepage()
    {
        return $this->getMeta('homepage');
    }

    /**
     * Get widget id.
     * @param bool $generate
     * @return int
     */
    public function getId($generate = FALSE)
    {
        if (empty($this->m_data['id']) && $generate) {
            $this->save();
            $this->fetch(); // load id
        } else if (!isset($this->m_data['id'])) {
            $this->m_data['id'] = 0;
        }

        return (int) $this->m_data['id'];
    }

    /**
     * Get widget file path
     * @return string
     */
    public function getPath()
    {
        if (empty($this->m_data['path'])) {
            $reflector = new ReflectionObject($this->widget);
            $this->m_data['path'] = $reflector->getFileName();
        }

        return (string) $this->m_data['path'];
    }

    /**
     * Get title.
     * @return string
     */
    public function getTitle()
    {
        if (method_exists($this->widget, 'getTitle')) {
            return $this->widget->getTitle();
        }
        return $this->getMeta('name');
    }

    /**
     * Get version
     * @return string
     */
    public function getVersion()
    {
        return $this->getMeta('version');
    }

    /**
     * Save data
     * @param array $p_columns
     * @return void
     */
    public function save($p_columns = NULL)
    {
        // init vals
        $this->getPath();
        $this->getClass();

        if ($this->getId() == 0) {
            parent::create($p_columns);
        } else {
            parent::update($p_columns);
        }
    }

    /**
     * Get metadata
     * @param string $key
     * @return string
     */
    private function getMeta($key)
    {
        if ($this->meta === NULL) {
            $this->meta = array();

            // load ini
            $dirname = dirname($this->getPath());
            $inifile = $dirname . '/' . basename($dirname) . '.ini';
            if (file_exists($inifile)) {
                $this->meta = parse_ini_file($inifile, TRUE);
                if (!empty($this->meta[$this->getClass()])) {
                    foreach ($this->meta[$this->getClass()] as $k => $v) {
                        $this->meta[$k] = $v;
                    }
                }
            }
        }

        return empty($this->meta[$key]) ? '' : (string) $this->meta[$key];
    }
}
