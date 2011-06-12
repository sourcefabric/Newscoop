<?php
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeManagementService;

//@New theme management
$resourceId = new ResourceId('Publication/Edit');
$themeManagementService = $resourceId->getService(IThemeManagementService::NAME_1);

$themePath = $_REQUEST['themePath'];

if($themePath != null && $themePath != '0'){
	$allTemplates = $themeManagementService->getTemplates($themePath);
} else {
	$allTemplates = array();
}

$ret = array();
foreach ($allTemplates as $template){
	$ret[$template->getPath()] = $template->getName();
}

echo json_encode($ret);
exit;
