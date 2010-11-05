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
        'extensions',
    );

    /**
     * Parsed files
     * @var array
     */
    private static $indexed = NULL;

    /**
     * Unregistered widgets
     * @var array
     */
    private static $unregistered = NULL;

    /**
     * Widgets repository
     * @var array
     */
    private static $widgets = array();

    /**
     * Get indexed widgets data.
     * @return array
     */
    private static function GetIndexed()
    {
        global $g_ado_db;

        if (self::$indexed === NULL) {
            self::$indexed = array();
            $queryStr = 'SELECT id, filename, checksum
                FROM widget';
            $rows = $g_ado_db->getAll($queryStr);
            foreach ($rows as $row) {
                self::$indexed[$row['filename']] = $row;
            }
        }

        return self::$indexed;
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
                WHERE wcw.fk_widgetcontext_id IS NULL OR (
                    wcw.fk_user_id <> ' . $g_user->getUserId() . '
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

        $key = $context->getName();
        if (!isset(self::$widgets[$key])) {
            self::$widgets[$key] = array();
            if ($context->isDefault()) { // unregistered
                // scan for new/updated widgets
                self::UpdateIndex(self::$dirs);

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
     * Update dirs index
     * @param array $dirs
     * @return void
     */
    private static function UpdateIndex(array $dirs)
    {
        // scan for files
        $files = array();
        foreach ($dirs as $dir) {
            $path = $GLOBALS['g_campsiteDir'] . "/$dir/*/*.php";
            $files = array_merge($files, glob($path));
        }

        if (empty($files)) {
            return $files;
        }

        // index files
        $indexed = self::GetIndexed();
        foreach ($files as $file) {
            $checksum = sha1_file($file);
            if (isset($indexed[$file])) { // indexed, check for changes
                if ($checksum != $indexed[$file]['checksum']) { // update
                    self::IndexFile($file, $checksum);
                }
                continue;
            }
            self::IndexFile($file, $checksum, TRUE);
        }
    }

    /**
     * Index file classes implementing IWidget
     * @param string $filename
     * @param string $checksum
     * @param bool $update
     * @return void
     */
    private static function IndexFile($filename, $checksum, $new = FALSE)
    {
        $s = file_get_contents($filename);
        $tokens = token_get_all($s);
        $tokens_size = sizeof($tokens);
        $class = '';
        for ($i = 0; $i < $tokens_size; $i++) {
            if ($tokens[$i][0] == T_CLASS) {
                $class = $tokens[$i + 2][1];
                require_once $filename;
                $reflection = new ReflectionClass($class);
                if ($reflection->implementsInterface('IWidget')
                    && $reflection->isInstantiable()) {
                    $data = array(
                        'filename' => $filename,
                        'class' => $class,
                        'checksum' => $checksum,
                    );
                    $widget = self::GetWidgetInstance($filename, $class, $data);
                    if ($new) { // new file
                        $widget->create($data);
                    } else { // update
                        $widget->update($data);
                    }
                }
            }
        }
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
