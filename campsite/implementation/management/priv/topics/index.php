<?php
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/topics/topics_common.php");

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$f_show_languages = camp_session_get('f_show_languages', array());

$topics = Topic::GetTree();
// return value is sorted by language
$allLanguages = Language::GetLanguages();

if (count($f_show_languages) <= 0) {
	$f_show_languages = DbObjectArray::GetColumn($allLanguages, 'Id');
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "");
echo camp_html_breadcrumbs($crumbs);
?>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/campsite.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.config.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.core.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.lang-enUS.js"></script>
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/javascript/fValidate/fValidate.validators.js"></script>	
<script>

function checkAll()
{
	<?php foreach ($allLanguages as $tmpLanguage) { ?>
	document.getElementById("checkbox_<?php p($tmpLanguage->getLanguageId()); ?>").checked = true;
	<?php } ?>
} // fn checkAll


function uncheckAll()
{
	<?php foreach ($allLanguages as $tmpLanguage) { ?>
	document.getElementById("checkbox_<?php p($tmpLanguage->getLanguageId()); ?>").checked = false;
	<?php } ?>
} // fn uncheckAll
</script>

<P>
<FORM action="index.php" method="POST">
<table class="table_input">
<tr>
	<td>
		<table cellpadding="1" cellspacing="3"><tr>
		<td><b><?php putGS("Show languages:"); ?></b></td>
		<td><input type="button" value="<?php putGS("Select All"); ?>" onclick="checkAll();" class="button" style="font-size: smaller;"></td>
		<td><input type="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAll();" class="button" style="font-size:smaller;"></td>
		</tr></table>
	</td>
</tr>
<tr>
	<td >
		<table cellpadding="0">
		<tr>
		<?php 
		foreach ($allLanguages as $tmpLanguage) {
			?>
			<td style="padding-left: 5px;">
				<input type="checkbox" name="f_show_languages[]" value="<?php p($tmpLanguage->getLanguageId()); ?>" id="checkbox_<?php p($tmpLanguage->getLanguageId()); ?>" <?php if (in_array($tmpLanguage->getLanguageId(), $f_show_languages)) { echo "checked"; } ?>>
			</td>
			<td>
				<?php p(htmlspecialchars($tmpLanguage->getCode())); ?>
			</td>
			<?php
		}
		?>		
			<td style="padding-left: 10px;">
				<input type="submit" name="f_show" value="<?php putGS("Show"); ?>" class="button">
			</td>
		</tr>
		</table>
	</td>	
</tr>
</table>
</FORM>

<p>
<?php  if ($User->hasPermission("ManageTopics")) { ?>	
<form method="POST" action="do_add.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
<input type="hidden" name="f_topic_parent_id" value="0">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" class="table_input">
<TR>
	<TD ALIGN="LEFT">
		<TABLE BORDER="0" CELLSPACING="2" CELLPADDING="1">
		<TR>
			<TD valign="middle"><IMG SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add.png" BORDER="0"></TD>
			<TD valign="middle"><B><?php  putGS("Add root topic:"); ?></B></TD>
			<td valign="middle">
				<SELECT NAME="f_topic_language_id" class="input_select" alt="select" emsg="<?php putGS("You must select a language."); ?>">
				<option value="0"><?php putGS("---Select language---"); ?></option>
				<?php 
			 	foreach ($allLanguages as $tmpLanguage) {
			 		camp_html_select_option($tmpLanguage->getLanguageId(), 
			 								null, 
			 								$tmpLanguage->getNativeName());
		        }
				?>			
				</SELECT>			
			</td>
			<td>
				<input type="text" name="f_topic_name" value="" class="input_text" size="20" alt="blank" emsg="<?php putGS('You must enter a name for the topic.'); ?>">
			</td>
			<td valign="middle">
				<input type="submit" name="add" value="<?php putGS("Add"); ?>" class="button">
			</td>
		</TR>
		</TABLE>
	</TD>
</TABLE>
</form>
<?php  } ?>

<p>
<?PHP
if (count($topics) == 0) { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No topics'); ?></LI>
	</BLOCKQUOTE>
	<?php  
} else {
?>
<script>
var topic_ids = new Array;
</script>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"></TD>
	<TD ALIGN="center" VALIGN="TOP" style="padding-left: 10px; padding-right: 10px;"><?php  putGS("Language"); ?></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Topic"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"></TD>
</TR>

<?php 
$color= 0;
$isFirstTopic = true;
foreach ($topics as $topicPath) { 
	$currentTopic = camp_array_peek($topicPath, false, -1);
	$topicTranslations = $currentTopic->getTranslations();
	$isFirstTranslation = true;
	foreach ($topicTranslations as $topicLanguageId => $topicName) {
		if (!in_array($topicLanguageId, $f_show_languages)) {
			continue;
		}
	?>
	<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<td <?php if (!$isFirstTopic && $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?>>
			<a href="javascript: void(0);" onclick="HideAll(topic_ids); ShowElement('add_subtopic_<?php p($currentTopic->getTopicId()); ?>_<?php p($topicLanguageId); ?>'); return false;"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/add_subtopic.png" alt="<?php putGS("Add subtopic"); ?>" title="<?php putGS("Add subtopic"); ?>" border="0"></a>
		</td>
		<td <?php if (!$isFirstTopic && $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?>>
		<?php if ($isFirstTranslation) {
			?>
			<a href="javascript: void(0);" onclick="HideAll(topic_ids); ShowElement('translate_topic_<?php p($currentTopic->getTopicId()); ?>');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/localizer.png" alt="<?php putGS("Translate"); ?>" title="<?php putGS("Translate"); ?>" border="0"></a>
			<?php
		}
		?>
		</td>
		<TD <?php if (!$isFirstTopic & $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?> valign="middle" align="center">
			<?php 
			$topicLanguage =& new Language($topicLanguageId);
			p($topicLanguage->getCode());
			?>
		</TD>
		<TD <?php if (!$isFirstTopic && $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?> valign="middle" align="left" width="450px">
			<?php 
			$printTopic = array();
			// pop off the last topic because we want to make it a hyperlink.
			$lastTopic = array_pop($topicPath);
			foreach ($topicPath as $topicId => $topic) {
				$translations = $topic->getTranslations();
				if (isset($translations[$topicLanguageId])) {
					$printTopic[] = $translations[$topicLanguageId];
				} else {
					$printTopic[] = "-----";
				}
			}
			// put it back on for other translations to use it.
			array_push($topicPath, $lastTopic);
			if (count($topicPath) > 1) {
				echo " / ";
			}
			echo htmlspecialchars(implode(" / ", $printTopic));
			echo " / <a href='/$ADMIN/topics/edit.php"
				 ."?f_topic_edit_id=".$currentTopic->getTopicId()
				 ."&f_topic_language_id=$topicLanguageId'>"
				.htmlspecialchars($topicName)."</a>";
			?>
		</TD>
		<td <?php if (!$isFirstTopic && $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?> align="center">
			<a href="<?php p("/$ADMIN/topics/do_del.php?f_topic_delete_id=".$currentTopic->getTopicId()."&f_topic_language_id=$topicLanguageId"); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the topic $1?', htmlspecialchars($topicName)); ?>');"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/delete.png" alt="<?php putGS("Delete"); ?>" title="<?php putGS("Delete"); ?>" border="0"></a>
		</td>
		</tr>
		
	    <tr id="add_subtopic_<?php p($currentTopic->getTopicId()); ?>_<?php p($topicLanguageId); ?>" style="display: none;">
	    	<td colspan="2"></td>
	    	<td colspan="3">
	    		<FORM method="POST" action="do_add.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
	    		<input type="hidden" name="f_topic_parent_id" value="<?php p($currentTopic->getTopicId()); ?>">
	    		<input type="hidden" name="f_topic_language_id" value="<?php p($topicLanguageId); ?>">
	    		<table cellpadding="0" cellspacing="0" style="border-top: 1px solid #8FBF8F; border-bottom: 1px solid #8FBF8F; background-color: #EFFFEF; padding-left: 5px; padding-right: 5px;" width="100%">
	    		<tr>
	    			<td align="left">
	    				<table cellpadding="2" cellspacing="1">
	    				<tr>
			    			<td><?php putGS("Add subtopic:"); ?></td>
			    			<td><?php p($topicLanguage->getNativeName()); ?>
			    			</td>
			    			<td><input type="text" name="f_topic_name" value="" class="input_text" size="15" alt="blank" emsg="<?php putGS('You must enter a name for the topic.'); ?>"></td>
			    			<td><input type="submit" name="f_submit" value="<?php putGS("Add"); ?>" class="button"></td>
			    		</tr>
		    			</table>
	    			</td>
	    		</tr>
	    		</table>
	    		</FORM>
	    	</td>
	    </tr>	
		<script>
		topic_ids.push("add_subtopic_"+<?php p($currentTopic->getTopicId()); ?>+"_<?php p($topicLanguageId); ?>");
		</script>
		<?php  
		$isFirstTranslation = false;
	}
	?>
    <tr id="translate_topic_<?php p($currentTopic->getTopicId()); ?>" style="display: none;">
    	<td colspan="2"></td>
    	<td colspan="3">
    		<FORM method="POST" action="do_add.php" onsubmit="return validateForm(this, 0, 1, 0, 1, 8);">
    		<input type="hidden" name="f_topic_id" value="<?php p($currentTopic->getTopicId()); ?>">
    		<table cellpadding="0" cellspacing="0" style="border-top: 1px solid #CFC467; border-bottom: 1px solid #CFC467; background-color: #FFFCDF ; padding-left: 5px; padding-right: 5px;" width="100%">
    		<tr>
    			<td align="left">
    				<table cellpadding="2" cellspacing="1">
    				<tr>
		    			<td><?php putGS("Add translation:"); ?></td>
		    			<td>
							<SELECT NAME="f_topic_language_id" class="input_select" alt="select" emsg="<?php putGS("You must select a language."); ?>">
							<option value="0"><?php putGS("---Select language---"); ?></option>
							<?php 
						 	foreach ($allLanguages as $tmpLanguage) {
						 		camp_html_select_option($tmpLanguage->getLanguageId(), 
						 								null, 
						 								$tmpLanguage->getNativeName());
					        }
							?>			
							</SELECT>
		    			</td>
		    			<td><input type="text" name="f_topic_name" value="" class="input_text" size="15" alt="blank" emsg="<?php putGS('You must enter a name for the topic.'); ?>"></td>
		    			<td><input type="submit" name="f_submit" value="<?php putGS("Translate"); ?>" class="button"></td>
		    		</tr>
		    		</table>
		    	</td>
    		</tr>
    		</table>
    		</FORM>
    	</td>
    </tr>
		<script>
		topic_ids.push("translate_topic_"+<?php p($currentTopic->getTopicId()); ?>);
		</script>
    <?php
    $isFirstTopic = false;
}
?>
<?php } ?>
</table>
<?php camp_html_copyright_notice(); ?>
