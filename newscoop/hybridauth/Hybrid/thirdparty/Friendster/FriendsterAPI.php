<?php
/*
 * Friendster API client core classes
 * Copyright Friendster, Inc. 2002-2008
 */

$GLOBALS['friendster_api']['debug'] = false;
 
class FriendsterAPIException extends Exception {
	public function __construct($code, $message = null) {
		if (is_null($message)) {
			$FAPIEC = new FriendsterAPIErrorCodes();
			$message = $FAPIEC->getDescription($code);
		}
		parent::__construct($message, $code);
	}
	
	public function __toString() {
		return __CLASS__ . ": [{$this->code}]: {$this->message}";
	}	
}   

class FriendsterAPIResponseType {
	const SARRAY = 0;
	const RAW = 1;
	const XML = 2;
	const JSON = 3;
}

class FriendsterAPIErrorCodes {
	const SUCCESS = 0;
	
	/* General */
	const UNKNOWN = 1;
	
	const METHOD = 3;
	
	const XML = 6;
	
	const POST = 8;
	const URI = 9;
	
	const REQUEST = 10;

	const CURL = 60;
	const CURL_MISSING = 61;
	
	const NOCURL_GET = 62;
	const NOCURL_POST = 63;

	const AUTH_TOKEN = 200;
	const AUTH_TOKEN_EMPTY = 201;
	const AUTH_TOKEN_MISSING = 202;
	
	const SESSION = 210;
	const SESSION_EMPTY = 211;
	const SESSION_MISSING = 212;
	
	const ARGS = 220;

	const SIGNATURE = 230;
	
	public $api_error_description = array (
		self::METHOD				=>	'Method Invalid',
		self::REQUEST				=>	'Request type unknown',
		self::XML					=>	'XML Invalid',
		self::CURL					=>	'Curl Error',
		self::CURL_MISSING			=>	'Curl Not Installed',
		self::NOCURL_GET			=>	'GET failed, non-curl',
		self::NOCURL_POST			=>	'POST failed, non-curl',
		self::POST					=>	'Post data invalid',
		self::AUTH_TOKEN			=>	'Auth Token Invalid',
		self::AUTH_TOKEN_EMPTY		=>	'Auth Token Empty',
		self::AUTH_TOKEN_MISSING	=>	'Auth Token Unavailable',
		self::SESSION				=>	'Session Invalid',
		self::SESSION_EMPTY			=>	'Session Empty',
		self::SESSION_MISSING		=>	'Session Unavailable',
		self::ARGS		=>	'Empty array of arguments',
		self::SIGNATURE		=>	'Invalid signature',
	);
	
	public function getDescription($code) {
		return array_key_exists($code, $this->api_error_description) 
			? $this->api_error_description[$code]
			: strval($code);
	}
}


class Request {
	
	const TIMEOUT = 30; 
	const GET = 'GET';
	const POST = 'POST';
	const PUT = 'PUT';
	const DELETE = 'DELETE';
	const BLOCK_SIZE = 4096;
	const USER_AGENT = 'FriendsterAPI PHP5 Client 1.0';
	
	function execute($url, $vars = null, $method = 'GET') {
	    
	    if (( $method == self:: GET ) && (!is_null($vars))) {
	    	$url .= "?$vars";
	    }
	    
	    if (function_exists('curl_init')) {
		    $ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $url);
	    	curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT . ' (curl)');
	    	@ curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		    curl_setopt($ch, CURLOPT_TIMEOUT, self::TIMEOUT);
		    
		    if ($method == self::POST) {
	    	    curl_setopt($ch, CURLOPT_POST, 1);
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
		    } 
		    elseif ($method != self::GET) {
		    	
		    	curl_setopt($ch, CURLOPT_POST, 1);
		    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
		        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);		    	
		    }
		    $data = curl_exec($ch);
		    $curl_error = curl_error($ch);
		    $curl_errno = curl_errno($ch); 
		    curl_close($ch);
	    	if ($data) return $data;
	    	
		    if ($curl_errno > 0) { 
        		throw new FriendsterAPIException(FriendsterAPIErrorCodes::CURL, $curl_error);
		    }
		    throw new FriendsterAPIException(FriendsterAPIErrorCodes::CURL);
	    } else { // non-curl version 
			if ($method != self::GET) {
				// POST, non-curl
				$context = array('http' =>
					array(	'method'	=>	$method,
							'header'	=>	"Content-type: application/x-www-form-urlencoded\r\n" .
											"User-Agent: " . self::USER_AGENT . " (non-curl)\r\n" .
											"Content-length: " .strlen($vars),
							'content'	=>	$vars));
				$cid = stream_context_create($context);
				if ($sock = fopen($url, 'r', false, $cid)) {
					$data = '';
					while(!feof($sock)) { $data .= fgets($sock, self::BLOCK_SIZE); }
					fclose($sock);

					return $data;
				} else {
					throw new FriendsterAPIException(FriendsterAPIErrorCodes::NOCURL_POST);
				}

			} else { // GET, non-curl
				if ($sock = fopen($url, 'r', false)) {
					while (!feof($sock)) { $data .= fgets($sock, self::BLOCK_SIZE);	}
					fclose($sock);
					return $data;
				} else {
					throw new FriendsterAPIException(FriendsterAPIErrorCodes::NOCURL_GET);
				}
			}
	    }
	}
}

?>
<?php
/*
 * FriendsterAPI PHP5 client
 * Copyright Friendster, Inc. 2002-2008
 * 
 */
 
class FriendsterAPI {

	const FRIENDSTER_API_VERSION = 1;

	// param names
	const PARAM_API_KEY = 'api_key';
	const PARAM_SIG = 'sig';
	const PARAM_SESSION_KEY = 'session_key';
	const PARAM_CALL_ID = 'nonce';
	const PARAM_AUTH_TOKEN = 'auth_token';
	const PARAM_UID = 'uid';
	const PARAM_CONTENT = 'content';
	const PARAM_INSTANCE_ID = 'instance_id';
	
	// api names
	const API_SESSION = 'session';
	const API_TOKEN = 'token';
	const API_USERS = 'user';
    const API_FANS = 'fans';
	const API_FRIENDS = 'friends';
	const API_AREFRIENDS = 'arefriends';
	const API_DEPTH = 'depth';
	const API_PHOTOS = 'photos';
	const API_PHOTO = 'photo';
	const API_SETPRIMARY = 'primaryphoto';
	const API_SHOUTOUT = 'shoutout';
	const API_WIDGET = 'widget';
    const API_EVENTS = 'events';
    const API_NOTIFICATION = 'notification';
    const API_APPFRIENDS = 'application/friends';
    const API_ALBUMS = 'albums';
    const API_ALBUM = 'album';
    const API_VIEWERS = 'viewers';

	
	// invalid call_id
	private $call_id = 0;
	
	private $path_info_prefix;
	private $server_base;
	private $api_key;
	private $secret;
	public  $session_key;
	private $last_call_id;
	private $base_www_url;
	
	public function __construct($api_key, $secret, $session_key = null,$base_api_url = 'http://api.friendster.com',
                                    $base_www_url = 'http://www.friendster.com') {
		// Friendster API server host
		$this->path_info_prefix =  '/v' . self::FRIENDSTER_API_VERSION . '/';
		$this->server_base = trim($base_api_url, '/') . $this->path_info_prefix;
		$this->api_key = $api_key;
		$this->secret = $secret;
		$this->session_key = $session_key;
		$this->base_www_url = $base_www_url;
	}
	
	public function setResponseType($response_type) {
		$this->$response_type = $response_type;
	}
	
	/* use this if you created a class object with $session_key == null */
	public function session($token = null) {
		
		if (is_null($token)) {
			// if no token provided, but we have session saved -> give it out
			if (!is_null($this->session_key)) return $this->session_key;
			throw new FriendsterAPIException(FriendsterAPIErrorCodes::AUTH_TOKEN);
		}
		$response = $this->doRequest(self::API_SESSION, null, array(self::PARAM_AUTH_TOKEN => $token), Request::POST);
		if (!array_key_exists(self::PARAM_SESSION_KEY, $response)) {
			throw new FriendsterAPIException(FriendsterAPIErrorCodes::SESSION_MISSING);
		}
		
		$this->session_key = $response[self::PARAM_SESSION_KEY]; 
		if  (strlen($this->session_key) == 0) {
			throw new FriendsterAPIException(FriendsterAPIErrorCodes::SESSION_EMPTY);
		}
				
		return $this->session_key;	
	}
	
	
	public function token() {
		$response = $this->doRequest(self::API_TOKEN, null, null, Request::POST);
		
		if (!array_key_exists(self::PARAM_AUTH_TOKEN, $response)) {
			throw new FriendsterAPIException(FriendsterAPIErrorCodes::AUTH_TOKEN_MISSING);
		}
		
		if (strlen($response[self::PARAM_AUTH_TOKEN]) == 0) {
			throw new FriendsterAPIException(FriendsterAPIErrorCodes::AUTH_TOKEN_EMPTY); 
		}
		return $response[self::PARAM_AUTH_TOKEN];
	}
	
	/* redirect to owners profile page */
	public function redirectToProfile($user_id, $params = null) {
		$encode_params = null;
		if ( isset($params) )
			$encode_params = $this->encodeParams($params);
		$url = $this->base_www_url . '/user.php?uid=' . $user_id;
		if ( $encode_params)
			$url .= '&'. implode('&', $encode_params);
		// Add update success status so that the page refreshes
		$url .= '&sc=3400';
		
        $this->redirect($url);
    }
    
	/* redirect to user login page */
	public function redirectToUserLogin($next_url) {
		$url = $this->base_www_url . '/login.php';
		if ( isset($next_url) )
			$url .= '?next='. $next_url;
    }
         
	public function redirect($url) {
		echo '<html>';
		echo '<head>';
		
		echo '<script type="text/javascript">';
		echo 'function redir () { window.top.location.href="' . $url . '";}' ;
		echo '</script>';
		
		echo '</head>';
		
		echo '<body onload="redir()">';
		echo '</body>';
		
		echo '</html>';
	}
	
        public function events($stream_id,$uids=null,$start_time=null,$end_time=null,$limit = null,
        $params = null, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
           	
		if ($params!=null && $callback!=null) {
			$params = array_merge($params,array('callback'=>$callback));
		} else {
			$params = array('callback'=>$callback);
		}
                 
                if ($uids!=null) {
                        $params = array_merge($params,array('uids'=>$uids));
                }
                if ($start_time!=null) {
                        $params = array_merge($params,array('start_time'=>$start_time));
                }
                if ($end_time!=null) {
                        $params = array_merge($params,array('end_time'=>$end_time));
                }
                if ($limit!=null) {
                        $params = array_merge($params,array('limit'=>$limit));
                }
                
         	return $this->doRequest(self::API_EVENTS, $stream_id, $params, Request::GET, $response_type);
	        
        } 
         
	public function users($uids, $params = null, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		
		if ($params!=null && $callback!=null) {
			$params = array_merge(params,array('callback'=>$callback));
		} else {
			$params = array('callback'=>$callback);
		}
		return $this->doRequest(self::API_USERS, $uids, $params, Request::GET, $response_type);
	}
	
         //alias to post_widget
	public function widget($content, $instance_id = null, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
                return $this->post_widget($content, $instance_id, $response_type,$callback);
        }
         
    public function post_widget($content, $instance_id = null, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array(self::PARAM_CONTENT => $content);
		if ($callback!=null) {
			$params = $params==null ? array('callback'=>$callback) : array_merge($params,array('callback'=>$callback));
		}
		if (!is_null($instance_id)) $params[self::PARAM_INSTANCE_ID] = $instance_id;
		return $this->doRequest(self::API_WIDGET, null, $params, Request::POST, $response_type);
	}
         
    public function get_widget($instance_id, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array();	
		if ($callback!=null) {
			$params = $params==null ? array('callback'=>$callback) : array_merge($params,array('callback'=>$callback));
		}
		if (!is_null($instance_id)) $params[self::PARAM_INSTANCE_ID] = $instance_id;
		return $this->doRequest(self::API_WIDGET, null, $params, Request::GET, $response_type);
	}        
         
	
	public function arefriends($uids, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_AREFRIENDS, $uids, $params, Request::GET, $response_type);	 
	}
	
	public function depth($uids, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_DEPTH, $uids, $params, Request::GET, $response_type);	
	}
	
	public function post_shoutout($content,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_SHOUTOUT, null, array('content'=>$content), Request::POST, $response_type);
	}
	
	public function get_shoutout($uids,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}	
		return $this->doRequest(self::API_SHOUTOUT, $uids, $params, Request::GET, $response_type);	
	}
	
	public function get_photo($uid,$pid,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		
		if (isset($uid))
			return $this->doRequest(self::API_PHOTO, $uid.'/'.$pid, array(), Request::GET, $response_type);
		else
			return $this->doRequest(self::API_PHOTO, $pid, $params, Request::GET, $response_type);		
	}
	
	public function put_photo($pid,$isprimary,$caption,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		$parameters = array();
		if (isset($isprimary)) {
			$parameters = array_merge(array('isprimary'=>$isprimary));
		}
		if (isset($caption)) {
			$parameters = array_merge(array('caption'=>$caption));
		}
		return $this->doRequest(self::API_PHOTO, $pid, $parameters, Request::PUT, $response_type);
	}
	
	public function delete_photo($pid,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_PHOTO, $pid, $params, Request::DELETE, $response_type);
	}
	
         /**
         * photos w/o albums
         */
	public function photos($uid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		return $this->get_photos($uid,null,$response_type,$callback);
    }
	
    /* (GET) photos with albums support */
    public function get_photos($uid,$aid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array('aid'=>$aid);
        if ($callback!=null) {
            $params =array_merge($params,array('callback'=>$callback));
        }
        return $this->doRequest(self::API_PHOTOS, $uid, $params, Request::GET, $response_type); 
    }
    
	public function friends($uid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_FRIENDS, $uid, $params, Request::GET, $response_type);
	}

	public function fans($uid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_FANS, $uid, $params, Request::GET, $response_type);
	}         
	
    /**
     * uploadphoto is for compatibility only, future applications should use post_photo instead
     */
	public function uploadphoto($uid, $caption, $filename, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		return $this->post_photo($uid, $caption, $filename, null, $response_type,$callback);
	}
	
    /**
     * Uploads a photo to the specified album
     */
    public function post_photo($uid, $caption, $filename, $aid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array('caption'=>$caption, 'filepath'=>$filename, 'aid'=>$aid);
        if ($callback!=null) {
            $params = array_merge($params,array('callback'=>$callback));
        }
        return $this->doRequest(self::API_PHOTOS, $uid, $params, Request::POST, $response_type);
    }   
    
    
    
	public function get_primaryphoto($uid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}		
		return $this->doRequest(self::API_SETPRIMARY,$uid, $params, Request::GET, $response_type);
	}
	
	/**
	 * Post a notification
	 */
	public function post_notification($uids,$subject,$label,$content,$url_fragment="",$notification_type=2, $response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {
	    $params = array( 'subject'=>$subject,'label'=>$label,'content'=>$content,'url_fragment'=>$url_fragment,'type'=>$notification_type);
	    if ($callback!=null) {
	    	$params = array_merge($params,array('callback'=>$callback));
	    }
	    return $this->doRequest(self::API_NOTIFICATION, $uids, $params, Request::POST, $response_type);
	}

	
	/* Get the list of friends who have the application currently installed */
	public function get_application_friends($response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {
		
		$params = array();
	    if ($callback!=null) {
	    	$params = array('callback'=>$callback);
	    }
	    return $this->doRequest(self::API_APPFRIENDS, null, $params, Request::GET, $response_type);			
	}

    /* Gets the list of albums for a specified user */
    public function get_albums($uid, $aids, $response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {
        $params = array('aids'=>$aids);
        if ($callback!=null) {
            $params[] = array('callback'=>$callback);
        }
        return $this->doRequest(self::API_ALBUMS, $uid, $params, Request::GET, $response_type);    	
    }

    /* Creates an album for the current user */
    public function post_album($name,$isprivate,$response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {
        $params = array('name'=>$name,'isprivate'=>$isprivate);
        if ($callback!=null) {
            $params = array_merge($params, array('callback'=>$callback));
        }
        return $this->doRequest(self::API_ALBUM, null, $params, Request::POST, $response_type);     
    }

    /**
     * Deletes an album
     */
    public function delete_album($aid, $preserve,$response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {
       $params = array('aid'=>$aid,'preserve'=>$preserve);
        if ($callback!=null) {
            $params = array_merge($params, array('callback'=>$callback));
        }
        return $this->doRequest(self::API_ALBUM, $aid, $params, Request::DELETE, $response_type);     
    }
    
    /*
     * Gets list of users who have viewed this users profile 
     * */
    public function viewers($uid, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = array();
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_VIEWERS, $uid, $params, Request::GET, $response_type);
	}
	
	/* (GET) comments */
    public function get_comments($start,$num, $response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
        $params = array('start'=>$start, 'num'=>$num);
        if ($callback!=null) {
            $params =array_merge($params,array('callback'=>$callback));
        }
        $args = "pending";
        return $this->doRequest(self::API_COMMENTS, $args, $params, Request::GET, $response_type); 
    }
    
	/**
	 * Post a comment
	 */
	public function post_comments($uid,$comment, $response_type = FriendsterAPIResponseType::SARRAY, $callback = null) {
	    $params = array( 'uid'=>$uid,'comment'=>$comment);
	    if ($callback!=null) {
	    	$params = array_merge($params,array('callback'=>$callback));
	    }
	    return $this->doRequest(self::API_COMMENTS, null, $params, Request::POST, $response_type);
	}


	public function put_comments($cids,$doaccept,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = $params = array( 'cids'=>$cids,'doaccept'=>$doaccept);
		
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_COMMENTS, null, $params, Request::PUT, $response_type);
	}

	public function get_search($name,$country,$location, $proximity, $page,$response_type = FriendsterAPIResponseType::SARRAY,$callback = null) {
		$params = $params = array( 'name'=>$name,'country'=>$country,'location'=>$location,'proximity'=>$proximity,'page'=>$page);
		
		if ($callback!=null) {
			$params =array('callback'=>$callback);
		}
		return $this->doRequest(self::API_SEARCH, null, $params, Request::GET, $response_type);
	}

	/* encodeParams
	 */
	 private function encodeParams(&$params) {
	 	$encoded_params = '';		
		foreach($params as $key => $value) {
			if (is_array($value)) {
				$value = implode(",", $value);
				$params[$key] = $value;
			}
			$encoded_params[] = $key . '=' . urlencode($value);
		}
		return $encoded_params;	 	
	 }
	
	/* doRequest(
	 * @method	:	'user', - method name
	 * @args	:	array(57519, 1234), - list of args OR single arg
	 * @params	:	array('status' => 'maried'), - get/post params
	 * @type	:	'POST' || 'GET'
	 */
	public function doRequest($method, $args, $params, $type = Request::GET,$response_type = FriendsterAPIResponseType::JSON) {
		
		// http://api.friendster.com/getUser
		$url = $this->server_base . $method;
		$argstr = "";
		if (!is_null($args)) {
			// http://api.friendster.com/getUser/
			$argstr = "/";
			if (is_array($args)) {
				if (count($args) == 0) {
					throw new FriendsterAPIException(FriendsterAPIErrorCodes::ARGS);
				}
				// http://api.friendster.com/getUser/57519,1234
				$argstr .= implode(",", $args);
			} else {
				// http://api.friendster.com/getUser/57519
				$argstr .= $args;
			}
			$url .= $argstr;
		}

		//Set the requested format
		if ($response_type == FriendsterAPIResponseType::XML) {
			$params['format'] = 'xml';
		} else
		if ($response_type == FriendsterAPIResponseType::JSON) {
			$params['format'] = 'json';
		}
		
		$params[self::PARAM_API_KEY] = $this->api_key;
		if (!is_null($this->session_key)) {
			$params[self::PARAM_SESSION_KEY] = $this->session_key;
			$params[self::PARAM_CALL_ID] = $this->getCallId(true);
		} 
		
		$filename = null;
		foreach($params as $key=>$value) {
			if ($key=='filepath') {
				$filename = '@'.$value;
				unset($params['filepath']);
			}
		}
		
		$encoded_params = $this->encodeParams($params);		
		$pathinfo = $this->path_info_prefix . $method . $argstr;
		$encoded_params[] = self::PARAM_SIG . '=' . $this->generateSignature($pathinfo, $params, $this->secret);
		
		//$url .= '?' . implode("&", $encoded_params);
		if (strlen($url) == 0) {
			throw new FriendsterAPIException(FriendsterAPIErrorCodes::URI);
		}
				
		$param_string = implode('&', $encoded_params);
		//Uncomment to enable server side debugging - you may have to disable signature
		//$param_string .= '&DBGSESSID=1@localhost:10001';
		if ($GLOBALS['friendster_api']['debug']) { echo "URL:\t$url\nMETHOD:\t$type\nPARAMS:\t$param_string\n\n"; }
		
		$req = new Request();
		
		//Handle file upload scenarios
		if ($filename!=null) {
			$md5sig = $this->generateSignature($pathinfo, $params, $this->secret);
			$params['filepath'] = $filename;
			$params['sig'] = $md5sig;
			$response =  $req->execute($url, $params, $type);
		} else {
			$response =  $req->execute($url, $param_string, $type);
		}
 
		$req = null;
		if ($GLOBALS['friendster_api']['debug']) { echo "RESPONSE:\n["; print_r($response); echo "]\n\n"; }

		$result = json_decode( $response, TRUE  ); 

		/*
				if ( $response_type != FriendsterAPIResponseType::SARRAY) {
					$result = $response;
				} else {
		         $xml_obj = @simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
		 			if ($xml_obj instanceof SimpleXMLElement) {
						$result = self::sxml_to_array($xml_obj);
					} else {
						throw new FriendsterAPIException(FriendsterAPIErrorCodes::XML);
					}
				}
		*/

		return $result;			
	}
	
	protected function generateSignature($pathinfo, $params, $secret) {
		$str = $pathinfo;
		ksort($params);
		foreach($params as $key => $value) {
			if ($key != self::PARAM_SIG) {
    			$str .= "$key=$value";
    		}
		}
		return md5($str . $secret);
	} // generateSignature

	/**
	 * Validate callback URL signature
	 */
	public static function validateSignature($secret) {
		$uri = $_SERVER['REQUEST_URI'];
		$uria = split('\?', $uri, 2);
		$pathinfo = $uria[0];
		if ( isset($uria[1]) && !empty($uria[1]) ) {
			$querystring = $uria[1];

			$params = array();
			$pairs = split('&', $querystring);
			for ( $i=0; $i < count($pairs); ++$i ) {
				$keyval = split('=', $pairs[$i]);
				$params[$keyval[0]] = isset($keyval[1]) ? $keyval[1]: '';
			}
		} else {
			$params = $_POST;
		}
		if ( count($params) == 0 )
			throw new FriendsterAPIException(FriendsterAPIErrorCodes::SIGNATURE);

		ksort($params);
		$str = '';
		$sig = '';
		foreach($params as $key => $value) {
			if ($key != self::PARAM_SIG) {
    			$str .= "$key=$value";
    		}
			else {
				$sig = $value;
			}
		}
      if ( md5($pathinfo . $str . $secret) != $sig ) {
         $pathinfo = rtrim($pathinfo, '/');
         if ( md5($pathinfo . $str . $secret) != $sig )
            throw new FriendsterAPIException(FriendsterAPIErrorCodes::SIGNATURE);
      }
	}// validateSignature

	public static function sxml_to_array($sxml) {
		$result = array();
		if ($sxml) {
			foreach($sxml as $key => $value) {
				if ($sxml['list']) {
					$result[] = self::sxml_to_array($value);
				} else {
					$result[$key] = self::sxml_to_array($value);
				}
			}
      } 
		
		return sizeof($result) > 0 ? $result : (string)$sxml;
	} // sxml_to_array
	
	protected function getCallId($generate = true) {
		if ($generate) {
			$timestamp = microtime(true);
			if ($timestamp <= $this->last_call_id ) {
				$timestamp = $this->last_call_id + 0.001;
			}
			$this->last_call_id = $timestamp;
		}
		return $this->last_call_id;
	} // getCallId
}
