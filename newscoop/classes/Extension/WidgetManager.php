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
        'YourArticlesWidget',
        'SourcefabricFeed',

        'RecentlyModifiedArticlesWidget',
        'SourcefabricNewsletter',

        'SubmittedArticlesWidget',
        'MapsGoogleGadget',

        'RecentlyPublishedArticlesWidget',
        'SearchWikipedia',

        'MostPopularArticlesWidget',
        'SourcefabricDevFeed',

        'PendingArticlesWidget',
    );

    public static function ExtPath() {
        return DIR_SEP . 'extensions' . DIR_SEP;
    }

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
        $extensions = $index->addDirectory(WWW_DIR . self::ExtPath())
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
        if(is_array($rows)) {
            foreach ($rows as $row) {
                $widget = new WidgetRendererDecorator((array) $row);
                if ($widget->getWidget() !== NULL) {
                    $widgets[] = $widget;
                }
            }
        }
        return $widgets;
    }

    /**
     * Add widget to user dashboard
     * @param int $widgetId
     * @param string|IWidgetContext $contextName
     * @param int $uid
     * @param int $order
     * @return bool
     */
    public static function AddWidget($widgetId, $context, $uid = NULL, $order = NULL)
    {
        global $g_ado_db, $g_user;

        // get context object
        if (is_string($context)) {
            $context = new WidgetContext($context);
        }

        // set uid
        if (empty($uid)) {
            $uid = $g_user->getUserId();
        }

        if ($order === NULL) {
            // set order to be on top
            $sql = 'SELECT MIN(`order`)
                FROM ' . WidgetManagerDecorator::TABLE . '
                WHERE fk_user_id = ' . (int) $uid . '
                    AND fk_widgetcontext_id = ' . $context->getId();
            $order = $g_ado_db->getOne($sql) - 1;
        }

        // generate uniq id
        $id = 'w' . substr(sha1(uniqid() . $g_user->getUserId()), -12);

        // add widget
        $widget = new WidgetManagerDecorator(array(
            'id' => $id,
            'fk_widget_id' => (int) $widgetId,
            'fk_widgetcontext_id' => $context->getId(),
            'fk_user_id' => (int) $uid,
            'order' => (int) $order,
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
        // contexts used on homepage
        $contexts = array(
            new WidgetContext('dashboard1'),
            new WidgetContext('dashboard2'),
        );

        foreach (WidgetManager::GetAvailable($p_uid) as $widget) {
            $extension = $widget->getExtension();
            if (in_array($extension->getClass(), self::$defaults)) {
                $order = (int) array_search($extension->getClass(), self::$defaults); // get order
                $context = $contexts[$order % sizeof($contexts)]; // pick context
                self::AddWidget($extension->getId(), $context, (int) $p_uid, $order);
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
