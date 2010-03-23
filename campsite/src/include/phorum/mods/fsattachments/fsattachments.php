<?php


function fsattachments_before_attach($array)
{
    list($message, $file) = $array;

    $GLOBALS["PHORUM"]["FSATTACH"]["file_data"]=$file["data"];
    $file["data"]="";

    return array($message, $file);

}

function fsattachments_after_attach($array)
{
    list($message, $file) = $array;

    $file_path = _fsattachments_get_file_path($file["file_id"], $file["name"]);

    if(!file_exists(dirname($file_path))){
        mkdir(dirname($file_path), 0755);
    }


    $fp = fopen($file_path, "w");
    fputs($fp, base64_decode($GLOBALS["PHORUM"]["FSATTACH"]["file_data"]));
    fclose($fp);

    // don't modify the data, so just return the array
    return $array;
}

function fsattachments_file($array)
{
    list($mime, $file) = $array;

    $file_path = _fsattachments_get_file_path($file["file_id"], $file["filename"]);

    $fp = fopen($file_path, "r");
    $file["file_data"] = base64_encode(fread($fp, filesize($file_path)));
    fclose($fp);

    return array($mime, $file);

}


function _fsattachments_get_file_path($file_id, $filename)
{
    if(strlen($file_id)<3){
        $dir="000";
    } else {
        $dir=substr($file_id, 0, strlen($file_id)-2)."00";
    }

    $ext = substr($filename, strrpos($filename, ".")+1);

    $file_path = $GLOBALS["PHORUM"]["fsattachments"]["path"]."/$dir/$file_id.$ext";

    return $file_path;
}

?>
