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

    // Check for possible collisions between modules.

    $phorum_check = "Modules (hook collision checks)";

    function phorum_check_modules() {
        $PHORUM = $GLOBALS["PHORUM"];

        // For some hooks, we only want one module enabled to
        // prevent collision problems. This is a list of
        // those specific hooks.
        $only_single_mod_allowed = array(
            'quote',
            'send_mail',
        );

        // Check all hooks that only may appear once.
        foreach ($only_single_mod_allowed as $hook) {
            if (isset($PHORUM["hooks"][$hook]["mods"])) {
                $mods = $PHORUM["hooks"][$hook]["mods"];
                if (count($mods) > 1) return array(
                    PHORUM_SANITY_WARN,
                    "You have activated multiple modules that handle
                     Phorum's \"".htmlspecialchars($hook)."\" hook.
                     However, this hook is normally only handled by
                     one module at a time. Keeping all modules
                     activated might lead to some unexpected results.
                     The colliding modules are: ".
                     implode(" + ", $mods), 
                    "You can ignore this message in case you
                     are sure that the modules can work together. Else,
                     make sure you have only one of these modules
                     enabled."
                );
            }
        }

        // All checks are OK.
        return array(PHORUM_SANITY_OK, NULL);
    }
?>
