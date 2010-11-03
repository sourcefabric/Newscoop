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

/**
 * Widget manager decorator class
 */
abstract class WidgetManagerDecorator extends DatabaseObject implements IWidget
{
    /** @var string */
    public $m_dbTableName = 'widget';

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
        'fk_widgetcontext_id',
    );

    /** @var bool */
    public $m_keyIsAutoIncrement = TRUE;

    /** @var IWidget */
    protected $widget = NULL;

    /**
     * @param IWidget $widget
     * @param array $data
     */
    public function __construct(IWidget $widget, array $data = array())
    {
        global $g_user;

        $this->widget = $widget;

        $this->m_tableName = '(widget w LEFT JOIN widgetcontext_widget wcw ON 
        (w.id = wcw.fk_widget_id AND wcw.fk_user_id = ' . $g_user->getUserId() . '))';

        parent::__construct($this->m_columnNames);
        $this->m_data = $data;

        if (empty($data)) {
            $this->fetch();
        }
    }

    /**
     * Get title.
     * @return string|NULL
     */
    public function getTitle()
    {
        return $this->widget->getTitle();
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
     * Get widget filename.
     * @return string
     */
    public function getFilename()
    {
        if (empty($this->m_data['filename'])) {
            $reflection = new ReflectionObject($this->widget);
            $this->m_data['filename'] = $reflection->getFileName();
        }

        return $this->m_data['filename'];
    }

    /**
     * Get widget id.
     * @return int
     */
    public function getId()
    {
        return (int) $this->m_data['id'];
    }

    /**
     * Get system name.
     * @return string
     */
    public function getName()
    {
        return strtolower($this->getClass());
    }
}
