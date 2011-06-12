<?PHP
camp_load_translation_strings("pub");
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Publication.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Alias.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/UrlType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Log.php');


function camp_is_publication_conflicting($p_publicationName)
{
	global $ADMIN;
	$publications = Publication::GetPublications($p_publicationName);
	if (count($publications) > 0) {
		$pubObj = array_pop($publications);
		$pubLink = "<A HREF=\"/$ADMIN/pub/edit.php?Pub=".$pubObj->getPublicationId().'">'. $pubObj->getName() ."</A>";
		$msg = getGS("The publication name you specified conflicts with publication '$1'.", $pubLink);
		camp_html_add_msg($msg);
	}
}


/**
 * Check if the alias given is already in use.  If so, a user error message
 * is created.
 *
 * @param mixed $p_alias
 * 		Can be a string or an int.
 * @return void
 */
function camp_is_alias_conflicting($p_alias)
{
	global $ADMIN;

	if (!is_numeric($p_alias)) {
		// The alias given is a name, which means it doesnt exist yet.
		// Check if the name conflicts with any existing alias names.
		$aliases = Alias::GetAliases(null, null, $p_alias);
		$alias = array_pop($aliases);
		if ($alias) {
			$pubId = $alias->getPublicationId();
			$pubObj = new Publication($pubId);
			$pubLink = "<A HREF=\"/$ADMIN/pub/edit.php?Pub=$pubId\">". $pubObj->getName() ."</A>";
			$msg = getGS("The publication alias you specified conflicts with publication '$1'.", $pubLink);
			camp_html_add_msg($msg);
		}
	} else {
		// The alias given is a number, which means it already exists.
		// Check if the alias ID is already in use by another publication.
		$aliases = Alias::GetAliases($p_alias);
		$alias = array_pop($aliases);
		if ($alias) {
			$pubs = Publication::GetPublications(null, $alias->getId());
			if (count($pubs) > 0) {
				$pubObj = array_pop($pubs);
				$pubLink = "<A HREF=\"/$ADMIN/pub/edit.php?Pub=".$pubObj->getPublicationId().'">'. $pubObj->getName() ."</A>";
				$msg = getGS("The publication alias you specified conflicts with publication '$1'.", $pubLink);
				camp_html_add_msg($msg);
			}
		}
	}
}
?>