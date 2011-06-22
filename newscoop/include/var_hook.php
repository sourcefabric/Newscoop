<?php

/**
 * @author Mihai Nistor <mihai.nistor@gmail.com>
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

function var_hook()
{
   $arg_list = func_get_args();
   $numargs = func_num_args();
   $out = '';
   for ($i = 0; $i < $numargs; $i++) {
       $out.= var_export($arg_list[$i], true).", ";

    }
    syslog(LOG_INFO, $out);
    //error_log($out);
}