<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../').'/';

require_once $newscoopDir.'vendor/autoload.php';

use Newscoop\Installer\Services\FinishService;

$upgradeErrors =array();
$finishService = new FinishService();
$result = $finishService->setupHtaccess();
if (!empty($result)) {
    $msg = implode(" ", $result) . " Most likely it's caused by wrong permissions. Make a backup of .htaccess manually.\nThen copy " . $newscoopDir ."htaccess.dist to " . $newscoopDir . ".htaccess";
    $logger->addError($msg);
    $upgradeErrors[] = $msg;
}
