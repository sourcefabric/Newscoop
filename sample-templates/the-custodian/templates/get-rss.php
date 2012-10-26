<?php
$url = "http://campsite-design.sourcefabric.org/en/?tpl=420";
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
";
$xml .= @file_get_contents($url);
file_put_contents("/var/www/campsite-design.git/campsite/src/templates/feed/index-en.rss", $xml);

$url = "http://campsite-design.sourcefabric.org/es/?tpl=420";
$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>
";
$xml .= @file_get_contents($url);
file_put_contents("/var/www/campsite-design.git/campsite/src/templates/feed/index-es.rss", $xml);
?>