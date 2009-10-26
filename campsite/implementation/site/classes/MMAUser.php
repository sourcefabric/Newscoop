<?php

abstract class MMAUser
{
	const error_unspecified_driver = 300;
	const error_invalid_driver = 301;
	const error_invalid_driver_declaration = 302;

	static public function Factory($p_driver, $p_userName)
	{
		if (!is_string($p_driver) || empty($p_driver)) {
			return new PEAR_Error("Unspecified driver name", self::error_unspecified_driver);
		}
		$className = __CLASS__ . '_' . ucfirst($p_driver);
		$path = dirname(__FILE__);
		if (!file_exists("$path/$className.php")) {
			return new PEAR_Error("Invalid driver name $p_driver", self::error_invalid_driver);
		}
		require_once("$path/$className.php");
		if (!class_exists($className)) {
			return new PEAR_Error("Invalid driver declaration for $p_driver", self::error_invalid_driver_declaration);
		}
		return new $className($p_userName);
	}

	abstract public function __construct($p_userName = null);

	abstract public function create($p_userName, $p_password, $p_realName, $p_isAdmin = false);

    abstract public function delete();

    abstract public function changePassword($p_oldPassword, $p_password);

    abstract public function addToGroup($p_group);

    abstract public function removeFromGroup($p_group);
}

?>