<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/IWidget.php';
require_once dirname(__FILE__) . '/Manager.php';

/**
 * Base class for widgets
 */
abstract class Extension_Widget extends DatabaseObject implements Extension_IWidget
{
    const TABLE = 'extension_widget';

    /** @var string */
    public $m_dbTableName = 'extension_widget';

    /** @var array */
    public $m_keyColumnNames = array('id');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'filename',
        'class',
        'title',
        'order',
        'is_collapsed',
        'fk_area_id',
    );

    /** @var bool */
    public $m_keyIsAutoIncrement = TRUE;

    /**
     * @param int $id
     */
    final public function __construct($id = NULL)
    {
        global $g_user;

        $this->m_tableName = '(extension_widget ew LEFT JOIN extension_area_widget eaw ON 
        (ew.id = eaw.fk_widget_id AND eaw.fk_user_id = ' . $g_user->getUserId() . '))';

        parent::__construct($this->m_columnNames);
        $this->m_data['id'] = $id;
        if ($id !== NULL) {
            $this->fetch();
        }
    }

    /**
     * Get class name.
     * @return string
     */
    final public function getClass()
    {
        if (empty($this->m_data['class'])) {
            $this->m_data['class'] = get_class($this);
        }

        return $this->m_data['class'];
    }

    /**
     * Get widget filename.
     * @return string
     */
    final public function getFilename()
    {
        if (empty($this->m_data['filename'])) {
            $reflection = new ReflectionObject($this);
            $this->m_data['filename'] = $reflection->getFileName();
        }

        return $this->m_data['filename'];
    }

    /**
     * Get widget id.
     * @return int
     */
    final public function getId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * Get system name.
     * @return string
     */
    final public function getName()
    {
        return strtolower(get_class($this));
    }

    /**
     * Render preview for widget.
     * @return void
     */
    public function renderPreview()
    {
        echo '<p>Widget preview</p>';
    }

    /**
     * Render widget.
     * @param string $type
     * @param bool $isAjax
     * @return void
     */
    final public function render($type = 'default', $isAjax = FALSE)
    {
        $renderMethod = 'render' . ucfirst($type);
        if (!method_exists($this, $renderMethod)) {
            $renderMethod = 'renderDefault';
        }

        if ($isAjax) { // render only content
            call_user_func(array($this, $renderMethod));
            return;
        }

        echo '<li id="widget_', $this->getId(), '" class="widget color-green">';
        if ($this->getTitle() != NULL) {
            echo '<div class="widget-head"><h3>', $this->getTitle(), '</h3></div>';
        }
        echo '<div class="widget-content">';
        call_user_func(array($this, $renderMethod));
        echo '</div></li>';
    }

    /**
     * Get widgets for area.
     * @param Extension_Area $area
     * @return array of Extension_IWidget
     */
    public static function GetByArea(Extension_Area $area)
    {
        global $g_ado_db, $g_user;

        // scan
        Extension_Manager::GetExtensions('Extension_IWidget');

        $where = 'fk_area_id = ' . $area->getId();
        if ($area->getId() == 0) {
            $where = 'fk_area_id IS NULL OR fk_area_id = 0';
        }

        $table = '(extension_widget ew LEFT JOIN extension_area_widget eaw ON 
        (ew.id = eaw.fk_widget_id AND eaw.fk_user_id = ' . $g_user->getUserId() . '))';

        $queryStr = "SELECT id, class, filename
            FROM $table
            WHERE $where
            ORDER BY `order`";
        $rows = $g_ado_db->getAll($queryStr);
        if (empty($rows)) {
            return array();
        }

        $widgets = array();
        foreach ((array) $g_ado_db->GetAll($queryStr) as $row) {
            require_once $row['filename'];
            $widgets[] = new $row['class']((int) $row['id']);
        }

        return $widgets;
    }

    /**
     * Get widget content for area.
     * @param string $area
     * @param string $widget
     */
    public static function GetContent($area = '', $widget = '')
    {
        list(,$widget_id) = explode('_', $widget);
        $area = new Extension_Area($area);
        $widget = self::GetById((int) $widget_id);

        ob_start();
        $widget->render($area->getType(), TRUE);
        $content = ob_get_contents();
        ob_clean();

        return $content;
    }

    /**
     * Get widget by id.
     * @param int id
     * @return IWidget
     */
    public static function GetById($id)
    {
        global $g_ado_db;

        // get widget file & class info
        $queryStr = 'SELECT filename, class
            FROM ' . self::TABLE . '
            WHERE id = ' . $id;
        $row = $g_ado_db->getRow($queryStr);

        // return instance
        require_once $row['filename'];
        return new $row['class']();
    }
}
