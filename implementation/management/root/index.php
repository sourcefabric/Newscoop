<?php

$method = $_SERVER['REQUEST_METHOD'];
if ($method == 'POST')
	$params = copyParamArray($_POST);
else
	$params = readParameters($_SERVER['QUERY_STRING']);

$msg = "<CampsiteMessage MessageType=\"URLRequest\" MessageId=\"14214\">\n";
$msg .= "\t<HTTPHost>" . htmlspecialchars($_SERVER['HTTP_HOST']) . "</HTTPHost>\n";
$msg .= "\t<DocumentRoot>" . htmlspecialchars($_SERVER['DOCUMENT_ROOT']) . "</DocumentRoot>\n";
$msg .= "\t<RemoteAddress>" . htmlspecialchars($_SERVER['REMOTE_ADDR']) . "</RemoteAddress>\n";
$msg .= "\t<PathTranslated>" . htmlspecialchars($_SERVER['PATH_TRANSLATED']) . "</PathTranslated>\n";
$msg .= "\t<RequestMethod>" . htmlspecialchars($_SERVER['REQUEST_METHOD']) . "</RequestMethod>\n";
$msg .= "\t<RequestURI>" . htmlspecialchars($_SERVER['REQUEST_URI']) . "</RequestURI>\n";
$msg .= "\t<Parameters>\n";
for($i = 0; $i < count($params[0]); $i++)
	$msg .= "\t\t<Parameter Name=\"" . htmlspecialchars($params[1][$i]) . "\" Type=\"string\">"
	     . htmlspecialchars($params[2][$i]) . "</Parameter>\n";
$msg .= "\t</Parameters>\n";
$msg .= "\t<Cookies>\n";
foreach ($_COOKIE as $cookie=>$value)
	if ($value != "")
		$msg .= "\t\t<Cookie Name=\"" . htmlspecialchars($cookie) . "\">" . htmlspecialchars($value)
		     . "</Cookie>\n";
$msg .= "\t</Cookies>\n";
$msg .= "</CampsiteMessage>\n";

//echo "<pre>\n" . htmlspecialchars($msg) . "\n";
$size = sprintf("%04x", strlen($msg));
//echo "size: " . $size . "\n";

$address = "127.0.0.1";
$service_port = 2001;
@$socket = fsockopen($address, $service_port, $errno, $errstr, 30);
if (!$socket) {
	echo "$errstr ($errno)\n";
	die();
} else {
//	echo "OK.\n</pre>\n";
}

fwrite($socket, "0001 ");
fwrite($socket, $size . " ");
fwrite($socket, $msg);
// fclose($socket);
// exit(0);

stream_set_timeout($socket, 10);
do {
	$str = fread($socket, 1000);
	echo $str;
} while ($str != "");
fclose($socket);


function readParameters($queryString)
{
	$out = array();
	$queryString .= "&";
	preg_match_all("|([\w]*[%\d{2}]*)=([\w]*[%\d{2}]*)&|U", $queryString, $out, PREG_PATTERN_ORDER);
	return $out;
}

function copyParamArray($params)
{
	$dest = array();
	$i = 0;
	foreach($params as $param=>$val)
	{
		$dest[0][$i] = $param . "=" . $val;
		$dest[1][$i] = $param;
		$dest[2][$i] = $val;
		$i++;
	}
	return $dest;
}

?>
