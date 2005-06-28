<?php

require_once($_SERVER['DOCUMENT_ROOT']. '/classes/common.php');
load_common_include_files("$ADMIN_DIR/u_types");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/CampsiteInterface.php");
require_once($_SERVER['DOCUMENT_ROOT']."/db_connect.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/UserType.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/Log.php');

function read_utype_common_parameters()
{
	global $uType, $userOffs, $lpp;

	$uType = Input::Get('uType', 'string', '');
	$userOffs = Input::Get('userOffs', 'int', 0);
	if ($userOffs < 0)
		$userOffs = 0;
	$lpp = Input::Get('lpp', 'int', 20);
}

?>