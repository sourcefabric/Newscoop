<?PHP
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Publication.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Issue.php");
require_once($_SERVER['DOCUMENT_ROOT']."/classes/Section.php");

global $Campsite;

/**
 * $Campsite["issues"] in indexed by publication ID.
 * $Campsite["sections"] is indexed by publication ID, issue number, and issue language.
 */
$Campsite["publications"] = Publication::GetPublications();
$Campsite["issues"] = array();
$Campsite["sections"] = array();
foreach ($Campsite["publications"] as $publication) {
	$Campsite["issues"][$publication->getPublicationId()] = 
		Issue::GetIssues($publication->getPublicationId(), null, null, $publication->getLanguageId(), 
			array('ORDER BY'=>array('Number'=>'DESC'), 'LIMIT' => '5'));
	foreach ($Campsite["issues"][$publication->getPublicationId()] as $issue) {
		$Campsite["sections"][$issue->getPublicationId()][$issue->getIssueNumber()][$issue->getLanguageId()] = 
			Section::GetSections($issue->getPublicationId(), 
				$issue->getIssueNumber(), $issue->getLanguageId());
	}
}

?>