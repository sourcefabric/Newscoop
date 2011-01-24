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
    const SETTING = 'WidgetsInstalled';

    /** @var array */
    private static $defaults = array(
        'MostPopularArticlesWidget',
        'PendingArticlesWidget',
        'RecentlyModifiedArticlesWidget',
        'RecentlyPublishedArticlesWidget',
        'SubmittedArticlesWidget',
        'YourArticlesWidget',
    );

    /**
     * Get available widgets for user
     * @param int|NULL $uid
     * @return array of IWidget
     */
    public static function GetAvailable($uid = NULL)
    {
        global $g_user;

        if ($uid === NULL) {
            $uid = $g_user->getUserId();
        }

        // get all widget extensions
        $index = new Extension_Index();
        $extensions = $index->addDirectory(dirname(__FILE__) . '/../../extensions')
            ->find('IWidget');

        // filter not-available (used)
        $widgets = array();
        foreach ($extensions as $extension) {
            $widget = WidgetManagerDecorator::GetByExtension($extension);
            if ($widget->isAvailable($uid)) {
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
            $widget = new WidgetRendererDecorator((array) $row);
            if ($widget->getWidget() !== NULL) {
                $widgets[] = $widget;
            }
        }
        return $widgets;
    }

    /**
     * Add widget to user dashboard
     * @param int $widgetId
     * @param string $contextName
     * @param int $uid
     * @return bool
     */
    public static function AddWidget($widgetId, $contextName, $uid = NULL)
    {
        global $g_ado_db, $g_user;

        // get context object
        $context = new WidgetContext($contextName);

        // set uid
        if (empty($uid)) {
            $uid = $g_user->getUserId();
        }

        $id = 'w' . substr(sha1(uniqid() . $g_user->getUserId()), -12);
        $widget = new WidgetManagerDecorator(array(
            'id' => $id,
            'fk_widget_id' => (int) $widgetId,
            'fk_widgetcontext_id' => $context->getId(),
            'fk_user_id' => (int) $uid,
            'order' => 'MIN(`order`) - 1',
        ));
        $widget->create();
        return $id;
    }

    /**
     * Set default widgets for g_user
     * @param int $p_uid
     * @return void
     */
    public static function SetDefaultWidgets($p_uid)
    {
        $contexts = array(
            new WidgetContext('dashboard1'),
            new WidgetContext('dashboard2'),
        );
        $context = 0;
        foreach (WidgetManager::GetAvailable($p_uid) as $widget) {
            $extension = $widget->getExtension();
            if (in_array($extension->getClass(), self::$defaults)) {
                self::AddWidget($extension->getId(), $contexts[$context]->getName(), (int) $p_uid);
                $context = ($context + 1) % 2;
            }
        }
    }

    /**
     * Set default widgets for all existing users (called after install/upgrade)
     * @return void
     */
    public static function SetDefaultWidgetsAll()
    {
        require_once dirname(__FILE__) . '/../User.php';

        // do only once
        if (SystemPref::Get(self::SETTING) != NULL) {
            return;
        }

        SystemPref::Set(self::SETTING, time());

        // set widgets per user
        $users = (array) User::GetUsers();
        foreach ($users as $user) {
            WidgetManager::SetDefaultWidgets($user->getUserId());
        }
    }
}
