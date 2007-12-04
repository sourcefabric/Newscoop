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
//   July 19 Fixed by Dagon, Date format and Location default                 //
////////////////////////////////////////////////////////////////////////////////

define('phorum_page', 'rss');

include_once("./common.php");
include_once("./include/format_functions.php");

// check this forum allows RSS
if(!$PHORUM['use_rss']){
    exit();
}

$cache_key = $_SERVER["QUERY_STRING"].",".$PHORUM["user"]["user_id"]; 
$data = phorum_cache_get("rss", $cache_key);

if(empty($data)){

    if($PHORUM["forum_id"]==$PHORUM["vroot"]){
        $forums = phorum_db_get_forums(0, -1, $PHORUM["vroot"]);
        $forum_ids = array_keys($forums);
    } elseif($PHORUM["folder_flag"] && $PHORUM["vroot"]==0 && $PHORUM["forum_id"]!=0){
        // we don't support rss for normal folders
        exit();
    } else {
        $forum_ids = $PHORUM["forum_id"];
        $forums = phorum_db_get_forums($PHORUM["forum_id"]);
    }
    
    // find default forum for announcements
    foreach($forums as $forum_id=>$forum){
        if($forum["folder_flag"]){
            unset($forums[$forum_id]);
        } elseif(empty($default_forum_id)) { 
            $default_forum_id = $forum_id;
        }
    }
    
    $PHORUM["threaded_list"]=false;
    $PHORUM["float_to_top"]=false;
    
    // get the thread set started
    $rows = array();
    $thread = (isset($PHORUM["args"][1])) ? (int)$PHORUM["args"][1] : 0;

    $rows = phorum_db_get_recent_messages(30, $forum_ids, $thread);
    
    unset($rows["users"]);
    
    $items = array();
    $pub_date=0;
    foreach($rows as $key => $row){
    
        if(!$PHORUM["forum_id"]){
            $row["subject"]="[".$forums[$row["forum_id"]]["name"]."] ".$row["subject"];
        }
    
        $forum_id = ($row["forum_id"]==0) ? $default_forum_id : $row["forum_id"];
    
        $items[]=array(
            "pub_date" => date("r",$row["datestamp"]),
            "url" => phorum_get_url(PHORUM_FOREIGN_READ_URL, $forum_id, $row["thread"], $row["message_id"]),
            "headline" => $row["subject"],
            "description" => strip_tags($row["body"]),
            "author" => $row["author"],
            "category" => $forums[$row["forum_id"]]["name"]
        );
    
    
        $pub_date = max($row["datestamp"], $pub_date);
    
    }
    
    if (!$PHORUM['locale']) $PHORUM['locale'] ="en"; //if locale not set make it 'en'
    
    if($PHORUM["forum_id"]){
        $url = phorum_get_url(PHORUM_LIST_URL);
        $name = $PHORUM["name"];
        $description = strip_tags($PHORUM["description"]);
    } else {
        $url = phorum_get_url(PHORUM_INDEX_URL);
        $name = $PHORUM["title"];
        $description = "";
    }
    
    $channel = array(
    
        "name" => $name,
        "url" => $url,
        "description" => $description,
        "pub_date" => date("r",$pub_date),
        "language" => $PHORUM['locale']
    
    );
    
    $data = create_rss_feed($channel, $items);

}

$charset = '';
if (! empty($GLOBALS["PHORUM"]["DATA"]["CHARSET"])) {
    $charset = '; charset=' . htmlspecialchars($GLOBALS["PHORUM"]["DATA"]['CHARSET']);
}
header("Content-Type: text/xml$charset");

echo $data;

phorum_cache_put("rss", $cache_key, $data, 300);

/*******************************************************/

function create_rss_feed($channel, $items)
{

    if(empty($items)){
        return;
    }

    $encoding = '';
    if (! empty($GLOBALS["PHORUM"]["DATA"]["CHARSET"])) {
        $encoding = 'encoding="' . htmlspecialchars($GLOBALS["PHORUM"]["DATA"]['CHARSET']) . '"';
    }

    $data ="<?xml version=\"1.0\" $encoding ?>\n";
    $data.="<rss version=\"2.0\">\n";
    $data.="  <channel>\n";
    $data.="    <title>".htmlspecialchars(strip_tags($channel["name"]))."</title>\n";
    $data.="    <link>$channel[url]</link>\n";
    $data.="    <description><![CDATA[$channel[description]]]></description>\n";
    $data.="    <language>$channel[language]</language>\n";

    $data.="    <pubDate>$channel[pub_date]</pubDate>\n";
    $data.="    <lastBuildDate>$channel[pub_date]</lastBuildDate>\n";
    $data.="    <category>".htmlspecialchars(strip_tags($channel["name"]))."</category>\n";
    $data.="    <generator>Phorum ".PHORUM."</generator>\n";

    $data.="    <ttl>600</ttl>\n";

    foreach($items as $item){
        $data.="    <item>\n";
        $data.="      <title>".htmlspecialchars($item['headline'])."</title>\n";
        $data.="      <link>$item[url]</link>\n";
        $data.="      <author>".htmlspecialchars($item['author'])."</author>\n";
        $data.="      <description><![CDATA[".htmlspecialchars($item['description'])."]]></description>\n";
        $data.="      <category>".htmlspecialchars(strip_tags($item['category']))."</category>\n";
        $data.="      <guid isPermaLink=\"true\">$item[url]</guid>\n";
        $data.="      <pubDate>$item[pub_date]</pubDate>\n";
        $data.="    </item>\n";
    }

    $data.="  </channel>\n";
    $data.="</rss>\n";

    return $data;

}


?>
