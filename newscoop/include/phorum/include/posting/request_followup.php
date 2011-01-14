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

// Create an empty message structure.
$message = array();

// Inject form field data into the message structure. No checks
// are done on the data over here. Here we just take care of
// putting the data in the right format in the data structure.
foreach ($PHORUM["post_fields"] as $var => $spec)
{
    // Format and store the data based on the configuration.
    switch ($spec[pf_TYPE])
    {
    	case "boolean":
    	    $message[$var] = isset($_POST[$var]) && $_POST[$var] ? 1 : 0;
    	    break;

    	case "integer":
    	    $message[$var] = isset($_POST[$var]) ? (int) $_POST[$var] : NULL;
    	    break;

        case "array":
    	    $message[$var] = isset($_POST[$var]) ? unserialize($_POST[$var]) : array();
    	    break;

        case "string":
    	    $message[$var] = isset($_POST[$var]) ? trim($_POST[$var]) : '';
            // Prevent people from impersonating others by using
            // multiple spaces in the author name.
            if ($var == 'author') {
                $message[$var] = preg_replace('/\s+/', ' ', $message[$var]);
            }
    	    break;

    	default:
    	    die ("Illegal field type used for field $var: " . $spec[pf_TYPE]);
    }
}

?>
