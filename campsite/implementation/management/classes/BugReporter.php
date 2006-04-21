<?php

/**
 * @package Campsite
 */

global $g_bugReporterDefaultServer;
$g_bugReporterDefaultServer = "http://code.campware.org/projects/campsite/autotrac";
//$g_bugReporterDefaultServer = "http://localhost/trac/autotracj";
//$g_bugReporterDefaultServer = "http://test.n-space.org/mocktrac/echo.php";

class BugReporter
{
    /**
     * Constructor
     *
     * An object for sending captured error info to an HTTP server.
     *
     * An object for sending captured error info to an HTTP server.  In
     * particular this is made to work with the Trac plugin Autotrac, though
     * it would be quite simple to make another HTTP server to work with it.
     *
     * A simple way to use this object is to create an error handler to the
     * following function report_bug():
     *
     *     function report_bug ($p_number, $p_string, $p_file, $p_line)
     *     {
     *         $reporter = new BugReporter ($p_number, $p_string, $p_file, $p_line);
     *         $reporter->setServer ("http://myserver.com/mydirectory")
     *         $reporter->sendToServer();
     *     }
     *
     * The errors are always sent to the server URL plus the extension
     * "/report".  So the above example would POST the error variables to
     * http://myserver.com/mydirectory/newreport .  The error variables
     * POSTed are:
     *
     *     - f_backtrace
     *     - f_id
     *     - f_software
     *     - f_str
     *     - f_num
     *     - f_file
     *     - f_line
     *     - f_backtrace
     *     - f_description
     *     - f_email
     *
     *
     * @param int $p_number The PHP error number.
     * @param string $p_string The error message.
     * @param string $p_file The file which encountered the error.
     * @praam int $p_line The line number of the file which encountered the error.
     * @param string $p_software The name of the software that encountered an error.
     * @param int $p_version The version of the software that encountered an error.
     * @param string $p_time The date and time.  If left blank, it is the current date and time.
     * @param $p_backtrace The stack trace.  This can be an array or string.
     */
    function BugReporter ($p_number, $p_string, $p_file, $p_line,
                          $p_software, $p_version, $p_time = "", $p_backtrace = "")
    {
        require_once "HTTP/Client.php";

        global $g_bugReporterDefaultServer;

        $this->invalidParam = "Invalid parameter value.";

        if (!is_string($p_software)) {
            trigger_error ($this->invalid_param);
        }

        if ($p_time == "") $p_time = date("r");

        if ($p_backtrace == "" || $p_backtrace == array()) {
            $backtrace = debug_backtrace();

            // --- We don't need the first 2 lines from the debug_backtrace() ---
            $newBacktrace = array ();
            for ($aa=2; $aa< sizeof($backtrace); $aa++) {
                $newBacktrace[] = $backtrace[$aa];
            }
            if (sizeof($newBacktrace) > 0) $backtrace = $newBacktrace;
        } else {
            $backtrace = $p_backtrace;
        }

        $this->m_req =& new HTTP_Request("");

        $this->m_software = $p_software;
        $this->m_version =  $p_version;
        $this->m_num = (int) $p_number;
        $this->m_str = $p_string;
        $this->m_file = $p_file;
        $this->m_line = (int) $p_line;
        $this->m_backtrace = $this->__convertBacktraceArrayToString ($backtrace);
        $this->m_time = $p_time;


        $this->setServer ($g_bugReporterDefaultServer);
        $this->setPingStatus(true);
    }

    /**
     * This changes the developers' default server.
     *
     * @param string $p_server  The URL of the new server.
     */
    function setServer($p_server)
    {
        $this->__server = $p_server;
        $this->__ping = "$p_server/ping";
        $this->m_newReport = "$p_server/newreport";
    }

    /**
     * Returns the current developers' server.
     *
     * @return string
     *          The current server's URL.
     */
    function getServer()
    {
        return $this->__server;
    }

    /**
     * Confirms that the server is online.
     *
     * @return boolean
     *          True if the server is up, else false.
     */
    function pingServer()
    {

        // --- if ping status is false, return true without making the call ---
        if (isset ($this->m_disablePing) && ($this->m_disablePing == true)) {
            return true;
        }
        $client = new HTTP_Client();
        $code = $client->get ($this->__ping);

        $response = $client->currentResponse();

        $this->__responseHeader = $response['headers'];
        $this->__responseBody = $response['body'];
        $this->__responseCode = $code;

        if (preg_match ("/pong/", $this->__responseBody) && ($code == 200))
            return true;
        else return false;

    }

    /**
     * When pinging status is is set to false, pingServer() returns
     * true without actually pinging the server.
     *
     */
    function setPingStatus ($p_pingingStatus)
    {
        if (!is_bool($p_pingingStatus)) trigger_error ($this->invalidParam);
        else $this->m_disablePing = !($p_pingingStatus);
    }

    /*
     * @return The pinging status
     */
    function getPingStatus()
    {
        return !$this->m_disablePing;
    }

    /**
     * Send the error details to the server via HTTP.
     */
    function sendToServer()
    {
        $client = new HTTP_Client();
        $code = $client->post
            ($this->m_newReport, array('f_software' => $this->m_software,
                                       'f_version' => $this->m_version,
                                       'f_num' => $this->m_num,
                                       'f_str' => $this->m_str,
                                       'f_line' => $this->m_line,
                                       'f_id' => $this->getId(),
                                       'f_backtrace' => $this->getBacktraceString(),
                                       'f_time' => $this->getTime(),
                                       'f_description' => $this->getDescription(),
                                       'f_email' => $this->getEmail()
                                    ));

        $response = $client->currentResponse();
        $this->__responseHeader = $response['headers'];
        $this->__responseBody = $response['body'];
        $this->__responseCode = $code;

        if ($code != 200) return false;

        // --- Did we get an "accepted"?
        elseif (preg_match ("/\baccepted\b/", $this->__responseBody)) return true;
        else return false;

    }

    /**
     * Return the name of the error-file, not including the path.
     *
     * @return string
     *          The name of the file, not including the path.
     */
    function getFileWithoutPath()
    {
        if (preg_match ("/\/$/", $this->m_file))
            trigger_error ($this->invalidParam);
        return preg_replace ("/.*\/([^\/]*)/", "$1", $this->m_file);
    }

    /**
     * @return string the file's ID-code.
     */
    function getId()
    {
        return "$this->m_num:$this->m_software:$this->m_version:" .
            $this->getFileWithoutPath() . ":$this->m_line";
    }

    /**
     * @return string The traceback
     */

    function getBacktraceString()
    {
        return $this->m_backtrace;
    }

    /**
     * @return string The name of the software the error occurred in.
     */

    function getSoftware()
    {
        return $this->m_software;
    }

    /**
     * @return string The version of the software the error occurred in
     */

    function getVersion()
    {
        return $this->m_version;
    }

    /**
     * @return int The error number
     */

    function getErrorNum()
    {
        return $this->m_num;
    }

    /**
     * @return The error-string
     */

    function getStr()
    {
        return $this->m_str;
    }

    /**
     * @return string The time at which the crash occurred
     */
    function getTime()
    {
        return $this->m_time;
    }

    /**
     * @return string The name of the file the crash occurred in.
     */
    function getFile()
    {
        return $this->m_file;
    }

    /**
     * @return int The line number the error occurred in.
     */
    function getLine ()
    {
        return $this->m_line;
    }

    /**
     * @return int Get the email address of the user.
     */
    function getEmail()
    {
        if (!isset($this->m_email)) $this->m_email = "";
        return $this->m_email;
    }

    /**
     * @return int Get the user's description of the error
     */
    function getDescription()
    {
        if (!isset($this->m_description)) $this->m_description = "";
        return $this->m_description;
    }

    /**
     * @param string The traceback string
     */
    function setBacktraceString ($p_backtrace)
    {
        $this->m_backtrace = $p_backtrace;
    }

    /**
     * @param string Set the name of the software in which the error occurred.
     */
    function setSoftware ($p_software)
    {
        $this->m_software = $p_software;
    }

    /**
     * @param string Set the version number of the software in which the error occurred.
     */
    function setVersion ($p_version)
    {
        $this->m_version = $p_version;
    }

    /**
     * @param string Set the number of the error which just occurred.
     */
    function setErrorNum ($p_errorNum)
    {
        $this->m_num = $p_errorNum;
    }

    /**
     * @param string Set the message for the error which just occured
     */
    function setStr ($p_str)
    {
        $this->m_str = $p_str;
    }

    /**
     * @param string Set the time when the error occurred
     */
    function setTime ($p_time)
    {
        $this->m_time = $p_time;
    }

    /**
     * @param string Set the filename of the error which just occurred.
     */
    function setFile ($p_file)
    {
        $this->m_file = $p_file;
    }

    /**
     * @param string Set the line number where the error occurred.
     */
    function setLine ($p_line)
    {
        $this->m_line = $p_line;
    }

    /**
     * @param string Set the user's email.
     */
    function setEmail ($p_email)
    {
        $this->m_email = $p_email;
    }

    /**
     * @param string Set the user's description of the error
     */
    function setDescription ($p_description)
    {
        $this->m_description = $p_description;
    }

    /**
     * Convert the backtrace array into a backtrace string.
     *
     * @param $p_backtrace array The array to be converted.
     * @return string The array as a string.
     */
    function __convertBacktraceArrayToString($p_backtrace)
    {
        if (is_string($p_backtrace)){
            return $p_backtrace;
        } else if (is_array ($p_backtrace)){
            $backtrace = "";

            for ($aa=0; $aa<sizeof($p_backtrace); $aa++) {
                $backtraceCurrentLine = "";

                // --- Get the Current the Backtrace line $aa (cbt) ---
                $cbt = $p_backtrace[$aa];

                $function = isset ($cbt['function']) ? $cbt ['function'] : "";
                $file = isset ($cbt['file']) ? $cbt ['file'] : "";
                $line = isset ($cbt['line']) ? $cbt ['line'] : "";

                $backtraceCurrentLine .= $function . "() called at [" . $file . ":" . $line . "]\n";

                if (isset($cbt['class'])) {
                    $backtraceCurrentLine = $cbt['class'] . "::" . $backtraceCurrentLine;
                }
                $backtrace .= $backtraceCurrentLine;
            }

            return $backtrace;
        } else trigger_error ($this->invalidParam);
    }
}


?>