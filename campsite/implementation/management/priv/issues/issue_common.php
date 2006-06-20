<?PHP
camp_load_translation_strings("issues");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Issue.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Language.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');

/**
 * Check if the given parameters match an existing issue.  All parameters
 * should be for the issue you are adding/editing.  If you are adding,
 * set $p_isExistingIssue to FALSE, and if you are editing, set it to TRUE.
 *
 * @param int $p_publicationId
 * @param int $p_issueNumber
 * @param int $p_languageId
 * @param string $p_urlName
 * @param boolean $p_isExistingIssue
 * 		Set this to true if the issue already exists.
 * @return string
 * 		Return empty string on success, error message on failure.
 */
function camp_is_issue_conflicting($p_publicationId, $p_issueNumber, $p_languageId, $p_urlName, $p_isExistingIssue)
{
	global $ADMIN;
	// The tricky part - language ID and URL name must be unique.
	$conflictingIssues = Issue::GetIssues($p_publicationId, $p_languageId, null, $p_urlName);
	$conflictingIssue = array_pop($conflictingIssues);

	// Check if the issue conflicts with another issue.

	// If the issue exists, we have to make sure the conflicting issue is not
	// itself.
	$isSelf = ($p_isExistingIssue && ($conflictingIssue->getIssueNumber() != $p_issueNumber));
	if (is_object($conflictingIssue) && !$isSelf) {
		$conflictingIssueLink = "/$ADMIN/issues/edit.php?"
			."Pub=$p_publicationId"
			."&Issue=".$conflictingIssue->getIssueNumber()
			."&Language=".$conflictingIssue->getLanguageId();

		$errMsg = getGS('The language and URL name must be unique for each issue in this publication.')."<br>".getGS('The values you are trying to set conflict with issue "$1$2. $3 ($4)$5".',
			"<a href='$conflictingIssueLink'>",
			$conflictingIssue->getIssueNumber(),
			$conflictingIssue->getName(),
			$conflictingIssue->getLanguageName(),
			'</a>');
		return $errMsg;
	}
	return "";
}
?>