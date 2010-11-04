<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/IWidgetContext.php';
require_once dirname(__FILE__) . '/WidgetManagerDecorator.php';

/**
 * Widget renderer implementation
 */
class WidgetRendererDecorator extends WidgetManagerDecorator
{
    /**
     * Render widget
     * @param IWidgetContext $context
     * @param bool $ajax
     * @return void|string
     */
    public function render(IWidgetContext $context = NULL, $ajax = FALSE)
    {
        // get content
        ob_start();
        if ($context->getId() == 0) {
            echo 'widget preview';
        } else {
            $this->widget->setContext($context);
            $this->widget->render();
        }
        $content = ob_get_contents();
        ob_end_clean();

        if ($ajax) { // return content
            return $content;
        }

        // render widget
        echo '<li id="widget_', $this->getId(), '" class="widget">';
        if ($this->getTitle() !== NULL) {
            echo '<div class="header"><h3>', $this->getTitle(), '</h3></div>';
        }
        echo '<div class="content">';
        echo $content;
        echo '</div></li>';
    }
}
