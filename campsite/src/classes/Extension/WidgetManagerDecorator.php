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
    public $m_keyColumnNames = array('filename', 'class');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'filename',
        'class',
        'checksum',
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
        parent::__construct($this->m_columnNames);

        $this->widget = $widget;
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

    /**
     * Update widget data
     * @param array $p_columns
     * @return IWidget
     */
    public function update($p_columns = NULL)
    {
        if ($this->getId() == 0) { // get id
            $this->fetch();
        }

        parent::update($p_columns);
    }
}
