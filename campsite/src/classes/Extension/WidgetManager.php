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
require_once dirname(__FILE__) . '/WidgetContext.php';
require_once dirname(__FILE__) . '/WidgetRendererDecorator.php';

/**
 * Widget Manager
 */
class WidgetManager
{
    /**
     * Widget directories
     * @var array
     */
    private static $dirs = array(
        '/extensions/',
    );

    /**
     * Registered widgets.
     * @var array
     */
    private static $registered = NULL;

    /**
     * Unregistered widgets.
     * @var array
     */
    private static $unregistered = NULL;

    /**
     * Widgets repository.
     * @var array
     */
    private static $widgets = array();

    /**
     * Get registered widgets.
     * @return array
     */
    private static function GetRegistered()
    {
        global $g_ado_db;

        if (self::$registered === NULL) {
            self::$registered = array();
            $queryStr = 'SELECT filename, class
                FROM extension_widget';
            $rows = $g_ado_db->getAll($queryStr);
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    self::$registered[$row['filename']] = $row['class'];
                }
            }
        }

        return self::$registered;
    }

    /**
     * Get unregistered widgets.
     * @return array
     */
    private static function GetUnregistered()
    {
        global $g_ado_db, $g_user;

        if (self::$unregistered === NULL) {
            self::$unregistered = array();
            $queryStr = 'SELECT *
                FROM widget w LEFT JOIN widgetcontext_widget wcw ON 
                    w.id = wcw.fk_widget_id
                WHERE wcw.fk_user_id = ' . $g_user->getUserId() . '
                    AND (wcw.fk_widgetcontext_id IS NULL
                        OR wcw.fk_widgetcontext_id = 0)';
            self::$unregistered = (array) $g_ado_db->GetAll($queryStr);
        }

        return self::$unregistered;
    }

    /**
     * Get widgets by context.
     * @param IWidgetContext $context
     * @return array of IWidget
     */
    public static function GetWidgetsByContext(IWidgetContext $context)
    {
        global $g_user, $g_ado_db;

        $key = $context->getId() !== NULL ? $context->getName() : '';
        if (!isset(self::$widgets[$key])) {
            self::$widgets[$key] = array();
            if (empty($key)) { // unregistered
                foreach (self::$dirs as $dir) {
                    self::$widgets[$key] += self::scanDir($dir);
                }

                foreach (self::GetUnregistered() as $row) {
                    self::$widgets[$key][] = self::GetWidgetInstance($row['filename'], $row['class'], $row);
                }
            } else { // registered
                $queryStr = 'SELECT *
                    FROM widget w LEFT JOIN widgetcontext_widget wcw ON 
                        w.id = wcw.fk_widget_id
                    WHERE wcw.fk_user_id = ' . $g_user->getUserId() . '
                        AND wcw.fk_widgetcontext_id = ' . $context->getId() . '
                    ORDER BY `order`';
                $rows = $g_ado_db->GetAll($queryStr);
                foreach ($rows as $row) {
                    self::$widgets[$key][] = self::GetWidgetInstance($row['filename'],
                        $row['class'], (array) $row);

                }
            }
        }
        return self::$widgets[$key];
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
            echo $context->getId(), $id, $order, "\n";
            $queryStr = 'UPDATE widgetcontext_widget
                SET fk_widgetcontext_id = ' . $context->getId() . ',
                    `order` = ' . $order . '
                WHERE fk_widget_id = ' . $id . '
                    AND fk_user_id = ' . $g_user->getUserId();
            $g_ado_db->execute($queryStr);
            if ($g_ado_db->Affected_Rows() <= 0 && $context->getId() > 0) { // not set
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

        return TRUE;
    }

    /**
     * Get widget content for specified context.
     * @param int $widget_id
     * @param string $context
     * @return string
     */
    public static function GetWidgetContent($widget_id, $context)
    {
        global $g_ado_db;

        list(,$widget_id) = explode('_', $widget_id);

        $context = self::GetWidgetContext($context);

        // get widget file & class info
        $queryStr = 'SELECT filename, class
            FROM widget
            WHERE id = ' . ( (int) $widget_id);
        $row = $g_ado_db->getRow($queryStr);

        $widget = self::GetWidgetInstance($row['filename'], $row['class'], (array) $row);

        return $widget->render($context, TRUE);
    }

    /**
     * Scan directory for classes implementing IWidget
     * @param string $dirname
     * @return array
     *      filename => class
     */
    private static function scanDir($dirname)
    {
        $registered = self::getRegistered();
        $files = array_keys($registered);

        // parse and save the rest
        $extensions = array();
        foreach (glob($GLOBALS['g_campsiteDir'] . "/$dirname/*.php") as $file) {
            if (!empty($files[$file])) { // used
                continue;
            }
            $s = file_get_contents($file);
            $tokens = token_get_all($s);
            $tokens_size = sizeof($tokens);
            $classname = '';
            for ($i = 0; $i < $tokens_size; $i++) {
                if ($tokens[$i][0] == T_CLASS) {
                    $classname = $tokens[$i + 2][1];
                    require_once $file;
                    $reflection = new ReflectionClass($classname);
                    if (in_array($interface, $reflection->getInterfaceNames())) {
                        $extension = new $classname();
                        $extension->create(array(
                            'filename' => $file,
                            'class' => $classname,
                        )); // gets id
                        $extensions[] = $extension;
                    }
                }
            }
        }

        return $extensions;
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
}
