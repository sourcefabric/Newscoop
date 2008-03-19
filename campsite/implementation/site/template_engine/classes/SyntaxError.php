<?php

define('SYNTAX_ERROR_CODE', 'error_code');
define('SYNTAX_ERROR_TEMPLATE', 'template_name');
define('SYNTAX_ERROR_LINE', 'line');
define('SYNTAX_ERROR_WHAT', 'what');

define('WHAT_STATEMENT', 'statement');
define('WHAT_PARAMETER', 'parameter');
define('WHAT_VALUE', 'value');

define('SYNTAX_ERROR_UNKNOWN', 'Unknown error: $1');
define('SYNTAX_ERROR_UNRECOGNIZED_TAG', 'Unrecognized tag <em>$1</em>');
define('SYNTAX_ERROR_UNKNOWN_REFERENCE', 'Unknown reference <em>$1</em>');
define('SYNTAX_ERROR_INVALID_PROPERTY', 'Invalid property <em>$1</em> of object <em>$2</em>');
define('SYNTAX_ERROR_INVALID_PROPERTY_VALUE', 'Invalid value <em>$1</em> of property <em>$2</em> of object <em>$3</em>');
define('SYNTAX_ERROR_INVALID_PARAMETER', 'Invalid parameter <em>$1</em> in statement <em>$2</em>');
define('SYNTAX_ERROR_INVALID_PARAMETER_VALUE', 'Invalid parameter value <em>$1</em> of parameter <em>$2</em> in statement <em>$3</em>');
define('SYNTAX_ERROR_MISSING_PARAMETER', 'Missing parameter <em>$1</em> in statement <em>$2</em>');

//getGS("Unknown error: <em>$1</em>");
//getGS("Unrecognized tag <em>$1</em>");
//getGS("Unknown reference <em>$1</em>");
//getGS("Invalid property <em>$1</em> of object <em>$2</em>");
//getGS("Invalid value <em>$1</em> of property <em>$2</em> of object <em>$3</em>");
//getGS("Invalid parameter <em>$1</em> in statement <em>$2</em>");
//getGS("Invalid parameter value <em>$1</em> of parameter <em>$2</em> in statement <em>$3</em>");
//getGS("Missing parameter <em>$1</em> in statement <em>$2</em>");


class SyntaxError {
	private $m_errorCode = null;

	private $m_templateName = null;

	private $m_line = null;

	private $m_what = null;


	public function __construct($p_parameters)
	{
		if (!is_array($p_parameters) || !array_key_exists(SYNTAX_ERROR_CODE, $p_parameters)) {
			return;
		}

		$this->m_errorCode = $p_parameters[SYNTAX_ERROR_CODE];
		if (isset($p_parameters[SYNTAX_ERROR_TEMPLATE])) {
			$this->m_templateName = $p_parameters[SYNTAX_ERROR_TEMPLATE];
			$this->m_line = isset($p_parameters[SYNTAX_ERROR_LINE]) ?
								$p_parameters[SYNTAX_ERROR_LINE] : null;
		}
		$this->m_what = isset($p_parameters[SYNTAX_ERROR_WHAT]) ?
							$p_parameters[SYNTAX_ERROR_WHAT] : null;
	}


	public static function ConstructParameters($p_errorCode, $p_templateName, $p_line = null,
											   $p_what = null)
	{
		$parameters[SYNTAX_ERROR_CODE] = $p_errorCode;
		$parameters[SYNTAX_ERROR_TEMPLATE] = $p_templateName;
		if (isset($p_line)) {
			$parameters[SYNTAX_ERROR_LINE] = $p_line;
		}
		if (isset($p_what)) {
			$parameters[SYNTAX_ERROR_WHAT] = $p_what;
		}
		return $parameters;
	}


	public function getMessage()
	{
		if (is_null($this->m_errorCode)) {
			return null;
		}

		$errorMessage = getGS(SyntaxError::ErrorMessage($this->m_errorCode), $this->m_what);
		if (is_null($errorMessage)) {
			return null;
		}

		if (isset($this->m_templateName)) {
			$message .= getGS('template') . ' ' . $this->m_templateName;
			if (isset($this->m_line)) {
				$message .= ', ' . getGS(' line') . ' ' . $this->m_line;
			}
			$message .= ': ';
		}

		$message .= $errorMessage;

		return $message;
	}


	private static function ErrorMessage($p_errorCode)
	{
		return $p_errorCode;
	}


	public function getErrorCode()
	{
		return $this->m_errorCode;
	}


	public function getTemplateName()
	{
		return $this->m_templateName;
	}


	public function getLine()
	{
		return $this->m_line;
	}


	public function what()
	{
		return $this->m_what;
	}

}

?>