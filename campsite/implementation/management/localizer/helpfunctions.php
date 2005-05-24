<?php

function XMLtoHTML($input)
{
    return "<p>".nl2br(str_replace( "\t", '&nbsp;&nbsp;&nbsp;&nbsp;',  htmlentities($input)))."</p>";
}


function ArrayToHTML($input)
{
    return "<p>".nl2br(str_replace(" ", "&nbsp;", print_r($input, true)))."</p>";
}


function pl($input)
{
    echo "<p>$input</p>";
}

if (!function_exists('cropstr')) {
    function cropStr ($input, $length, $char='')
    {
        if (is_numeric($length)) {
            if ($char) {
                if (strpos ($input, $char)) {
                    $len =  strrpos(substr($input, 0, $length), $char);
                }
            } else {
                $len = $length;
            }
            $output = substr ($input, 0, $len);
            if (strlen ($input)>$len) {
                $output .= "...";
            }
        } else {
            return $input;
        }

        return $output;
    }
}

function isInt ($in, $noZero = true)
{
    if ($noZero && !($in>0)) {
        return false;
    }
    if (preg_match('/^[0-9]*$/', $in)) {
        return true;
    }

    return false;
}

function Error($msg)
{
	if (!isset($GLOBALS['error'])) {
		$GLOBALS['error'] = array();
		$GLOBALS['error']['msg'] = '';
	}
    $GLOBALS['error']['msg'] .= "<div class='error'>$msg</div>";
}

if (!function_exists('putGS')) {
    function putGS($s)
    {
        global $g_translationStrings, $TOL_Language;
        $nr=func_num_args();
        if (!isset($g_translationStrings[$s]) || ($g_translationStrings[$s]==''))
            $my="$s (not translated)";
        else
            $my= $g_translationStrings[$s];
        if ($nr>1)
            for ($i=1;$i<$nr;$i++){
                $name='$'.$i;
                $val=func_get_arg($i);
                $my=str_replace($name,$val,$my);
            }
        echo $my;
    }
}

if (!function_exists('getGS')) {
    function getGS($s)
    {
        global $g_translationStrings, $TOL_Language;
        $nr=func_num_args();
        if (!isset($g_translationStrings[$s]) || ($g_translationStrings[$s]=='') )
            $my="$s (not translated)";
        else
            $my= $g_translationStrings[$s];
        if ($nr>1)
            for ($i=1;$i<$nr;$i++){
                $name='$'.$i;
                $val=func_get_arg($i);
                $my=str_replace($name,$val,$my);
            }
        return  $my;
    }
}

?>