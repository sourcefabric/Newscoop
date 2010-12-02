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
require_once dirname(__FILE__) . '/Widget.php';
require_once dirname(__FILE__) . '/WidgetContext.php';
require_once dirname(__FILE__) . '/WidgetRendererDecorator.php';
require_once dirname(__FILE__) . '/Index.php';
require_once dirname(__FILE__) . '/FeedWidget.php';

/**
 * Widget Manager
 */
class WidgetManager
{
    /**
     * Get available widgets for user
     * @return array of IWidget
     */
    public static function GetAvailable()
    {
        global $g_ado_db, $g_user;

        // get all widget extensions
        $index = new Extension_Index();
        $extensions = $index->addDirectory(WWW_DIR . '/extensions')
            ->find('IWidget');

        // filter not-available (used)
        $widgets = array();
        foreach ($extensions as $extension) {
            $widget = WidgetManagerDecorator::GetByExtension($extension);
            if ($widget->isAvailable()) {
                $widgets[] = $widget;
            }
        }
        return $widgets;
    }

    /**
     * Get widgets by context.
     * @param IWidgetContext $context
     * @return array of IWidget
     */
    public static function GetWidgetsByContext(IWidgetContext $context)
    {
        global $g_user, $g_ado_db;

        $queryStr = 'SELECT w.path, w.class, wcw.*
            FROM ' . Extension_Extension::TABLE . ' w
                INNER JOIN ' . WidgetManagerDecorator::TABLE . ' wcw
                ON w.id = wcw.fk_widget_id
            WHERE wcw.fk_user_id = ' . $g_user->getUserId() . '
                AND wcw.fk_widgetcontext_id = ' . $context->getId() . '
            ORDER BY `order`';
        $rows = $g_ado_db->GetAll($queryStr);

        $widgets = array();
        foreach ($rows as $row) {
            $widgets[] = new WidgetRendererDecorator((array) $row);
        }
        return $widgets;
    }

    /**
     * Add widget to user dashboard
     * @param int $widgetId
     * @param string $contextName
     * @return bool
     */
    public static function AddWidget($widgetId, $contextName)
    {
        global $g_ado_db, $g_user;

        // get context object
        $context = new WidgetContext($contextName);

        $id = 'w' . substr(sha1(uniqid() . $g_user->getUserId()), -12);
        $widget = new WidgetManagerDecorator(array(
            'id' => $id,
            'fk_widget_id' => (int) $widgetId,
            'fk_widgetcontext_id' => $context->getId(),
            'fk_user_id' => $g_user->getUserId(),
            'order' => 'MIN(`order`) - 1',
        ));
        $widget->create();
        return $id;
    }
}
