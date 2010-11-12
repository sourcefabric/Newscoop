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

/**
 * Widget Manager
 */
class WidgetManager
{
    const TABLE = 'widgetcontext_widget';

    /**
     * Get available widgets for specified user
     * @return array of IWidget
     */
    public static function GetAvailable()
    {
        global $g_ado_db, $g_user;

        // get all widgets
        $index = new Extension_Index();
        $widgets = $index->addDirectory(WWW_DIR . '/extensions')
            ->find('IWidget');

        // get used by user
        $user_widgets = array();
        $queryStr = 'SELECT id, path, class, fk_widgetcontext_id
            FROM widget w LEFT JOIN widgetcontext_widget wcw
                ON (w.id = wcw.fk_widget_id
                AND wcw.fk_user_id = ' . $g_user->getUserId() . ')';
        $rows = $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $user_widgets[self::FormatId($row['path'], $row['class'])] = $row;
        }

        // filter widgets
        $available = array();
        foreach ($widgets as $widget) {
            $id = self::FormatId($widget->getPath(), $widget->getClass());
            if (!empty($user_widgets[$id])
                && !empty($user_widgets[$id]['fk_widgetcontext_id'])) {
                continue;
            }

            $data = empty($user_widgets[$id]) ? array() : $user_widgets[$id];
            $available[] = self::getWidgetInstance($widget->getPath(),
                $widget->getClass(), $data);
        }

        return $available;
    }

    /**
     * Get widgets by context.
     * @param IWidgetContext $context
     * @return array of IWidget
     */
    public static function GetWidgetsByContext(IWidgetContext $context)
    {
        global $g_user, $g_ado_db;

        $queryStr = 'SELECT *
            FROM widget w INNER JOIN widgetcontext_widget wcw
                ON w.id = wcw.fk_widget_id
            WHERE wcw.fk_user_id = ' . $g_user->getUserId() . '
                AND wcw.fk_widgetcontext_id = ' . $context->getId() . '
            ORDER BY `order`';
        $rows = $g_ado_db->GetAll($queryStr);

        $widgets = array();
        foreach ($rows as $row) {
            $widgets[] = self::GetWidgetInstance($row['path'], $row['class'], (array) $row);
        }
        return $widgets;
    }

    /**
     * Set widgets context.
     * @param IWidgetContext|string $context
     * @param array of IWidgets $widgets
     * @return bool
     */ 
    public static function SetContextWidgets($context = '', $widgets = array())
    {
        global $g_user, $g_ado_db;

        if (empty($widgets)) {
            return TRUE;
        }

        $context = self::GetWidgetContext($context);

        foreach (self::ParseWidgetIds($widgets) as $order => $id) {
            $queryStr = 'UPDATE widgetcontext_widget
                SET fk_widgetcontext_id = ' . $context->getId() . ',
                    `order` = ' . $order . '
                WHERE fk_widget_id = ' . $id . '
                    AND fk_user_id = ' . $g_user->getUserId();
            $g_ado_db->execute($queryStr);
            if ($g_ado_db->Affected_Rows() <= 0 && !$context->isDefault()) { // not set
                $queryStr = sprintf('INSERT INTO widgetcontext_widget
                    (fk_widgetcontext_id, fk_widget_id, fk_user_id, `order`) VALUES
                    (%d, %d, %d, %d)',
                    $context->getId(),
                    $id,
                    $g_user->getUserId(),
                    $order);
                $g_ado_db->execute($queryStr);
            }
        }
        $queryStr = 'DELETE FROM widgetcontext_widget WHERE fk_widgetcontext_id = 0';
        $g_ado_db->execute($queryStr);

        return TRUE;
    }

    /**
     * Add widget to user dashboard
     * @param int $widget_id
     * @param string $context_name
     * @return bool
     */
    public static function AddWidget($widget_id, $context_name)
    {
        global $g_ado_db, $g_user;

        // get context object
        $context = self::GetWidgetContext($context_name);

        // insert before - move others
        $queryStr = 'UPDATE widgetcontext_widget
            SET `order` = `order` + 1
            WHERE fk_widgetcontext_id = ' . $context->getId();
        $g_ado_db->execute($queryStr);

        // insert
        $queryStr = sprintf('INSERT INTO widgetcontext_widget
            (fk_widgetcontext_id, fk_widget_id, fk_user_id, `order`) VALUES
            (%d, %d, %d, 0)',
            $context->getId(),
            (int) $widget_id,
            $g_user->getUserId());
        $g_ado_db->execute($queryStr);
        return TRUE;
    }

    /**
     * Remove widget from user dashboard
     * @param string $widget_id
     * @return bool
     */
    public static function RemoveWidget($widget_id)
    {
        global $g_ado_db, $g_user;

        try { // parse id
            list(, $id) = explode('_', (string) $widget_id);
        } catch (Exception $e) {
            $id = 0;
        }

        // delete
        $queryStr = 'DELETE FROM ' . self::TABLE . '
            WHERE fk_user_id = ' . $g_user->getUserId() . '
                AND fk_widget_id = ' . ((int) $id);
        $g_ado_db->execute($queryStr);
        return TRUE;
    }

    /**
     * Get widget content for specified view
     * @param int $widget_id
     * @param string $view
     * @return string
     */
    public static function GetWidgetContent($widget_id, $view)
    {
        global $g_ado_db;

        list(,$widget_id) = explode('_', $widget_id);

        // get widget file & class info
        $queryStr = 'SELECT id, path, class
            FROM widget
            WHERE id = ' . ( (int) $widget_id);
        $row = $g_ado_db->getRow($queryStr);

        $widget = self::GetWidgetInstance($row['path'], $row['class'], (array) $row);
        return $widget->render($view, TRUE);
    }

    /**
     * Get instance of widget.
     * @param string $filename
     * @param string $class
     * @param array $data
     * @return WidgetRendererDecorator
     */
    private static function GetWidgetInstance($filename, $class, array $data = array())
    {
        require_once $filename;
        $widget = new $class;
        return new WidgetRendererDecorator($widget, $data);
    }

    /**
     * Get widgets ids from JSON.
     * @param array $widgets
     * @return array of int
     */
    private static function ParseWidgetIds(array $widgets = NULL)
    {
        $ids = array();
        foreach ((array) $widgets as $widget) {
            list(, $id) = explode('_', $widget);
            $ids[] = $id;
        }
        return $ids;
    }

    /**
     * Get WidgetContext instance.
     * @param IWidgetContext|string $context
     * @return IWidgetContext
     */
    private static function GetWidgetContext($context)
    {
        if ($context instanceof IWidgetContext) {
            return $context;
        } elseif (is_string($context)) {
            return new WidgetContext($context);
        } else {
            throw new LogicException("Can't create context");
        }
    }

    /**
     * Format widget id
     * @param string $path
     * @param string $class
     */
    private static function FormatId($path, $class)
    {
        return implode(':', array($path, $class));
    }
}
