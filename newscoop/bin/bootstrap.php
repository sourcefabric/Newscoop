<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

// get document root via -d, --document_root
$document_root = dirname(dirname(__FILE__));
$options = getopt('d:', array('document_root:'));
foreach (array('d', 'document_root') as $option) {
    if (!empty($options[$option])) {
        $document_root = $options[$option];
    }
}

// check if document_root exists
if (!is_dir($document_root)) {
    $file = basename($_SERVER['argv'][0]);
    echo "$file error: Directory '$document_root' does not exist.\n";
    echo "Please provide valid Newscoop document_root path via: $file [-d path] [--document_root path]\n";
    exit(1);
}

// set global variables, constants
define('WWW_DIR', realpath($document_root));
$GLOBALS['g_campsiteDir'] = $CAMPSITE_DIR = WWW_DIR;

