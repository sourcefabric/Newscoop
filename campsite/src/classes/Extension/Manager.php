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

/**
 * Manager class
 */
class Extension_Manager
{
    const EXTENSIONS = '/extensions/*/*.php';

    /**
     * Get not-used extensions.
     *
     * @param string $interface
     *      - type of extension - IWidget, IPlugin..
     *
     * @return array of IWidget
     */
    public static function GetExtensions($interface = '')
    {
        global $g_ado_db;

        // load parsed extensions
        $queryStr = 'SELECT filename
            FROM extension_widget';
        $rows = $g_ado_db->getAll($queryStr);
        $files = array();
        if (!empty($rows)) {
            foreach ($rows as $row) {
                $files[$row['filename']] = TRUE;
            }
        }

        // parse and save the rest
        $extensions = array();
        foreach (glob($GLOBALS['g_campsiteDir'] . self::EXTENSIONS) as $file) {
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
}
