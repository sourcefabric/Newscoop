<?

require_once($Campsite['HTML_DIR']."/db_connect.php");
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/lib_campsite.php");
require_once($Campsite['HTML_DIR']."/$ADMIN_DIR/languages.php");

function publish_articles($datetime)
{
	$sql = "select * from ArticlePublish where"
	         . " YEAR(PublishTime) = YEAR('" . $datetime . "')"
	         . " and MONTH(PublishTime) = MONTH('" . $datetime . "')"
	         . " and DAYOFMONTH(PublishTime) = DAYOFMONTH('" . $datetime . "')"
	         . " and HOUR(PublishTime) = HOUR('" . $datetime . "')"
	         . " and MINUTE(PublishTime) = MINUTE('" . $datetime . "')";
	if (!$res = mysql_query($sql))
		return;
	if (mysql_num_rows($res) <= 0)
		return;

	while ($row = mysql_fetch_assoc($res)) {
		$article = $row['NrArticle'];
		$language = $row['IdLanguage'];
		$publish = $row['Publish'];
		$front_page = $row['FrontPage'];
		$section_page = $row['SectionPage'];

		if ($publish == 'P' || $publish == 'U')
			$set[] = "Published = '" . ($publish == 'P' ? 'Y' : 'S') . "'";
		if ($front_page == 'S' || $front_page == 'R')
			$set[] = "OnFrontPage = '" . ($front_page == 'S' ? 'Y' : 'N') . "'";
		if ($section_page == 'S' || $section_page == 'R')
			$set[] = "OnSection = '" . ($section_page == 'S' ? 'Y' : 'N') . "'";
		$set_str = implode(', ', $set);
		$sql = "update Articles set $set_str where Number = " . $article
		     . " and IdLanguage = " . $language;
		mysql_query($sql);
	}
}

function publish_issues($datetime)
{
	$sql = "select * from IssuePublish where"
	         . " YEAR(PublishTime) = YEAR('" . $datetime . "')"
	         . " and MONTH(PublishTime) = MONTH('" . $datetime . "')"
	         . " and DAYOFMONTH(PublishTime) = DAYOFMONTH('" . $datetime . "')"
	         . " and HOUR(PublishTime) = HOUR('" . $datetime . "')"
	         . " and MINUTE(PublishTime) = MINUTE('" . $datetime . "')";
	if (!$res = mysql_query($sql))
		return;
	if (mysql_num_rows($res) <= 0)
		return;

	while ($row = mysql_fetch_assoc($res)) {
		$pub = $row['IdPublication'];
		$issue = $row['NrIssue'];
		$language = $row['IdLanguage'];
		$action = $row['Action'];
		$publish_articles = $row['PublishArticles'];

		$state = $action == 'P' ? 'Y' : 'S';
		if ($publish_articles == 'Y') {
			$art_sql = "select * from Articles where IdPublication = " . $pub
			         . " and NrIssue = " . $issue . " and IdLanguage = " . $language;
			$art_res = mysql_query($art_sql);
			while ($art_res && $art_row = mysql_fetch_assoc($art_res)) {
				$article = $art_row['Number'];

				$sql = "update Articles set Published = '" . $state . "' where Number = " . $article
				     . " and IdLanguage = " . $language;
				mysql_query($sql);
			}
		}
		$sql = "update Issues set PublicationDate = CURDATE(), Published = '" . $state
		     . "' where IdPublication = " . $pub . " and Number = " . $issue
		     . " and IdLanguage = " . $language;
		mysql_query($sql);
	}
}

function automatic_publishing($datetime)
{
	publish_issues($datetime);
	publish_articles($datetime);
}

$datetime = strftime("%Y-%m-%d %H:%M:00");
automatic_publishing($datetime);

?>
