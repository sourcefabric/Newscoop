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

    if(!defined("PHORUM_ADMIN")) return;

    class PhorumAdminMenu
    {
        var $_title;
        var $_id;
        var $_links;

        function PhorumAdminMenu ($title="", $id="")
        {
            $this->reset($title, $id);
        }

        function reset($title="", $id="")
        {
            $this->_title = $title;
            $this->_id = $id;
            $this->_links=array();
        }

        function add($title, $module, $description)
        {
            $this->_links[]=array("title"=>$title, "module"=>$module, "description"=>$description);
        }


        function show()
        {
            if($this->_title){
                echo "<div class=\"PhorumAdminMenuTitle\">$this->_title</div>\n";
            }
            echo "<div class=\"PhorumAdminMenu\"";
            if($this->_id) echo " id=\"$this->_id\"";
            echo ">";

            foreach($this->_links as $link){
                $desc=$link["description"];
                $html ="<a title='$desc' href=\"$_SERVER[PHP_SELF]";
                if(!empty($link["module"])) $html.="?module=$link[module]";
                $html.="\">$link[title]</a><br />";
                echo $html;
            }

            echo "</div>\n";


        }

    }

?>
