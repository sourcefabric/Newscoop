<?php

$newscoopDir = realpath(dirname(__FILE__).'/../../../../../../').'/';

require_once $newscoopDir.'vendor/autoload.php';

use Newscoop\Installer\Services\FinishService;

$finishService = new FinishService();
$finishService->setupHtaccess();
