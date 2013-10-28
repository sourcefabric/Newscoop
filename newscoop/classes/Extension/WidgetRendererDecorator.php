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
class WidgetRendererDecorator extends WidgetManagerDecorator implements IWidget
{
    /**
     * Render widget
     * @param string $view
     * @param bool $ajax
     * @return void|string
     */
    public function render($view = Widget::DEFAULT_VIEW, $ajax = FALSE)
    {
        $this->getWidget(); // init

        $translator = \Zend_Registry::get('container')->getService('translator');
        // set view if possible
        if (method_exists($this->widget, 'setView')) {
            $this->widget->setView($view);
        }

        if ($ajax) { // render content
            // run beforeRender method
            if (method_exists($this->widget, 'beforeRender')) {
                $this->widget->beforeRender();
            }

            // get content
            ob_start();
            $this->widget->render();
            $content = ob_get_clean();

            return $content;
        }

        // get height from cookie
        $height = 100;
        $heights = isset($_COOKIE['widget_heights']) ? explode('&', $_COOKIE['widget_heights']) : array();
        foreach ($heights as $param) {
            if (empty($param)) {
                break;
            }

            list($wid, $wheight) = explode('=', $param);
            if ($this->getId() == $wid) {
                $height = $wheight;
                break;
            }
        }

        // render widget placeholder
        echo '<li id="', $this->getId(), '" class="widget ui-dialog ui-widget-content ui-corner-all">';
        if ($this->widget->getTitle() !== NULL) {
            echo '<div class="header ui-dialog-titlebar ui-widget-header ui-corner-all ui-helper-clearfix">';
            echo '<h3 class="ui-dialog-title">', $this->widget->getTitle(), '</h3>';
            echo '</div>';
        }
        echo '<div class="content"><div class="scroll ui-dialog-content ui-widget-content">', "\n";
        echo '<div class="loading" style="height:', $height, 'px"><p>', $translator->trans('Loading...', array(), 'api'), '</p></div>';
        echo '</div></div>', "\n";
        echo '<div class="extra">';
        $this->renderMeta();
        $this->renderSettings();
        echo '</div>';
        echo '</li>', "\n";
    }

    /**
     * Render widget metadata
     * @return void
     */
    public function renderMeta()
    {
    	/*
    	 * $translator->trans('Author')
    	 * $translator->trans('Version', array(), 'api')
    	 * $translator->trans('Homepage', array(), 'api')
    	 * $translator->trans('License', array(), 'api')
    	 */
        $meta = array(
            'Author', 'Version', 'Homepage', 'License',
        );

        $translator = \Zend_Registry::get('container')->getService('translator');
        ob_start();
        foreach ($meta as $key) {
            $method = 'get' . $key;
            $value = $this->getWidget()->$method();
            if (empty($value)) {
                continue;
            }

            echo '<dt>' . $translator->trans($key, array(), 'api') . ':</dt>' . "\n";
            echo '<dd>';
            if (preg_match('#^http://#', $value)) { // generate link
                $title = str_replace('http://', '', $value);
                echo '<a href="', $value, '" target="_blank">';
                echo $title, '</a>';
            } else {
                echo $value;
            }
            echo '</dd>', "\n";
        }
        $content = ob_get_contents();
        ob_end_clean();

        if (!empty($content)) {
            echo "<dl class=\"meta\">\n$content\n</dl>";
        }
    }

    /**
     * Render widget settings form
     * @return void
     */
    public function renderSettings()
    {   
        ob_start();
        $translator = \Zend_Registry::get('container')->getService('translator');
        $reflection = new ReflectionObject($this->widget);
        $filter = ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED;
        foreach ($reflection->getProperties($filter) as $property) {
            $doc = $property->getDocComment();
            if (strpos($doc, '@setting') === FALSE) {
                continue;
            }

            // get label
            $matches = array();
            if (preg_match('/@label ([^*]+)/', $doc, $matches)) {
                $label = trim($matches[1]);
            } else {
                $label = $property->getName();
            }

            // generate id
            $id = $reflection->getName() . '-' . $property->getName();
            $id = strtolower($id);

            // value getter
            $method = 'get' . ucfirst($property->getName());

            echo '<dl><dt>';
            echo '<label for="', $id, '">', rtrim($translator->trans($label, array(), 'extensions'), ' (*)'), '</label>';
            echo '</dt><dd>';
            printf('<input id="%s" type="text" name="%s" value="%s" maxlength="255" />',
                $id,
                $property->getName(),
                $this->widget->$method());
            echo '</dd></dl>', "\n";
        }
        $settings = ob_get_clean();

        if (empty($settings)) {
            return;
        }

        echo '<form class="settings" action="" method="">';
        echo '<fieldset>', $settings;
        echo '<input type="submit" value="', $translator->trans('Save'), '" />';
        echo '</fieldset>';
        echo '</form>';
    }
}
