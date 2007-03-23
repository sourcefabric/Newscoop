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

if ( !defined( "PHORUM_ADMIN" ) ) return;

class PhorumInputForm {
    var $_rows;
    var $_hiddens;
    var $_action;
    var $_method;
    var $_target;
    var $_enctype;
    var $_events;
    var $_submit;
    var $_help;

    function PhorumInputForm ( $action = "", $method = "get", $submit = "Submit", $target = "", $enctype = "", $events = "" )
    {
        $this->_action = ( empty( $action ) ) ? $_SERVER["PHP_SELF"] : $action;
        $this->_method = $method;
        $this->_target = $target;
        $this->_enctype = $enctype;
        $this->_events = $events;
        $this->_submit = $submit;
    }

    function hidden( $name, $value )
    {
        $this->_hiddens[$name] = $value;
    }

    function addrow( $title, $contents = "", $valign = "middle", $align = "left" )
    {
        list( $talign, $calign ) = explode( ",", $align );
        if ( empty( $calign ) ) $calign = $talign;

        list( $tvalign, $cvalign ) = explode( ",", $valign );
        if ( empty( $cvalign ) ) $cvalign = $tvalign;

        $this->_rows[] = array( "title" => $title,
            "contents" => $contents,
            "title_valign" => $tvalign,
            "content_valign" => $cvalign,
            "title_align" => $talign,
            "content_align" => $calign
            );

        end( $this->_rows );

        return key( $this->_rows );
    }

    function addhelp( $row, $title, $text )
    {
        // Allow title and text to span multiple lines and
        // do escaping for encapsulation within the help
        // javascript code.
        $title = addslashes(str_replace("\n", " ", $title));
        $text = addslashes(str_replace("\n", " ", $text));
        $this->_help[$row] = array( $title, $text );
    }

    function addbreak( $break = "&nbsp;" )
    {
        $this->_rows[] = array( "break" => $break );
        end( $this->_rows );
        return key( $this->_rows );
    }

    function addmessage( $message )
    {
        $this->_rows[] = array( "message" => $message );
    }

    function show()
    {
        global $PHORUM_IMAGES_URL;

        if(count($this->_help)){
            echo "<script type=\"text/javascript\">\nvar help = Array;\n";
            foreach($this->_help as $key=>$data){
                $title = str_replace('"', "&quot;", $data[0]);
                $text = str_replace('"', '&quot;', $data[1]);
                $text = str_replace("\n", "\\n", $text);
                echo "help[$key] = [\"$title\", \"$text\"];\n";
            }
            echo "</script>\n";
        }
        echo "<form style=\"display: inline;\" action=\"$this->_action\" method=\"$this->_method\"";
        if ( !empty( $this->_target ) ) echo " target=\"$this->_target\"";
        if ( !empty( $this->_enctype ) ) echo " enctype=\"$this->_enctype\"";
        if ( !empty( $this->_events ) ) echo " $this->_events";
        echo ">\n";

        if ( is_array( $this->_hiddens ) ) foreach( $this->_hiddens as $name => $value ) {
            echo "<input type=\"hidden\" name=\"$name\" value=\"$value\">\n";
        }

        echo "<table border=\"0\" cellspacing=\"2\" cellpadding=\"2\" class=\"input-form-table\" width=\"100%\">\n";

        if ( is_array( $this->_rows ) ) foreach( $this->_rows as $key => $row ) {

            if ( $row["break"] ) {
                $title = $row["break"];
                if ( isset( $this->_help[$key] ) ) {
                    $title = $title . "<a href=\"javascript:show_help($key);\"><img class=\"question\" alt=\"Help\" title=\"Help\" border=\"0\" src=\"$PHORUM_IMAGES_URL/qmark.gif\" height=\"16\" width=\"16\" /></a>";
                }
                echo "<tr class=\"input-form-tr\">\n";
                echo "  <td colspan=\"2\" class=\"input-form-td-break\">$title</td>\n";
                echo "</tr>\n";
            } elseif ( $row["message"] ) {
                echo "<tr class=\"input-form-tr\">\n";
                echo "  <td colspan=\"2\" class=\"input-form-td-message\">$row[message]</td>\n";
                echo "</tr>\n";
            } else {
                $colspan = ( $row["contents"] == "" ) ? " colspan=2" : "";

                $title = $row["title"];

                if ( isset( $this->_help[$key] ) ) {
                    $title = $title . "<a href=\"javascript:show_help($key);\"><img class=\"question\" alt=\"Help\" title=\"Help\" border=\"0\" src=\"$PHORUM_IMAGES_URL/qmark.gif\" height=\"16\" width=\"16\" /></a>";
                }

                echo "<tr class=\"input-form-tr\">\n";
                echo "  <th valign=\"$row[title_valign]\" align=\"$row[title_align]\" class=\"input-form-th\"$colspan nowrap=\"nowrap\">$title</th>\n";
                if ( !$colspan ) {
                    echo "  <td valign=\"$row[content_valign]\" align=\"$row[content_align]\"  class=\"input-form-td\">$row[contents]</td>\n";
                }
                echo "</tr>\n";
            }
        }
        echo "<tr class=\"input-form-tr\">\n";
        echo "  <td class=\"input-form-td-break\" align=\"center\" colspan=\"2\"><input type=\"submit\" value=\"$this->_submit\" class=\"input-form-submit\"></td>\n";
        echo "</tr>\n";

        echo "</table>\n";

        echo "\n";

        echo "</form>\n";
    }

    function time_select( $prefix, $blank_line = true, $time = "" )
    {
        if ( empty( $time ) ) $time = date( "H:i:s" );
        list( $hour, $minute, $second ) = explode( "-", $time );

        if ( $hour > 12 ) {
            $hour -= 12;
            $ampm = "PM";
        } else {
            $ampm = "AM";
        }

        for( $x = 0;$x <= 12;$x++ ) {
            if ( $x == 0 && $blank_line ) {
                $values[0] = "";
            } else {
                $key = ( $x < 10 ) ? "0$x" : $x;
                $values[$key] = $x;
            }
        }
        $data = $this->select_tag( $prefix . "hour", $values, $hour ) . " : ";

        array_merge( $values, range( 13, 60 ) );

        $data .= $this->select_tag( $prefix . "minute", $values, $minute ) . " : ";
        $data .= $this->select_tag( $prefix . "second", $values, $second ) . " ";

        $data .= $this->select_tag( $prefix . "ampm", array( "AM" => "AM", "PM" => "PM" ), $ampm );
    }

    function date_select( $prefix, $blank_line = true, $date = "TODAY", $year_start = "", $year_end = "" )
    {
        if ( $date == "TODAY" ) $date = date( "Y-m-d" );
        list( $year, $month, $day ) = explode( "-", $date );

        if ( empty( $year_start ) ) $year_start = date( "Y" );

        if ( empty( $year_end ) ) $year_end = date( "Y" ) + 2;

        for( $x = 0;$x <= 12;$x++ ) {
            if ( $x == 0 && $blank_line ) {
                $values[0] = "";
            } elseif ( $x > 0 ) {
                $key = ( $x < 10 ) ? "0$x" : $x;
                $values[$key] = date( "F", mktime( 0, 0, 0, $x ) );
            }
        }
        $data = $this->select_tag( $prefix . "month", $values, $month ) . " ";

        for( $x = 0;$x <= 31;$x++ ) {
            if ( $x == 0 && $blank_line ) {
                $values[0] = "";
            } elseif ( $x > 0 ) {
                $key = ( $x < 10 ) ? "0$x" : $x;
                $values[$key] = $x;
            }
        }

        $data .= $this->select_tag( $prefix . "day", $values, $day ) . ", ";

        unset( $values );
        if ( $blank_line ) $values = array( "" );
        for( $x = $year_start;$x <= $year_end;$x++ ) {
            $values[$x] = $x;
        }
        $data .= $this->select_tag( $prefix . "year", $values, $year );

        return $data;
    }

    function text_box( $name, $value, $size = 0, $maxlength = 0, $password = false, $extra = "" )
    {
        $type = ( $password ) ? "password" : "text";
        $data = "<input type=\"$type\" name=\"$name\"";
        if ( $size > 0 ) $data .= " size=\"$size\"";
        if ( $maxlength > 0 ) $data .= " maxlength=\"$maxlength\"";
        $value = htmlspecialchars( $value );
        $data .= " value=\"$value\" $extra>";

        return $data;
    }

    function textarea( $name, $value, $cols = 30, $rows = 5, $extra = "" )
    {
        $value = htmlspecialchars( $value );
        $data = "<textarea name=\"$name\" cols=\"$cols\" rows=\"$rows\" $extra>$value</textarea>";

        return $data;
    }

    function select_tag( $name, $values, $selected = "", $extra = "" )
    {
        $data = "<select name=\"$name\" $extra>\n";
        foreach( $values as $value => $text ) {
            $value = htmlspecialchars( $value );
            $text = htmlspecialchars( $text );
            $data .= "<option value=\"$value\"";
            if ( $value == $selected ) $data .= " selected=\"selected\"";
            $data .= ">$text</option>\n";
        }
        $data .= "</select>\n";
        return $data;
    }

    function select_tag_valaskey( $name, $values, $selected = "", $extra = "" )
    {
        $data = "<select name=\"$name\" $extra>\n";
        foreach( $values as $value => $text ) {
            $data .= "<option value=\"$text\"";
            $text = htmlspecialchars( $text );
            if ( $text == $selected ) $data .= " selected";
            $data .= ">$text</option>\n";
        }
        $data .= "</select>\n";
        return $data;
    }

    function radio_button( $name, $values, $selected = "", $separator = "&nbsp;&nbsp;", $extra = "" )
    {
        foreach( $values as $value => $text ) {
            $value = htmlspecialchars( $value );
            $text = htmlspecialchars( $text );
            $data .= "<input type=\"radio\" name=\"$name\" value=\"$value\"";
            if ( $selected == $value ) $data .= " checked";
            $data .= " $extra>&nbsp;$text$separator";
        }
        return $data;
    }

    function checkbox( $name, $value, $caption, $checked = 0, $extra = "" )
    {
        $is_checked = ( !empty( $checked ) ) ? "checked" : "" ;

        $value = htmlspecialchars( $value );

        $data = "<nobr><input type=\"checkbox\" name=\"$name\" value=\"$value\" $is_checked $extra>&nbsp;$caption</nobr>";

        return $data;
    }

    // $list and $checklist are both associative and should have the same indicies
    function checkbox_list( $prefix, $list, $separator = "&nbsp;&nbsp;", $checklist = 0 )
    {
        // Get the listing of options to check into a array function library usable format
        if ( empty( $checklist ) ) {
            $checked_items = array();
        } else {
            if ( !is_array( $checklist ) ) {
                $checked_items = array( $checklist );
            } else {
                $checked_items = $checklist;
            }
        }
        // Loop through all the array elements and call function to generate the appropriate input tag
        foreach( $list as $index => $info ) {
            $check_name = $prefix . "[" . $index . "]";
            $check_value = $info["value"];
            $check_caption = $info["caption"];
            $is_checked = ( in_array( $check_value, $checked_items ) ) ? 1 : 0;

            $data .= $this->checkbox( $check_name, $check_value, $check_caption, $is_checked ) . $separator;
        }

        return $data;
    }

}

?>
