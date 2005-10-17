<?PHP

class ParserCom {
	function __buildResetCacheMessage($p_type, $p_parameters)
	{
		$msg = "<CampsiteMessage MessageType=\"ResetCache\">\n";
		$msg .= "\t<CacheType>" . htmlspecialchars($p_type) . "</CacheType>\n";
		$msg .= "\t<Parameters>\n";
		if (is_array($p_parameters))
			foreach ($p_parameters as $name => $value)
				$msg .= "\t\t<Parameter Name=\"" . htmlspecialchars($name) . "\">" 
				     . htmlspecialchars($value) . "</Parameter>\n";
		$msg .= "\t</Parameters>\n";
		$msg .= "</CampsiteMessage>\n";
		$size = sprintf("%04x", strlen($msg));
		$msg = "0002 " . $size . " " . $msg;
	//	echo "<pre>sending ".htmlentities($msg)."</pre>";
		return $msg;
	}
	
	
	function __buildRestartServerMessage()
	{
		$msg = "<CampsiteMessage MessageType=\"RestartServer\">\n</CampsiteMessage>\n";
		$size = sprintf("%04x", strlen($msg));
		$msg = "0003 " . $size . " " . $msg;
		return $msg;
	}
	
	
	function GetServerPort()
	{
		global $Campsite;
		return $Campsite['PARSER_PORT'];
	} // fn GetServerPort

	
	/**
	 * @param string $p_type
	 *		Can be one of: "publications", "topics", "article_types"
	 * @param string $p_operation
	 *		Can be one of: "create", "delete", "modify"
	 * @param array $p_params
	 */
	function SendMessage($p_type, $p_operation, $p_params)
	{
		$address = "127.0.0.1";
		$server_port = ParserCom::GetServerPort();
		$params = $p_params;
		$params["operation"] = $p_operation;
		$msg = ParserCom::__buildResetCacheMessage($p_type, $p_params);
		
		@$socket = fsockopen($address, $server_port, $errno, $errstr, 30);
		if (!$socket) {
			$err_msg = "Unable to connect to server: " . $errstr . " (" . $errno . ")";
			echo $err_msg;
			return false;
		}
	
		fwrite($socket, $msg);
		fflush($socket);
		fclose($socket);
		return true;
	} // fn SendMessage
	
} // class ParserCom

?>