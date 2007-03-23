<?PHP
camp_load_translation_strings("pub");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Publication.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Log.php');


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
			$pubObj =& new Publication($pubId);
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

/**
 * Create a forum for a publication.
 *
 * @param Publication $p_publicationObj
 * @return Phorum_forum
 */
function camp_forum_create($p_publicationObj)
{
	// create the phorum
    $forum =& new Phorum_forum();
    $forum->create();
    $p_publicationObj->setForumId($forum->getForumId());
	return $forum;
} // fn camp_forum_create


/**
 * Update the forum config.
 *
 * @param Phorum_forum $p_forum
 * @param string $p_publicationName
 * @param boolean $p_enabled
 * @param boolean $p_publicPostingEnabled
 */
function camp_forum_update($p_forum, $p_publicationName, $p_enabled, $p_publicPostingEnabled)
{
	$p_forum->setName($p_publicationName);
	if ($p_publicPostingEnabled) {
		$p_forum->setPublicPermissions($p_forum->getPublicPermissions()
									 | PHORUM_USER_ALLOW_NEW_TOPIC
									 | PHORUM_USER_ALLOW_REPLY);
	} else {
		$p_forum->setPublicPermissions($p_forum->getPublicPermissions()
									 & !PHORUM_USER_ALLOW_NEW_TOPIC
									 & !PHORUM_USER_ALLOW_REPLY);
	}
	$p_forum->setIsVisible($p_enabled);
} // fn camp_forum_update
?>