<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/Widget.php';

/**
 * Area class
 */
class Extension_Area extends DatabaseObject
{
    /** @var string */
    public $m_dbTableName = 'extension_area';

    /** @var string */
    public $m_keyColumnNames = array('name');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'name',
    );

    /** @var array */
    private $widgets = NULL;

    /**
     * @param string $name
     */
    public function __construct($name = '')
    {
        parent::__construct($this->m_columnNames);
        $this->m_data['name'] = $name;
        if (!empty($name)) {
            $this->fetch();
        }
    }

    /**
     * Get id.
     * @return int
     */
    public function getId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * Get name.
     * @return string
     */
    public function getName()
    {
        return (string) $this->m_data['name'];
    }

    /**
     * Get type.
     * @return string
     */
    public function getType()
    {
        return $this->getId() == 0 ? 'preview' : 'default';
    }

    /**
     * Get widgets in area.
     * @return array of Extension_IWidget */
    public function getWidgets()
    {
        if ($this->widgets === NULL) {
            $this->widgets = Extension_Widget::GetByArea($this);
        }

        return $this->widgets;
    }

    /**
     * Render widgets.
     * @param string $type
     * @return void
     */
    public function render($type = 'default')
    {
        echo '<ul id="', $this->getName(), '" class="area">';
        foreach ($this->getWidgets() as $widget) {
            $widget->render($type);
        }
        echo '</ul>';
    }

    /**
     * Save widgets in area.
     * @param string $area
     * @param array $widgets
     * @return bool
     */
    public static function SaveWidgets($area = '', $widgets = array())
    {
        global $g_user, $g_ado_db;

        $area = new Extension_Area($area);
        foreach (self::getIds($widgets) as $order => $id) {
            $queryStr = 'UPDATE extension_area_widget
                SET fk_area_id = ' . $area->getId() . ',
                    `order` = ' . $order . '
                WHERE fk_widget_id = ' . $id . '
                    AND fk_user_id = ' . $g_user->getUserId();
            $g_ado_db->execute($queryStr);
            if ($g_ado_db->Affected_Rows() <= 0 && $area->getId() > 0) { // not set
                $queryStr = sprintf('INSERT INTO extension_area_widget
                    (fk_area_id, fk_widget_id, fk_user_id, `order`) VALUES
                    (%d, %d, %d, %d)',
                    $area->getId(),
                    $id,
                    $g_user->getUserId(),
                    $order);
                $g_ado_db->execute($queryStr);
            }
        }

        return TRUE;
    }

    /**
     * Get json widgets ids.
     * @param array $widgets
     * @return array of int
     */
    private static function getIds(array $widgets = NULL)
    {
        $ids = array();
        foreach ((array) $widgets as $widget) {
            list(, $id) = explode('_', $widget);
            $ids[] = $id;
        }
        return $ids;
    }
}
