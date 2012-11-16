<?php
/**
 * @package Campsite
 *
 * @author Martin Saturka <martin.saturka@sourcefabric.org>
 * @copyright 2011 Sourcefabric, Ops.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.sourcefabric.org
 */

/**
 * Imports the stored function, e.g. for 'Point in Polygon' checking
 *
 * @param string $p_fileName
 *
 * @return bool
 */
function importSqlStoredProgram ($p_db, $p_fileName) {
    $was_correct = true;

    $fc = @file($p_fileName);
    if (!$fc) {
        return false;
    }

    $delimiter = ";";

    $last_multi_query = isset($p_db->multiQuery) ? $p_db->multiQuery : false;
    $p_db->multiQuery = true; // we define a stored function, but can not use the 'delimiter' command

    $one_comm_arr = array();
    foreach ($fc as $one_line) {
        $one_line = trim($one_line);
        if (empty($one_line) || ("--" == substr($one_line, 0, 2))) {
            continue;
        }
        $one_line_len = strlen($one_line);

        $exec_last = false; // to exec the read commands
        $exec_this = false; // for possible running the delimiter setting, but can not do that within php
        $check_more = true; // wheter the line is something else then delimiter setting

        // checking whether the line sets delimiter, and what is that delimiter
        if ("delimiter" == strtolower(substr($one_line, 0, strlen("delimiter")))) {
            $delim_arr_ini = explode(" ", $one_line);
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
                        //$exec_this = true; // we can not set delimiter within php
                        $check_more = false;
                    }
                }
            }
        }

        // if not a delimiter setting
        if ($check_more) {
            if (substr($one_line, ($one_line_len - strlen($delimiter))) == $delimiter) {
                $one_line = substr($one_line, 0, ($one_line_len - strlen($delimiter)));
                $exec_last = true;
            }
            $one_comm_arr[] = $one_line;
        }

        // do we have a complete command (set)?, i.e. ended with the current delimiter
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
            if ($p_db->Execute($one_sql_query) == false) {
                $was_correct = false;
                //echo "\n<br>\nproblems with $one_sql_query: " . $p_db->ErrorMsg() . "\n<br>\n";
            }
        }

    }

    $p_db->multiQuery = $last_multi_query;

    return $was_correct;

} // fn importSqlStoredProgram

?>
