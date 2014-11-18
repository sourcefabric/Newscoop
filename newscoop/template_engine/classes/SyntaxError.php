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
define('SYNTAX_ERROR_INVALID_PARAMETER_VALUE', 'Invalid value <em>$1</em> of parameter <em>$2</em> in statement <em>$3</em>');
define('SYNTAX_ERROR_MISSING_PARAMETER', 'Missing parameter <em>$1</em> in statement <em>$2</em>');
define('SYNTAX_ERROR_INVALID_OPERATOR', 'Invalid operator <em>$1</em> of parameter <em>$2</em> in statement <em>$3</em>');
define('SYNTAX_ERROR_INVALID_ATTRIBUTE', 'Invalid attribute <em>$1</em> in statement <em>$2</em>, <em>$3</em> parameter');
define('SYNTAX_ERROR_INVALID_TEMPLATE', 'Invalid template <em>$1</em> specified in the <em>$2</em> form');

// Needed for localizer
//$translator->trans('Unknown error: $1');
//$translator->trans('Unrecognized tag <em>$1</em>');
//$translator->trans('Unknown reference <em>$1</em>');
//$translator->trans('Invalid property <em>$1</em> of object <em>$2</em>');
//$translator->trans('Invalid value <em>$1</em> of property <em>$2</em> of object <em>$3</em>');
//$translator->trans('Invalid parameter <em>$1</em> in statement <em>$2</em>');
//$translator->trans('Invalid value <em>$1</em> of parameter <em>$2</em> in statement <em>$3</em>');
//$translator->trans('Missing parameter <em>$1</em> in statement <em>$2</em>');
//$translator->trans('Invalid operator <em>$1</em> of parameter <em>$2</em> in statement <em>$3</em>');
//$translator->trans('Invalid attribute <em>$1</em> in statement <em>$2</em>, <em>$3</em> parameter');
//$translator->trans('Invalid template <em>$1</em> specified in the <em>$2</em> form');


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


	/**
	 * @param string $p_errorCode
	 * @param string|null $p_templateName
	 * @param string $p_line
	 * @param string[] $p_what
	 */
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
        $translator = \Zend_Registry::get('container')->getService('translator');
		if (is_null($this->m_errorCode)) {
			return null;
		}

		$errorMessage = $translator->trans(SyntaxError::ErrorMessage($this->m_errorCode), array('$1' => $this->m_what), 'preview');
		if (is_null($errorMessage)) {
			return null;
		}

		if (isset($this->m_templateName)) {
			$message .= $translator->trans('template', array(), 'preview') . ' ' . $this->m_templateName;
			if (isset($this->m_line)) {
				$message .= ', ' . $translator->trans('line', array(), 'preview') . ' ' . $this->m_line;
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


$GLOBALS['g_errorList'] = array();


function templateErrorHandler($p_errorCode, $p_errorString, $p_errorFile = null,
                              $p_errorLine = null, $p_errorContext = null)
{
    if (strncasecmp($p_errorString, 'Newscoop error:', strlen("Newscoop error:")) == 0) {
        $errorString = substr($p_errorString, strlen("Newscoop error:"));
    } elseif(strncasecmp($p_errorString, 'Smarty error:' ,strlen('Smarty error:')) == 0) {
        $errorString = substr($p_errorString, strlen("Smarty error:"));
    } else {
        return;
    }

    $what = null;

    if (preg_match('/unrecognized tag:?\s*\'?([^\(]*)\'?\s*\(/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_UNRECOGNIZED_TAG;
        $what = array($matches[1]);
    } elseif (preg_match('/(\$.+)\s+is\s+an\s+unknown\s+reference/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_UNKNOWN_REFERENCE;
        $what = array($matches[1]);
    } elseif (preg_match('/invalid\s+property\s+(.+)\s+of\s+object\s+(.*)/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_PROPERTY;
        $what = array($matches[1], $matches[2]);
    } elseif (preg_match('/invalid\s+value\s+(.+)\s+of\s+property\s+(.*)\s+of\s+object\s+(.*)/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_PROPERTY_VALUE;
        $what = array($matches[1], $matches[2], $matches[3]);
    } elseif (preg_match('/invalid\s+parameter\s+(.+)\s+in\s+(.*)/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_PARAMETER;
        $what = array($matches[1], $matches[2]);
    } elseif (preg_match('/invalid\s+value\s+(.+)\s+of\s+parameter\s+(.*)\s+in\s+statement\s+(.*)/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_PARAMETER_VALUE;
        $what = array($matches[1], $matches[2], $matches[3]);
    } elseif (preg_match('/missing\s+parameter\s+(.*)\s+in\s+statement\s+(.*)/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_MISSING_PARAMETER;
        $what = array($matches[1], $matches[2]);
    } elseif (preg_match('/invalid\s+operator\s+(.+)\s+of\s+parameter\s+(.*)\s+in\s+statement\s+(.*)/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_OPERATOR;
        $what = array($matches[1], $matches[2], $matches[3]);
    } elseif (preg_match('/invalid\s+attribute\s+(.+)\s+in\s+statement\s+(.*),\s+(.*)\s+parameter/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_ATTRIBUTE;
        $what = array($matches[1], $matches[2], $matches[3]);
    } elseif (preg_match('/invalid\s+template\s+(.*)\s+specified\s+in\s+the\s+(.*)\s+form/', $errorString, $matches)) {
        $errorCode = SYNTAX_ERROR_INVALID_TEMPLATE;
        $what = array($matches[1], $matches[2]);
    } else {
        $errorCode = SYNTAX_ERROR_UNKNOWN;
        $what = array($errorString);
    }

    if (preg_match('/\[in\s+([\d\w]*\.tpl)*\s+line\s+([\d]+)\s*\]/', $errorString, $matches)) {
        $errorFile = $matches[1];
        $errorLine = $matches[2];
    } else {
        $errorFile = null;
        $errorLine = null;
    }

    $error = new SyntaxError(SyntaxError::ConstructParameters($errorCode, $errorFile,
                             $errorLine, $what));
    $GLOBALS['g_errorList'][] = $error;
}

?>