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
class WidgetManagerDecorator extends DatabaseObject
{
    const TABLE = 'WidgetContext_Widget';

    /** @var string */
    public $m_dbTableName = self::TABLE;

    /** @var array */
    public $m_keyColumnNames = array('id', 'fk_user_id');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'fk_widgetcontext_id',
        'fk_widget_id',
        'fk_user_id',
        'order',
        'settings',
    );

    /** @var IWidget */
    protected $widget = NULL;

    /** @var Extension_Extension */
    private $extension = NULL;

    /** @var array */
    private $meta = NULL;

    /** @var array */
    private $settings = NULL;

    /**
     * @param string|array $args
     */
    public function __construct($args = NULL)
    {
        global $g_user;

        if (is_array($args)) {
            $this->m_data = $args;
            if (!empty($args['path']) && !empty($args['class'])) {
                $this->extension = new Extension_Extension($args['class'], $args['path']);
                $this->getWidget();
            }
        } else {
            parent::__construct($this->m_columnNames);
            $this->m_data['id'] = (string) $args;
            $this->m_data['fk_user_id'] = $g_user->getId();
        }
    }

    /**
     * Get instance for extension
     * @param Extension_Extension $extension
     * @return integer
     */
    public static function GetByExtension(Extension_Extension $extension)
    {
        global $g_user;

        // be able to render
        $widget = new WidgetRendererDecorator(array(
            'fk_user_id' => $g_user->getId(),
            'fk_widget_id' => $extension->getId(),
        ));

        // set extension, widget
        $widget->extension = $extension;
        $widget->setWidget($extension->getInstance());
        return $widget;
    }

    /**
     * Get extension.
     * @return Extension_Extension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Get widget id
     * @return string
     */
    public function getId()
    {
        return (string) $this->m_data['id'];
    }

    /**
     * Update data
     * @param array $p_columns
     * @return bool
     */
    public function update($p_columns = NULL)
    {
        // encode settings
        if (!empty($p_columns['settings'])) {
            $p_columns['settings'] = json_encode($p_columns['settings']);
        }

        parent::update($p_columns);
        return TRUE;
    }

    /**
     * Get metadata
     * @param string $key
     * @return string
     */
    public function getMeta($key)
    {
        if ($this->meta === NULL) {
            $this->meta = array();

            // load ini
            $dirname = dirname(realpath($this->extension->getPath()));
            $inifile = $dirname . '/' . basename($dirname) . '.ini';
            if (file_exists($inifile)) {
                $this->meta = parse_ini_file($inifile);
            }
        }

        return empty($this->meta[$key]) ? '' : (string) $this->meta[$key];
    }

    /**
     * Get widget instance
     */
    public function getWidget()
    {
        if ($this->widget === NULL) {
            if (!isset($this->extension)) {
                if (empty($this->m_data['fk_widget_id'])) {
                    $this->fetch();
                }
                $this->extension = Extension_Extension::GetById($this->m_data['fk_widget_id']);
            }

            $this->widget = $this->extension->getInstance();
            if ($this->widget === NULL) {
                return $this->widget;
            }

            $this->widget->setManager($this);
            if (!empty($this->m_data['settings'])) {
                $settings = json_decode($this->m_data['settings']);
                $this->widget->setSettings((array) $settings);
            }
        }
        return $this->widget;
    }

    /**
     * Is available?
     * @param int $uid
     * @return bool
     */
    public function isAvailable($uid)
    {
        global $g_user, $g_ado_db;

        if ($this->getWidget()->getAnnotation('multi') !== NULL) {
            return TRUE;
        }

        // get used widgets per user
        static $used = NULL;
        if ($used === NULL) { 
            $queryStr = 'SELECT id, fk_widget_id
                FROM ' . self::TABLE . '
                WHERE fk_user_id = ' . ((int) $uid);
            $rows = $g_ado_db->GetAll($queryStr);
            if (!is_array($rows)) {
                $rows = array();
            }

            $used = array();
            foreach ($rows as $row) {
                $used[$row['fk_widget_id']] = $row['id'];
            }
        }

        // checkout if it's used
        return empty($used[$this->getExtension()->getId()]);
    }

    /**
     * Get widget setting
     * @param string $p_setting
     * @return mixed
     */
    public function getSetting($p_setting)
    {
        return $this->getWidget()->getSetting($p_setting);
    }

    /**
     * Calls forwarded to widget
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments) {
        $wdg = $this->getWidget();
        if (!$wdg) {return null;}
            return call_user_func_array(array($wdg, $name), $arguments);
        }
    }
