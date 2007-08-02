<?php

if(!defined("PHORUM")) return;

// BB Code Phorum Mod
function phorum_bb_code($data)
{
    $PHORUM = $GLOBALS["PHORUM"];

    $search = array(
        "/\[img\]((http|https|ftp):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%# ]+?)\[\/img\]/is",
        "/\[url\]((http|https|ftp|mailto):\/\/([a-z0-9\.\-@:]+)[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),\#%~ ]*?)\[\/url\]/is",
        "/\[url=((http|https|ftp|mailto):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%# ]+?)\](.+?)\[\/url\]/is",
        "/\[email\]([a-z0-9\-_\.\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+?)\[\/email\]/ies",
        "/\[color=([\#a-z0-9]+?)\](.+?)\[\/color\]/is",
        "/\[size=([+\-\da-z]+?)\](.+?)\[\/size\]/is",
        "/\[b\](.+?)\[\/b\]/is",
        "/\[u\](.+?)\[\/u\]/is",
        "/\[i\](.+?)\[\/i\]/is",
        "/\[s\](.+?)\[\/s\]/is",
        "/\[center\](.+?)\[\/center\]/is",
        "/\[hr\]/i",
        "/\[code\](.+?)\[\/code\]/is",
        "/\[sub\](.+?)\[\/sub\]/is",
        "/\[sup\](.+?)\[\/sup\]/is",
    );

    // add extra tags to links, if enabled in the admin settings page

    $extra_link_tags = "";

    if(isset($PHORUM["mod_bb_code"])){ // check for settings file before using settings-dependent variables
        if ($PHORUM["mod_bb_code"]["links_in_new_window"]){
            $extra_link_tags .= "target=\"_blank\" ";
        }
        if ($PHORUM["mod_bb_code"]["rel_no_follow"]){
            $extra_link_tags .= "rel=\"nofollow\" ";
        }
    }

    $replace = array(
        "<img src=\"$1\" />",
        "[<a $extra_link_tags href=\"$1\">$3</a>]",
        "<a $extra_link_tags href=\"$1\">$3</a>",
        "'<a $extra_link_tags href=\"'.phorum_html_encode('mailto:$1').'\">'.phorum_html_encode('$1').'</a>'",
        "<span style=\"color: $1\">$2</span>",
        "<span style=\"font-size: $1\">$2</span>",
        "<strong>$1</strong>",
        "<u>$1</u>",
        "<i>$1</i>",
        "<s>$1</s>",
        "<center class=\"bbcode\">$1</center>",
        "<hr class=\"bbcode\" />",
        "<pre class=\"bbcode\">$1</pre>",
        "<sub class=\"bbcode\">$1</sub>",
        "<sup class=\"bbcode\">$1</sup>",
    );

    $quote_search = array(
        "/\[quote\]/is",
        "/\[quote ([^\]]+?)\]/is",
        "/\[quote=([^\]]+?)\]/is",
        "/\[\/quote\]/is"
    );

    $quote_replace = array(
        "<blockquote class=\"bbcode\">".$PHORUM["DATA"]["LANG"]["Quote"] . ":<div>",
        "<blockquote class=\"bbcode\">".$PHORUM["DATA"]["LANG"]["Quote"] . ":<div><strong>$1</strong><br />",
        "<blockquote class=\"bbcode\">".$PHORUM["DATA"]["LANG"]["Quote"] . ":<div><strong>$1</strong><br />",
        "</div></blockquote>"
    );

    foreach($data as $message_id => $message){

        if(isset($message["body"])){

            // do BB Code here
            $body = $message["body"];

            $rnd=substr(md5($body.time()), 0, 4);

            // convert bare urls into bbcode tags as best we can
            // the haystack has to have a space in front of it for the preg to work.
            $body = preg_replace("/([^='\"(\[url\]|\[img\])])((http|https|ftp):\/\/[a-z0-9;\/\?:@=\&\$\-_\.\+!*'\(\),~%#]+)/i", "$1:$rnd:$2:/$rnd:", " $body");

            // stip puncuation from urls
            if(preg_match_all("!:$rnd:(.+?):/$rnd:!i", $body, $match)){

                $urls = array_unique($match[1]);

                foreach($urls as $key => $url){
                    // stip puncuation from urls
                    if(preg_match("|[^a-z0-9=&/\+_]+$|i", $url, $match)){

                        $extra = $match[0];
                        $true_url = substr($url, 0, -1 * (strlen($match[0])));

                        $body = str_replace("$url:/$rnd:", "$true_url:/$rnd:$extra", $body);

                        $url = $true_url;
                    }

                    $body = str_replace(":$rnd:$url:/$rnd:", "[url]{$url}[/url]", $body);
                }

            }

            // no sense doing any of this if there is no [ in the body
            if(strstr($body, "[")){

                // convert bare email addresses into bbcode tags as best we can.
                $body = preg_replace("/([a-z0-9][a-z0-9\-_\.\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+[a-z0-9])/i", "[email]$1[/email]", $body);

                // clean up any BB code we stepped on.
                $body = str_replace("[email][email]", "[email]", $body);
                $body = str_replace("[/email][/email]", "[/email]", $body);

                // fiddle with white space around quote and code tags.
                $body=preg_replace("/\s*(\[\/*(code|quote)\])\s*/", "$1", $body);

                // run the pregs defined above
                $body = preg_replace($search, $replace, $body);

                // quote has to be handled differently because they can be embedded.
                // we only do quote replacement if we have matching start and end tags
                if(strstr($body, "[quote") && substr_count($body, "[quote")==substr_count($body, "[/quote]")){
                    $body = preg_replace($quote_search, $quote_replace, $body);
                }


            }


            $data[$message_id]["body"] = $body;
        }
    }

    return $data;
}


function phorum_bb_code_quote ($array)
{
    $PHORUM = $GLOBALS["PHORUM"];

    if(isset($PHORUM["mod_bb_code"]) && $PHORUM["mod_bb_code"]["quote_hook"]){
        return "[quote $array[0]]$array[1][/quote]";
    }
}
?>