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

/**
 * Extension
 */
class Extension_Extension extends DatabaseObject
{
    const TABLE = 'Widget';

    /** @var string */
    public $m_dbTableName = self::TABLE;

    /** @var array */
    public $m_keyColumnNames = array('path', 'class');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'path',
        'class',
    );

    /** @var string */
    private $interface = '';

    /**
     * @param string $interface
     * @param string $class
     */
    public function __construct($class, $path, $interface = '')
    {
        $this->interface = $interface;
        $this->m_data = array(
            'id' => NULL,
            'class' => (string) $class,
            'path' => (string) $path,
        );
    }

    /**
     * Get class name
     * @return string
     */
    public function getClass()
    {
        return (string) $this->m_data['class'];
    }

    /**
     * Get path
     * @return string
     */
    public function getPath()
    {
        $cur_path = $this->m_data['path'];

        if (0 === strpos($cur_path, WWW_DIR)) {
            return $cur_path;
        }

        foreach (WidgetManager::ExtPath() as $path) {
            if (file_exists($file = WWW_DIR . $path . $this->m_data['path'])) {
                return $file;
            }
        }
    }

    /**
     * Get id
     * @return int
     */
    public function getId()
    {
        if ($this->m_data['id'] === NULL) {
            $this->fetch();
            if (empty($this->m_data['id'])) {
                $this->create();
                $this->fetch();
            }
        }
        return (int) $this->m_data['id'];
    }

    /**
     * Get instance
     * @return IWidget|NULL
     */
    public function getInstance()
    {
        $file = $this->getPath();
        if (!file_exists($file)) {
            return NULL;
        }

        require_once $file;
        $class = $this->getClass();
        return new $class;
    }

    /**
     * Has class interface?
     * @param string $interface
     * @return bool
     */
    public function hasInterface($interface)
    {
        return $interface === $this->interface;
    }

    /**
     * Get Extension by id
     * @param int $id
     * @return Extension_Extension
     */
    public static function GetById($id)
    {
        global $g_ado_db;

        $queryStr = 'SELECT *
            FROM ' . self::TABLE . '
            WHERE id = ' . ((int) $id);
        $row = $g_ado_db->GetRow($queryStr);
        return new self($row['class'], $row['path']);
    }
}
