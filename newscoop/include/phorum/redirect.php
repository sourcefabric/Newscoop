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

// Redirect to another page. This is used for working around an MSIE bug 
// where redirecting to an anchored URL looses the anchor if redirected 
// directly from a script that acts on POST input coming from an 
// enctype="multipart/mixed" form... *sigh*.

define('phorum_page', 'redirect');

require_once("./common.php");

if (isset($PHORUM["args"]["phorum_redirect_to"])) {
    $redir = urldecode($PHORUM["args"]["phorum_redirect_to"]);
    phorum_redirect_by_url($redir);
} else {
    header("Location: index.php");
}

?>
