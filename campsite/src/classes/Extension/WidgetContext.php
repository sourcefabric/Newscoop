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
require_once dirname(__FILE__) . '/IWidgetContext.php';
require_once dirname(__FILE__) . '/WidgetManager.php';

/**
 * Widget Context
 */
class WidgetContext extends DatabaseObject implements IWidgetContext
{
    /** @var string */
    public $m_dbTableName = 'widgetcontext';

    /** @var string */
    public $m_keyColumnNames = array('name');

    /** @var array */
    public $m_columnNames = array(
        'id',
        'name',
    );

    /** @var bool */
    private $isHorizontal = FALSE;

    /** @var bool */
    private $isVertical = FALSE;

    /** @var array of IWidget */
    private $widgets = NULL;

    /**
     * @param string $name
     */
    public function __construct($name = NULL)
    {
        parent::__construct($this->m_columnNames);

        if ($name === NULL) {
            $name = self::DEFAULT_NAME;
        } else {
            $name = strtolower($name);
        }

        $this->m_data['name'] = $name;
        if ($name != self::DEFAULT_NAME) { // load context id
            $this->fetch();
            if (empty($this->m_data['id'])) { // store new context
                $this->create(array(
                    'name' => $name,
                ));
            }
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
     * Get context widgets.
     * @return array of IWidget
     */
    public function getWidgets()
    {
        if ($this->widgets === NULL) {
            $this->widgets = WidgetManager::GetWidgetsByContext($this);
        }
        return $this->widgets;
    }

    /**
     * Is default?
     * @return bool
     */
    public function isDefault()
    {
        return $this->getName() == self::DEFAULT_NAME;
    }

    /**
     * Set horizontal
     * @param bool $horizontal
     * @return IWidgetContext
     */
    public function setHorizontal($horizontal = TRUE)
    {
        $this->isHorizontal = (bool) $horizontal;
        return $this;
    }

    /**
     * Is horizontal?
     * @return bool
     */
    public function isHorizontal()
    {
        return $this->isHorizontal;
    }

    /**
     * Set vertical
     * @param bool $vertical
     * @return IWidgetContext
     */
    public function setVertical($vertical = TRUE)
    {
        $this->isVertical = (bool) $vertical;
        return $this;
    }

    /**
     * Is vertical?
     * @return bool
     */
    public function isVertical()
    {
        return $this->isVertical;
    }

    /**
     * Render context.
     * @return void
     */
    public function render()
    {
        $classes = array('context');

        if ($this->isHorizontal()) {
            $classes[] = 'horizontal';
        }

        if ($this->isVertical()) {
            $classes[] = 'vertical';
        }

        echo '<ul id="', $this->getName(), '" class="', implode(' ', $classes), '">', "\n";
        foreach ($this->getWidgets() as $widget) {
            $widget->render($this);
        }
        echo '</ul>';
    }
}
