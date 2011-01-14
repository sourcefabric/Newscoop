<?php

if(!defined("PHORUM")) return;

function phorum_mod_replace ($data)
{
    $PHORUM=$GLOBALS["PHORUM"];

    if(isset($PHORUM["mod_replace"])){

        foreach($data as $key => $message){

            if(isset($message["body"])){

                $body=$message["body"];
    
                foreach($PHORUM["mod_replace"] as $entry){
    
                    $entry["replace"]=str_replace(array("<", ">"), array("<", ">"), $entry["replace"]);
    
                    if($entry["pcre"]){
                        $body=preg_replace("/$entry[search]/is", $entry["replace"], $body);
                    } else {
                        $body=str_replace($entry["search"], "$entry[replace]", $body);
                    }
    
                }
    
                $data[$key]["body"]=$body;
            }
        }

    }

    return $data;

}

?>
