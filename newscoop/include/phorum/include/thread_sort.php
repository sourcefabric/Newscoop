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

    if(!defined("PHORUM")) return;

    ////////////////////////////////////////////////////////////////////////
    //
    // This function sorts $rows and fills $threads.  It assumes that $rows
    // is an array that is sorted by thread, then id.  This is critical as
    // it ensures that a child is not encountered before a parent.
    // It could be made more complicated to implement the tree graphics
    // as Phorum 3 did.  However, this is much faster and less complicated
    // If someone just has to have the tree graphics, it can be done.
    //

    function phorum_sort_threads($rows)
    {
        foreach($rows as $row){
            $rows[$row["parent_id"]]["children"][]=$row["message_id"];
        }
        
        // rewriting the define to a var for the new style of indenting
        $GLOBALS['PHORUM']['DATA']['marker']=$GLOBALS['PHORUM']['TMP']['marker'];

        $sorted_rows=array(0=>array());

        _phorum_recursive_sort($rows, $sorted_rows);

        unset($sorted_rows[0]);

        return $sorted_rows;
    }


    // not to be called directly.  Call phorum_sort_threads

    function _phorum_recursive_sort($rows, &$threads, $seed=0, $indent=0)
    {
        global $PHORUM;

        if($seed>0){
            $threads[$rows[$seed]["message_id"]]=$rows[$seed];
            // old style of indenting
            $threads[$rows[$seed]["message_id"]]["indent"]=str_repeat($PHORUM['TMP']['indentstring'], $indent);
            
            if(!empty($indent)){
                $threads[$rows[$seed]["message_id"]]["indent"].=$PHORUM['TMP']['marker'];
            }
            
            // new style of indenting by padding-left
            $threads[$rows[$seed]["message_id"]]["indent_cnt"]=$indent*$PHORUM['TMP']['indentmultiplier'];
            if($indent < 31) {
                $wrapnum=80-($indent*2);
            } else {
                $wrapnum=20;
            }
            $threads[$rows[$seed]["message_id"]]["subject"]=wordwrap($rows[$seed]['subject'],$wrapnum," ",1);
            
            $indent++;

        }
        if(isset($rows[$seed]["children"])){
            foreach($rows[$seed]["children"] as $child){
                _phorum_recursive_sort($rows, $threads, $child, $indent);
            }
        }
    }




?>
