<?php
/**
 * @package Campsite
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/db_connect.php');

class LoginAttempts {

	/**
	 * Delete IP records older than 12 hours from the database.
	 * @return void
	 */
	public static function DeleteOldLoginAttempts()
	{
		global $g_ado_db;
		$now = time();
		// Records are kept for 12 hours.
		$diff = $now - 43200;
        $queryStr = "DELETE FROM FailedLoginAttempts WHERE time_of_attempt <=".$diff;
	    $g_ado_db->Execute($queryStr);
	} // fn DeleteOldLoginAttempts


	/**
	 * Insert IP record to the database which has a failed login attempt.
	 *
	 * @return void
	 */
	public static function RecordLoginAttempt()
	{
		global $g_ado_db;
		$now = time();
		$userIp = getenv('REMOTE_ADDR');
		$queryStr = "INSERT INTO FailedLoginAttempts (ip_address,time_of_attempt) VALUES('".$userIp."','".$now."')";
		$g_ado_db->Execute($queryStr);
	 } // fn RecordLoginAttempt


	/**
     * Checks if failed login attempts exceeds the number of
     * failed login attempts saved in the System Preferences.
     *
	 * @return boolean
	 */
	public static function MaxLoginAttemptsExceeded()
	{
		global $g_ado_db;
		$userIp = getenv('REMOTE_ADDR');
		$preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
		$maxFailuresAllowed = $preferencesService->LoginFailedAttemptsNum;
		if (is_null($maxFailuresAllowed)) {
			$maxFailuresAllowed = 3;
		}
		$queryStr = "SELECT COUNT(*) FROM FailedLoginAttempts WHERE ip_address='".$userIp."'";
		$ip_num = $g_ado_db->GetOne($queryStr);

		if ($ip_num >= $maxFailuresAllowed) {
			return true;
		} else {
			return false;
		}
	} // fn MaxLoginAttemptsExceeded


	/**
	 * Resets the login counter by clearing all IP records
     * from the failed login attempt database after a sucessful login.
     *
	 * @return void
	 */
	public static function ClearLoginAttemptsForIp()
	{
		global $g_ado_db;
		$ip = getenv('REMOTE_ADDR');
		$queryStr = "DELETE FROM FailedLoginAttempts WHERE ip_address='".$ip."';";
		$g_ado_db->Execute($queryStr);
	} // fn ClearLoginAttemptsForIp

} // class Captcha
?>