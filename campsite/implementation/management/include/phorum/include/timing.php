<?php

////////////////////////////////////////////////////////////////////////////////
//                                                                            //
//   Copyright (C) 2006  Phorum Development Team                              //
//   http://www.phorum.org                                                    //
//                                                                            //
//   This program is free software. You can redistribute it and/or modify     //
//   it under the terms of either the current Phorum License (viewable at     //
//   phorum.org) or the Phorum License that was distributed with this file    //
//                                                                            //
//   This program is distributed in the hope that it will be useful,          //
//   but WITHOUT ANY WARRANTY, without even the implied warranty of           //
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                     //
//                                                                            //
//   You should have received a copy of the Phorum License                    //
//   along with this program.                                                 //
////////////////////////////////////////////////////////////////////////////////

// This library contains some functions that can be used for
// code benchmarking. It is not actively in use in the Phorum
// core, but its functions are used by the developers once
// in a while.

    if(!defined("PHORUM")) return;

    function timing_start($key="default")
    {
        $GLOBALS["_TIMING"][$key]["start"]=microtime();
    }
    
    function timing_mark($mark, $key="default")
    {
        $GLOBALS["_TIMING"][$key][$mark]=microtime();
    }

    function timing_print($key="default")
    {
        echo '<table border="1" cellspacing="0" cellpadding="2">';
        echo "<tr><th>Mark</th><th>Time</th><th>Elapsed</th></tr>";
        foreach($GLOBALS["_TIMING"][$key] as $mark => $mt){
            $thistime=array_sum(explode(" ", $mt));
            if(isset($lasttime)){
                $elapsed=round($thistime-$start, 4);
                $curr=round($thistime-$lasttime, 4);
                echo "<tr><td>$mark</td><td>$curr sec.</td><td>$elapsed sec.</td></tr>"; 


            } else {
                $start=$thistime;
            }
            $lasttime=$thistime;
        }
        echo "</table>";
    }

?>
