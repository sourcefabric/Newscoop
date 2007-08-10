<?php
$mod_perm = array();

$ext_rightsFields = array(
	#'ManagePhorum'            => 'N',
	'ManagePoll'              => 'N',
	#'ManageTimer'             => 'N',
	#'ModerateTimer'           => 'N',
	#'ManageSearchtracker'     => 'N',
	#'ManageNewsletter'        => 'N',		
	#'ModerateArticleComments' => 'N',		
	#'VotingRes'               => 'N',
	#'ProfileAttributes'       => 'N'
);

foreach ($ext_rightsFields as $field=>$value) {
	$val = Input::Get($field, 'string', 'off');
	$permissionEnabled = ($val == 'on') ? true : false;
	$mod_perm[$field] = $permissionEnabled; 
}

$editUser->updatePermissions($mod_perm);
?>