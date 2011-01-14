<?php

require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/classes/CampSession.php');


class SecurityToken
{
	/**
	 * Campsite security token used in forms and URLs
	 */
	const SECURITY_TOKEN = 'security_token';


	public static function GetToken()
	{
		return CampSession::singleton()->getToken();
	}


	public static function URLParameter()
	{
		return urlencode(self::SECURITY_TOKEN) . '=' . urlencode(CampSession::singleton()->getToken());
	}


	public static function FormParameter()
	{
		return '<input type="hidden" name="' . self::SECURITY_TOKEN . '" '
		. 'value="' . htmlspecialchars(CampSession::singleton()->getToken()) . '" />';
	}


	public static function isValid()
	{
		return CampSession::singleton()->getToken()
		=== Input::Get(self::SECURITY_TOKEN, 'string', null, true);
	}
}

?>