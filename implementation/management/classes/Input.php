<?php
global $g_inputErrors;
$g_inputErrors = array();

class Input {
	
	/**
	 * Get an input value from the $_REQUEST array and check its type.
	 * The default value is returned if the value is not defined in the $_REQUEST array,
	 * or if the value does not match the required type.
	 *
	 * Use Input::isValid() to check if any errors were generated.
	 *
	 * @param string p_varName
	 *		The index into the $_REQUEST array.
	 *
	 * @param string p_type 
	 *		The type of data expected; can be 'int' or 'string'.  Default is 'string'.
	 *
	 * @param mixed p_defaultValue
	 * 		The default value to return if the value is not defined in the $_REQUEST array,
	 * 		or if the value does not match the required type.
	 *
	 * @param boolean p_errorsOk
	 *		Set to true to ignore any errors for this variable (i.e. Input::IsValid()
	 *		will still return true even if there are errors for this varaible).
	 *
	 * @return mixed
	 */
	function get($p_varName, $p_type = 'string', $p_defaultValue = null, $p_errorsOk = false) {
		global $g_inputErrors;
		
		if (!isset($_REQUEST[$p_varName])) {
			if (!$p_errorsOk) {
				$g_inputErrors[] = array($p_varName, 'not set');
			}
			return $p_defaultValue;
		}
		switch (strtolower($p_type)) {
		case 'int':
			if (!is_numeric($_REQUEST[$p_varName])) {
				if (!$p_errorsOk) {
					$g_inputErrors[] = array($p_varName, 'type');
				}
				return $p_defaultValue;
			}
			break;
		case 'string':
			if (!is_string($_REQUEST[$p_varName])) {
				if (!$errorsOk) {
					$g_inputErrors[] = array($p_varName, 'type');
				}
				return $p_defaultValue;
			}
			break;
		}
		return $_REQUEST[$p_varName];
	} // fn get
	
	
	/**
	 * Return FALSE if any calls to Input::get() resulted in an error.
	 * @return boolean
	 */
	function isValid() {
		global $g_inputErrors;
		if (count($g_inputErrors) > 0) {
			return false;
		}
		else {
			return true;
		}
	} // fn isValid
	
		
} // class Input

?>