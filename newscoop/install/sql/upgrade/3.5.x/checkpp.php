<?php
$cs_dir = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
$GLOBALS['g_campsiteDir'] = $cs_dir;
require_once($cs_dir.DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR.'campsite_constants.php');
require_once(CS_PATH_SITE.DIR_SEP.'include'.DIR_SEP.'campsite_init.php');

// $checkpp_dir = dirname(dirname(dirname(__FILE__)));
// $checkpp_sql = $checkpp_dir . "checkpp.sql";
$checkpp_sql = CS_INSTALL_DIR . DIR_SEP . 'sql' . DIR_SEP . "checkpp.sql";

function importCheckPP($p_fileName) {

global $g_db;

$fh = $fopen($p_fileName, "r");
$fc = file($fh);
fclose($fh);

$delimiter = ";";

$one_comm_arr = array();
foreach ($fc as $one_line) {
    $one_line = trim($one_line);
    if (empty($one_line) || ("--" == substr($one_line, 0, 2))) {
        continue;
    }
    $one_line_len = strlen($one_line);

    $exec_last = false;
    $exec_this = false;

    $check_more = true;

    if ("delimiter" == strtolower(substr($one_line, 0, strlen("delimiter")))) {
        $delim_arr_ini = split(" ", $one_line);
        $delim_arr = array();
        foreach ($delim_arr_ini as $one_delim_part) {
            $one_delim_part = trim($one_delim_part);
            if (empty($one_delim_part)) {
                continue;
            }
            $delim_arr[] = $one_delim_part;
        }
        if (2 == count($delim_arr)) {
            if ("delimiter" == strtolower($delim_arr[0])) {
                if (!empty($delim_arr[1])) {
                    $delimiter = $delim_arr[1];
                    $exec_last = true;
                    $exec_this = true;
                    $check_more = false;
                }
            }
        }
    }

    if ($check_more) {
        if (substr($one_line, ($one_line_len - strlen($delimiter))) == $delimiter) {
            $one_comm_arr[] = $one_line;
            $exec_last = true;
        }
    }

    $sql_commands = array();

    if ($exec_last) {
        if (!empty($one_comm_arr)) {
            $sql_commands[] = implode(" ", $one_comm_arr);
        }
        $one_comm_arr = array();
    }

    if ($exec_this) {
        $sql_commands[] = $one_line;
    }

    foreach ($sql_commands as $one_sql_query) {
        if ($g_db->Execute($one_sql_query) == false) {
            echo "\n<br>\nproblems with $one_sql_query\n<br>\n";
        }
    }

}

} // fn importCheckPP

importCheckPP($checkpp_sql);

?>
