<?php
/**
 * @package Campsite
 */

require_once('DatabaseObject.php');

/**
 * @package Campsite
 */
class Log extends DatabaseObject {
	var $m_keyColumnNames = array('time_created', 'fk_event_id', 'text');
	var $m_keyIsAutoIncrement = false;
	var $m_dbTableName = 'Log';
	var $m_columnNames = array(
		'time_created',
		'fk_event_id',
		'fk_user_id',
		'text',
		'user_ip');


	/**
	 * This is a static function.
	 * Write a message to the log table.
	 *
	 * @param string $p_text
	 * @param int $p_eventId
	 * @param integer $p_userId
	 *
	 * @return void
	 */
	public static function Message($p_text, $p_userId = null, $p_eventId = 0)
	{
		global $g_ado_db;
        global $Campsite;

        if (isset($Campsite) && is_array($Campsite) && array_key_exists('OMIT_LOGGING', $Campsite) && $Campsite['OMIT_LOGGING']) {
            return;
        }

        if (empty($p_userId)) {
			$p_userId = 0;

            $auth = \Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $p_userId = $auth->getIdentity();
            }
		}

		if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$userIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			$userIP = $_SERVER['REMOTE_ADDR'];
		} else {
			$userIP = '';
		}

        $ip_ary = explode('/', (string) $userIP);
        $userIP = substr($ip_ary[0], 0, 39); // IPv6

        $queryStr = "INSERT INTO Log (time_created, fk_event_id, fk_user_id, text, user_ip) VALUES
                    (NOW(), {$p_eventId}, {$p_userId}, " . $g_ado_db->escape($p_text) . ", " . $g_ado_db->escape($userIP) . ")";
		$g_ado_db->Execute($queryStr);
	} // fn Message


    /**
     * Log article related event.
     *
     * @param Article $p_article
     * @param string $p_text
     * @param int $p_userId
     * @param int $p_eventId
     * @param bool $p_short
     *
     * @return void
     */
    public static function ArticleMessage(Article $p_article, $p_text, $p_userId = NULL, $p_eventId = 0, $p_short = FALSE)
    {
        ob_start();

        $translator = \Zend_Registry::get('container')->getService('translator');
        echo $translator->trans('Article'), ': ', $p_article->getTitle();

        if (!$p_short) { // add publication, issue, section
            echo ' (';
            echo $translator->trans('Publication'), ': ', $p_article->getPublicationId();
            echo ', ';
            echo $translator->trans('Issue'), ': ', $p_article->getIssueNumber();
            echo ', ';
            echo $translator->trans('Section'), ': ', $p_article->getSectionNumber();
            echo ")\n";
        }

        // generate url
        $url = ShortURL::GetURL($p_article->getPublicationId(),
            $p_article->getLanguageId(),
            $p_article->getIssueNumber(),
            $p_article->getSectionNumber(),
            $p_article->getArticleNumber());
        if (strpos($url, 'http') !== FALSE) { // no url for deleted
            echo $translator->trans('Article URL', array(), 'api'), ': ', $url, "\n";
        }

        echo $translator->trans('Article Number', array(), 'api'), ': ', $p_article->getArticleNumber(), "\n";
        echo $translator->trans('Language'), ': ', $p_article->getLanguageName(), "\n";

        echo "\n";
        echo $translator->trans('Action') . ': ', $p_text;

        $message = ob_get_clean();
        self::Message($message, $p_userId, $p_eventId);
    }


	/**
	 * Get the time the log message was created.
	 * @return string
	 */
	public function getTimeStamp()
	{
		return $this->m_data['time_created'];
	} // fn getTimeStamp


	/**
	 * Return the log message.
	 * @return string
	 */
	public function getText()
	{
		return $this->m_data['text'];
	} // fn getText


	/**
	 * Get the event ID which cooresponds to an entry in the "Events" table.
	 * @return int
	 */
	public function getEventId()
	{
		return $this->m_data['fk_event_id'];
	} // fn getEventId


	public function getClientIP()
	{
	    return $this->m_data['user_ip_addr'];
	}


	/**
	 * Return the number of log lines.
	 * @param int $p_eventId
	 * @return int
	 */
	public static function GetNumLogs($p_eventId = null)
	{
		global $g_ado_db;
		$queryStr = 'SELECT COUNT(*) FROM Log';
		if (!is_null($p_eventId)) {
			$queryStr .= " WHERE fk_event_id=$p_eventId";
		}
		$total = $g_ado_db->GetOne($queryStr);
		return $total;
	} // fn GetNumLogs


	/**
	 * Get the logs.
	 *
	 * @param int $p_eventId
	 * @param array $p_sqlOptions
	 *
	 * @return array
	 */
	public static function GetLogs($p_eventId = null, $p_sqlOptions = null)
	{
		if (is_null($p_sqlOptions) || !isset($p_sqlOptions['ORDER BY'])) {
			$p_sqlOptions['ORDER BY'] = array('time_created' => 'DESC');
		}
		$tmpLog = new Log();
		$columns = $tmpLog->getColumnNames(true);
		$queryStr = "SELECT ".implode(", ", $columns)
		    .", INET_NTOA(Log.user_ip) AS user_ip_addr"
		    .", liveuser_users.Name as full_name"
		    .", liveuser_users.UName as user_name"
		    ." FROM Log"
		    ." LEFT JOIN liveuser_users"
		    ." ON Log.fk_user_id = liveuser_users.Id";
		if (!is_null($p_eventId)) {
			$queryStr .= " WHERE Log.fk_event_id=$p_eventId";
		}
		$queryStr = DatabaseObject::ProcessOptions($queryStr, $p_sqlOptions);
		$logLines = DbObjectArray::Create('Log', $queryStr);
		return $logLines;
	} // fn GetLogs

} // class Log

?>
