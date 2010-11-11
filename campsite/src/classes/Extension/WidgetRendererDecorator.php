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
require_once dirname(__FILE__) . '/WidgetManagerDecorator.php';

/**
 * Widget renderer implementation
 */
class WidgetRendererDecorator extends WidgetManagerDecorator
{
    /** @var array */
    private static $metakeys = array(
        'Author', 'Version', 'Homepage',
    );

    /**
     * Render widget
     * @param string $view
     * @param bool $ajax
     * @return void|string
     */
    public function render($view = Widget::DEFAULT_VIEW, $ajax = FALSE)
    {
        // set view if possible
        if (method_exists($this->widget, 'setView')) {
            $this->widget->setView($view);
        }

        // get content
        ob_start();
        $this->widget->render();
        $content = ob_get_contents();
        ob_end_clean();

        if ($ajax) { // return content
            return $content;
        }

        // render whole widget
        echo '<li id="widget_', $this->getId(), '" class="widget">';
        if ($this->getTitle() !== NULL) {
            echo '<div class="header"><h3>', $this->getTitle(), '</h3></div>';
        }
        echo '<div class="content">';
        echo $content;
        echo '</div>', "\n";
        $this->renderMeta();
        echo '</li>', "\n";
    }

    /**
     * Render widget metadata
     * @return void
     */
    public function renderMeta()
    {
        echo '<dl class="meta">', "\n";
        foreach (self::$metakeys as $key) {
            $method = 'get' . $key;
            if (!method_exists($this, $method)) {
                continue;
            }

            $value = $this->$method();
            if (empty($value)) {
                continue;
            }

            echo '<dt>', getGS($key), ':</dt>', "\n";
            echo '<dd>', $value, '</dd>', "\n";
        }
        echo '</dl>', "\n";
    }
}
