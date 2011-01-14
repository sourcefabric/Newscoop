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

if ( !defined( "PHORUM_CONTROL_CENTER" ) ) return;

function phorum_cc_get_language_info()
{
    $langs = phorum_get_language_info();
    $f_langs = array();
    $profile = $GLOBALS['PHORUM']['DATA']['PROFILE'];
    if ( !isset( $profile['user_language'] ) )
        $defsel = " selected=\"selected\"";
    else
        $defsel = "";
    $f_langs[] = array( 'file' => '', 'name' => $GLOBALS['PHORUM']['DATA']['LANG']['Default'], 'sel' => $defsel );

    foreach( $langs as $entry => $name ) {
        $sel = "";
        if ( isset( $profile['user_language'] ) && $profile['user_language'] == $entry ) {
            $sel = " selected=\"selected\"";
        } 
        $f_langs[] = array( 'file' => $entry, 'name' => $name, 'sel' => $sel );
    } 
    return $f_langs;
} 

function phorum_cc_get_template_info()
{
    $langs = phorum_get_template_info();
    $profile = $GLOBALS['PHORUM']['DATA']['PROFILE'];

    $f_langs = array();
    if ( !isset( $profile['user_template'] ) )
        $defsel = " selected=\"selected\"";
    else
        $defsel = "";
    $f_langs[] = array( 'file' => '', 'name' => $GLOBALS['PHORUM']['DATA']['LANG']['Default'], 'sel' => $defsel );

    foreach( $langs as $entry => $name ) {
        $sel = "";
        if ( isset( $profile['user_template'] ) && $profile['user_template'] == $entry ) {
            $sel = " selected=\"selected\"";
        } 
        $f_langs[] = array( 'file' => $entry, 'name' => $name, 'sel' => $sel );
    } 
    return $f_langs;
} 

if ( count( $_POST ) ) {
    // dst is time + 1 hour
    if(isset($_POST['tz_offset']) && $_POST['tz_offset'] != -99) {
        if($_POST['tz_offset'] && isset($_POST['is_dst']) && $_POST['is_dst']) {
            $_POST['tz_offset']=++$_POST['tz_offset']."";
        }
    }
    // unsetting dst if not checked
    if(!isset($_POST['is_dst'])) {
        $_POST['is_dst']=0;   
    }
    
    list($error,$okmsg) = phorum_controlcenter_user_save( $panel );
} 

if ( isset( $PHORUM["user_time_zone"] ) ) {
    $PHORUM['DATA']['PROFILE']['TZSELECTION'] = $PHORUM["user_time_zone"];
} 
// compute the tz-array
if ( !isset( $PHORUM['DATA']['PROFILE']['tz_offset'] ) || $PHORUM['DATA']['PROFILE']['tz_offset'] == -99 ) {
    $defsel = " selected=\"selected\"";
} else {
    $defsel = "";
} 

// remove dst from tz_offset
if(isset($PHORUM['DATA']['PROFILE']['is_dst']) && $PHORUM['DATA']['PROFILE']['is_dst']) {
    $PHORUM['DATA']['PROFILE']['tz_offset']=--$PHORUM['DATA']['PROFILE']['tz_offset']."";
}

$PHORUM["DATA"]["TIMEZONE"][] = array( 'tz' => '-99', 'str' => $PHORUM['DATA']['LANG']['Default'], 'sel' => $defsel );
foreach( $PHORUM['DATA']['LANG']['TIME'] as $tz => $str ) {
    if ( isset($PHORUM['DATA']['PROFILE']['tz_offset']) && $PHORUM['DATA']['PROFILE']['tz_offset'] === "$tz" ) {
        $sel = " selected";
    } else {
        $sel = "";
    } 
    $PHORUM["DATA"]["TIMEZONE"][] = array( 'tz' => $tz, 'str' => $str, 'sel' => $sel );
} 

$PHORUM['DATA']['LANGUAGES'] = phorum_cc_get_language_info();
if ( isset( $PHORUM["user_template"] ) ) {
    $PHORUM['DATA']['PROFILE']['TMPLSELECTION'] = $PHORUM["user_template"];
} 
$PHORUM['DATA']['TEMPLATES'] = phorum_cc_get_template_info();

$PHORUM["DATA"]["PROFILE"]["block_title"] = $PHORUM["DATA"]["LANG"]["EditBoardsettings"];
$PHORUM['DATA']['PROFILE']['BOARDSETTINGS'] = 1;
$template = "cc_usersettings";

?>
