<?php

# Version Yahc/0.4.2
#
#  fixed : ereg passing by reference
#
# Version Yahc/0.4.1
#
#  fixed : header extraction returns *value* not the *name*
#  fixed : correct user agent string
#
# Version Yahc/0.4
#
#  fixed : cookie sending blank line bug
#  added : connection: close header added
#  change: user agent now Yahc, instead of HTTP_Conn
#  change: opening connection sends first request line
#  change: send_request now uses send_request_line
#
# Version Yahc/0.3
#
#  change: request_etc is now request_post
#  todo  : POST now RFC2388, RFC???? compliant
#  todo  : user authentication RFC2617 compliant
#
# Version Yahc/0.2 - second edition, multiple quick hacks
#
#  added: preliminary user/pass support
#  added: preliminary POST support
#  added: multiple methods
#  added: changeable protocols
#  added: cookie support
#  added: header retrieval
#  fixed: HTTP/html splitting now works
#  added: response_HTML_size
#  added: response_HTTP_code
#  added: response_HTTP_protocol
#  added: response_HTTP_note
#  added: response_HTTP_cookie
#  added: request_*
#  fixed: UA string Yahc, not HTTP_Con
#
# Version Yahc/0.1 - very first edition
#

class Yahc {

 var $s_Complete;
 var $s_Host;
 var $s_Port;
 var $s_URI;

 var $f_Handle;

 var $request_protocol;        # HTTP/1.1
 var $request_method;          # GET
 var $request_cookie;          # Item=Val;Item2=Val2
 var $request_authentic_user;  # Mike
 var $request_authentic_pass;  # drowssap
 var $request_post;            # form POST data
 var $request_connection;      # should we close?
 var $redirect;                # should we redirect internally?
 var $response;

 var $response_HTML;
 var $response_HTML_size;      # n bytes
 var $response_HTTP;
 var $response_HTTP_code;      # 200 / 400 / 500 / ...
 var $response_HTTP_protocol;  # HTTP/1.1
 var $response_HTTP_note;      # OK
 var $response_HTTP_cookie;    # Item=Val; expires=time

 var $response_HEADER;         # HTTP-Header added by Sebastian Goebel, devel@yellowsunshine.de
 var $response_all_HTTP_cookie;# array of cookies set during redirection;
                               # usable in this way:
                               # if (is_array($instance_name->response_all_HTTP_cookie))
                               #   foreach ($instance_name->response_all_HTTP_cookie as $lost=>$cookie)
                               #     {
                               #     $cookie = trim ($cookie);
                               #     if (substr($cookie, -1)<>";")   # some software creates incomplete cookie-statements
                               #       $cookie .= ";";
                               #     if (!eregi("path=", $cookie))
                               #       $cookie .= " path=/;";
                               #     echo "<meta http-equiv=\"set-cookie\" content=\"$cookie\">";
                               #     }

 var $ua_String;

 ## --------------------------------------------------------

 function Yahc($addr = FALSE, $ua = FALSE) {
  if ( $addr )
   $this->set_address($addr);

  $this->request_protocol = 'HTTP/1.1';
  $this->request_method = 'GET';

  if ( $ua )
   $this->ua_String = $ua;
  else
   $this->ua_String = "Yahc/0.4.2 PHP/". phpversion();

  $this->request_post = '';
  $this->request_connection = 'close';

 }

 ## --------------------------------------------------------
  # ArrSerialize added by Sebastian Goebel
 function ArrSerialize ($arr, $k)
  {
  foreach ($arr AS $key=>$val)
    {
    if (is_array ($val))
      $str .= $this->ArrSerialize ($val, $k."[".$key."]");
    else $str .= urlencode ($k."[".$key."]")."=".urlencode ($val)."&";
    }
  return $str;
  }

 ## --------------------------------------------------------

 function make_request_post( $post_data ) {
  for ( reset($post_data) ; list($key, $value) = each($post_data) ; )
    {
    if (is_array ($value))  # these lines added by Sebastian Goebel
      $this->request_post .= $this->ArrSerialize ($value, $key);
    else
      $this->request_post .= urlencode($key) .'='. urlencode($value) .'&';
    }
 }

 ## --------------------------------------------------------
  # function make_request_cookie added by Sebastian Goebel
  # use an 2-dimensional Array (VAR1=>VAL1, ..., VARn=>VALn)

 function make_request_cookie( $cookie_data ) {
  for ( reset($cookie_data) ; list($key, $value) = each($cookie_data) ; )
   $this->request_cookie .= urlencode($key) .'='. urlencode($value) .';';
 }

 ## --------------------------------------------------------

 function set_address($address) {
  $this->s_Complete = parse_url($address);
  $this->s_Host   = $this->s_Complete["host"];
  $this->s_Port   = $this->s_Complete["port"] ? $this->s_Complete["port"] : 80;
  $this->s_URI    = $this->s_Complete["path"]
                  .($this->s_Complete["query"] ? '?'.$this->s_Complete["query"] : '');
  # line added by Sebastian Goebel
  $this->s_Path = dirname($this->s_Complete["path"]);

  $this->request_authentic_user = $this->s_Complete["user"];
  $this->request_authentic_pass = $this->s_Complete["pass"];

  return ($this->s_Host && $this->s_URI);
 }

 ## --------------------------------------------------------

 function connect() {
  if ( $this->f_Handle = fsockopen($this->s_Host, $this->s_Port, $this->errno, $this->errstr, 10) )
   return $this->send_request_line("$this->request_method $this->s_URI $this->request_protocol");
  else
   return FALSE;
 }

 ## --------------------------------------------------------

 function disconnect() {
  if ($this->f_Handle)
   return fclose($this->f_Handle);
  else
   return FALSE; # no connection open
 }

 ## --------------------------------------------------------

 function parse_headers($headername) {
  # here's a potential problem:
  # a host may return more than one header of a certain
  # type. unfortunately, Yahc only returns the
  # first one. So much for multiple cookies.

  # the RFCs specify headers to be separated by a CRLF pair
  # some servers may not be compliant, in which case this
  # will not work very well

  $each_header = split("[\r]?\n", $this->response_HTTP);

  # comparison is case insensitive.
  # each header MUST be followed by a colon and a space, or
  # this will break - very fussy
  #
  # it has to be, or a search for Content will return
  # the first of Content-Length, Content-Type, etc
  for ( ; list(,$this_header) = each($each_header) ; )
   if ( eregi("^$headername: ", $this_header) )
    return substr( $this_header, (strlen($headername) + 2) );
 }

 ## --------------------------------------------------------
  # function maybe_redirect added by Sebastian Goebel

 function maybe_redirect () {
  //echo "<p>HEADER: ".$this->response_HEADER."</p>";
  if ($this->response_location) {

   //echo "<p>LOCATION: ".$this->response_location;
   //$this->request_referer = $this->s_URI; //?????????????

   $this->request_method = "GET";
   if (!eregi ("http://", $this->response_location))    # no complete URI
     {
     if (substr($this->response_location, 0, 1)=="/")   # absulute Path
       $this->response_location = "http://$this->s_Host:$this->s_Port$this->response_location";
     else                                               # relative Path, use old one
       $this->response_location = "http://$this->s_Host:$this->s_Port$this->s_Path/$this->response_location";
     }
   $this->set_address ($this->response_location);

   //echo  "<p>REDIRECT to: $this->response_location</p>";

   unset ($this->response);
   unset ($this->request_post);

   if ($this->connect()) {
    $this->send_request();
    $this->get_response();
   }
   else die ("<p>INTERNAL REDIRECTION FAILS</p>");
  }
 }

 ## --------------------------------------------------------

 function send_request_line($request) {

  #echo "$request<br>";

  if ($this->f_Handle)
   return fputs($this->f_Handle, $request."\r\n");
  else
   return FALSE; # no connection open
 }

 ## --------------------------------------------------------

 function send_request() {
  if ($this->f_Handle)
   return
     $this->send_request_line("User-Agent: $this->ua_String") &
     $this->send_request_line("Host: $this->s_Host") &
     ( $this->request_connection ? $this->send_request_line("Connection: $this->request_connection") : TRUE ) &
     ( $this->request_cookie ? $this->send_request_line("Cookie: ".substr ($this->request_cookie, 0, strlen ($this->request_cookie)-1)) : TRUE ) &
     # following line added by Sebastian Goebel
     ( $this->request_referer ? $this->send_request_line("Referer: $this->request_referer") : TRUE ) &
     # following line changed by Sebastian Goebel
     ( $this->request_post ? $this->send_request_line("Content-type: application/x-www-form-urlencoded\r\nContent-length: ".(strlen ($this->request_post)-1)."\r\n\r\n".substr ($this->request_post, 0, strlen ($this->request_post)-1)) : TRUE ) &
     $this->send_request_line('');

   # HTTP authentication not supported. it will use:
   # $this->request_authentic_user;
   # $this->request_authentic_pass;

  else
   return FALSE; # no connection open
 }

 ## --------------------------------------------------------

 function get_response_line() {
  if ($this->f_Handle)
   return fgets($this->f_Handle, 512);
  else
   return FALSE;
 }


 ## --------------------------------------------------------

 function get_response() {
  while ( $newtext = fgets($this->f_Handle, 128) )
   $this->response .= $newtext;

  $this->disconnect();

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  # split the response into HTTP and HTML
  # this is delimited by the first \r\n\r\n

  # added +4 in strpos() by Sebastian Goebel
  $this->response_HTML = substr ($this->response, strpos($this->response, "\r\n\r\n")+4);
  # response_HEADER addet by Sebastian Goebel
  $this->response_HEADER = substr ($this->response, 0 , strpos($this->response, "\r\n\r\n"));
  $this->response_HTML_size = strlen ($this->response_HTML);
  $this->response_HTTP = substr ($this->response, 0, strlen($this->response)-$this->response_HTML_size );

  # - - - - - - - - - - - - - - - - - - - - - - - - - - - -
  # understand what happened in our request

  eregi("^[^\r^\n]*", $this->response_HTTP, $piece);

  # pieces[0] now contains: 'HTTP/1.1 200 OK'
  # lets break this up even further

  $pieces = explode(' ', $piece[0]);

  $this->response_HTTP_protocol = $pieces[0];
  $this->response_HTTP_code = $pieces[1];

  # the note will potentially be more than one word
  for ( $loop_i = 2 ; $loop_i < count($pieces) ; $loop_i++ )
   $this->response_HTTP_note .= $pieces[$loop_i] . ' ';

  # unnecessary, but hey
  unset($pieces);

  # and the lowly cookie

  $this->response_HTTP_cookie = $this->parse_headers('Set-Cookie');

  # following 5 lines added by Sebastian Goebel
  $this->response_location = $this->parse_headers ("Location");

  if ($this->response_HTTP_cookie)
    $this->response_all_HTTP_cookie[] .= $this->response_HTTP_cookie;

  if ($this->redirect)
    $this->maybe_redirect ();
 }

 ## --------------------------------------------------------

 function state_debug() {
  if ($this->f_Handle)
   print "Connection: Open ($this->f_Handle)\n";
  else
   print "Connection: Closed\n";

  print "Error Number: $this->errno\n";
  print "Error Text: $this->errstr\n";
 }

 ## --------------------------------------------------------

 function dialogue_debug() {
  print "<hr><pre>$this->request</pre><hr><pre>$this->response_HTTP</pre><hr>"
    . htmlspecialchars($this->response_HTML)."<hr>";
 }

}

?>