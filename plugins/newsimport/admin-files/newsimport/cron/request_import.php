#!/usr/bin/env php
<?php

function newsimport_ask_for_import() {
    set_time_limit(0);

    $incl_dir = dirname(dirname(dirname(dirname(__FILE__)))).DIRECTORY_SEPARATOR.'include'.DIRECTORY_SEPARATOR;
    require($incl_dir . 'default_access.php');
    require($incl_dir . 'news_feeds_intall.php');

    $request_url = $newsipmort_install;
    if ('/' != $request_url[strlen($request_url)-1]) {
        $request_url .= '/';
    }
    $request_url .= '_newsimport/?';
    //&newsfeed=events_1

    $one_limit = 500;
    $request_url .= 'newsauth=' . urlencode($newsimport_default_access) . '&newslimit=' . $one_limit;

    $request_offsets = array(0);
    for ($ind = 1; $ind <= 20; $ind++) {
        $request_offsets[] = $ind * $one_limit;
    }

    foreach ($request_offsets as $one_offset) {
        try {
            $one_request = $request_url . '&newsoffset=' . $one_offset;
            echo $one_request . "\n";
            file_get_contents($one_request);
        }
        catch (Exception $exc) {}
    }

    //$fh = fopen('/tmp/d006', 'a');
    //fwrite($fh, "aaa\n");
    //fclose($fh);

}

newsimport_ask_for_import();

?>
