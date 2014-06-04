<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/sections/section_common.php");

$translator = \Zend_Registry::get('container')->getService('translator');

if (!SecurityToken::isValid()) {
    camp_html_display_error($translator->trans('Invalid security token!'));
    exit;
}

if (!$g_user->hasPermission('ManageSection')) {
	camp_html_display_error($translator->trans("You do not have the right to add sections.", array(), 'sections'));
	exit;
}

$f_publication_id = Input::Get('f_publication_id', 'int', 0);
$f_issue_number = Input::Get('f_issue_number', 'int', 0);
$f_language_id = Input::Get('f_language_id', 'int', 0);
$f_name = trim(Input::Get('f_name', 'string', '', true));
$f_description = trim(Input::Get('f_description', '', true));
$f_number = trim(Input::Get('f_number', 'int', 0, true));
if(SaaS::singleton()->hasPermission('ManageSectionSubscriptions')) {
	$f_add_subscriptions = Input::Get('f_add_subscriptions', 'checkbox');
} else {
	$f_add_subscriptions = 0;
}
$f_url_name = trim(Input::Get('f_url_name', 'string', '', true));

if (!Input::IsValid()) {
	camp_html_display_error($translator->trans('Invalid input: $1', array('$1' => Input::GetErrorString()), 'sections'), $_SERVER['REQUEST_URI']);
	exit;
}

$issueObj = new Issue($f_publication_id, $f_language_id, $f_issue_number);
$publicationObj = new Publication($f_publication_id);

if (!$publicationObj->exists()) {
    camp_html_display_error($translator->trans('Publication does not exist.'));
    exit;
}
if (!$issueObj->exists()) {
	camp_html_display_error($translator->trans('No such issue.'));
	exit;
}

$correct = true;
$created = false;
$isValidShortName = camp_is_valid_url_name($f_url_name);

$errors = array();
if ($f_name == "") {
	$correct = false;
	$errors[] = $translator->trans('You must fill in the $1 field.', array('$1' => '"'.$translator->trans('Name').'"'));
}
if ($f_number == 0) {
	$correct= false;
	$f_number = ($f_number + 0);
	$errors[] = $translator->trans('You must fill in the $1 field.', array('$1' => '"'.$translator->trans('Number').'"'));
}
if ($f_url_name == "") {
	$correct = false;
	$errors[] = $translator->trans('You must fill in the $1 field.', array('$1' => '"'.$translator->trans('URL Name').'"'));
}
if (!$isValidShortName && trim($f_url_name) != "") {
	$correct = false;
	$errors[] = $translator->trans('The $1 field may only contain letters, digits and underscore (_) character.', array('$1' => '"' . $translator->trans('URL Name', array(), 'sections') . '"'));
}
$sectionsConstraints = array(new ComparisonOperation('idpublication', new Operator('is'), $f_publication_id),
new ComparisonOperation('nrissue', new Operator('is'), $f_issue_number),
new ComparisonOperation('number', new Operator('is'), $f_number));
$sections = Section::GetList($sectionsConstraints, null, 0, 0, $sectionsCount, true);
if ($sectionsCount > 0) {
    $correct = false;
    $errors[] = $translator->trans('The section number $1 was already in use.', array('$1' => $f_number), 'sections');
}
if ($correct) {
    $newSection = new Section($f_publication_id, $f_issue_number, $f_language_id, $f_number);
    $columns = array();
    $columns['Description'] = $f_description;
    $created = $newSection->create($f_name, $f_url_name, $columns);
    if ($created) {
	    if ($f_add_subscriptions) {
	        $numSubscriptionsAdded = Subscription::AddSectionToAllSubscriptions($f_publication_id, $f_number);
			if ($numSubscriptionsAdded == -1) {
	            $errors[] = $translator->trans('Error updating subscriptions.', array(), 'sections');
			}
	    }

        $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
        $cacheService->clearNamespace('section');

	    camp_html_goto_page("/$ADMIN/sections/edit.php?Pub=$f_publication_id&Issue=$f_issue_number&Language=$f_language_id&Section=".$newSection->getSectionNumber());
    }
}

$tmpArray = array('Pub' => $publicationObj, 'Issue' => $issueObj);
camp_html_content_top($translator->trans('Adding new section', array(), 'sections'), $tmpArray);
?>

<P>
<CENTER>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box" ALIGN="CENTER">
<TR>
	<TD COLSPAN="2"><BLOCKQUOTE>
    <?php
    foreach ($errors as $error) { ?>
		<LI><?php echo $error; ?></LI>
		<?php
	}
	if ($created) {    ?>
        <LI><?php  echo $translator->trans('The section $1 has been successfuly added.', array('$1' => '<B>'.htmlspecialchars($f_name).'</B>'), 'sections'); ?></LI>
        <?php
        if ($f_add_subscriptions) {
			if ($numSubscriptionsAdded > 0) { ?>
				<LI><?php  echo $translator->trans('A total of $1 subscriptions were updated.', array('$1' => '<B>'.$numSubscriptionsAdded.'</B>'), 'sections'); ?></LI>
	           <?php
			}
		}
    } else {
        if ($correct != 0) { ?>
        	<LI><?php  echo $translator->trans('The section could not be added.', array(), 'sections'); ?></LI>
        	<LI><?php  echo $translator->trans('Please check if another section with the same number or URL name does not exist already.', array(), 'sections'); ?></LI>
            <?php
        }
    }
    ?>
    </BLOCKQUOTE>
    </TD>
</TR>

<TR>
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
    <?php  if ($correct && $created) { ?>
        <INPUT TYPE="button" class="button" NAME="Add another" VALUE="<?php  echo $translator->trans('Add another'); ?>" ONCLICK="location.href='/admin/sections/add.php?Pub=<?php  p($f_publication_id); ?>&Issue=<?php  p($f_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
		<INPUT TYPE="button" class="button" NAME="Done" VALUE="<?php  echo $translator->trans('Done'); ?>" ONCLICK="location.href='/admin/sections/?Pub=<?php  p($f_publication_id); ?>&Issue=<?php  p($f_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
<?php  } else { ?>
		<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  echo $translator->trans('OK'); ?>" ONCLICK="location.href='add.php?Pub=<?php  p($f_publication_id); ?>&Issue=<?php  p($f_issue_number); ?>&Language=<?php  p($f_language_id); ?>'">
<?php  } ?>
	</DIV>
	</TD>
</TR>
</TABLE>
</CENTER>
<P>

<?php camp_html_copyright_notice(); ?>
