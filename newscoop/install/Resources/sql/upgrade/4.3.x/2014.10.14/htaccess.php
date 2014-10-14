<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../').'/';

require_once $newscoopDir.'vendor/autoload.php';

use Newscoop\Installer\Services\FinishService;

$finishService = new FinishService();
$result = $finishService->setupHtaccess();
if ($result) {
    $msg = $result . " Please copy it manually.";
    $logger->addError($msg);
    array_splice($upgradeErrors, 0, 0, array($msg));
}
