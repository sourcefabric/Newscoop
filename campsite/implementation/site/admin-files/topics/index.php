<?php
require_once($GLOBALS['g_campsiteDir']. "/$ADMIN_DIR/topics/topics_common.php");

$f_show_languages = camp_session_get('f_show_languages', array());

$topics = Topic::GetTree();
// return value is sorted by language
$allLanguages = Language::GetLanguages(null, null, null, array(), array(), true);

$loginLanguageId = 0;
$loginLanguage = Language::GetLanguages(null, camp_session_get('TOL_Language', 'en'), null, array(), array(), true);
if (is_array($loginLanguage) && count($loginLanguage) > 0) {
	$loginLanguage = array_pop($loginLanguage);
	$loginLanguageId = $loginLanguage->getLanguageId();
}

if (count($f_show_languages) <= 0) {
	$f_show_languages = DbObjectArray::GetColumn($allLanguages, 'Id');
}

$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Topics"), "");
echo camp_html_breadcrumbs($crumbs);

include_once($GLOBALS['g_campsiteDir']."/$ADMIN_DIR/javascript_common.php");

camp_html_display_msgs("0.5em", 0);
?>
<script>
function checkAllLang()
{
	<?php foreach ($allLanguages as $tmpLanguage) { ?>
	document.getElementById("checkbox_<?php p($tmpLanguage->getLanguageId()); ?>").checked = true;
	<?php } ?>
} // fn checkAllLang


function uncheckAllLang()
{
	<?php foreach ($allLanguages as $tmpLanguage) { ?>
	document.getElementById("checkbox_<?php p($tmpLanguage->getLanguageId()); ?>").checked = false;
	<?php } ?>
} // fn uncheckAllLang
</script>

<P>
<FORM action="index.php" method="POST">
<table class="table_input">
<tr>
	<td>
		<table cellpadding="1" cellspacing="3"><tr>
		<td><b><?php putGS("Show languages:"); ?></b></td>
		<td><input type="button" value="<?php putGS("Select All"); ?>" onclick="checkAllLang();" class="button" style="font-size: smaller;"></td>
		<td><input type="button" value="<?php putGS("Select None"); ?>" onclick="uncheckAllLang();" class="button" style="font-size:smaller;"></td>
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
<?php  if ($g_user->hasPermission("ManageTopics")) { ?>
<form method="POST" action="do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
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
			 								$loginLanguageId,
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
	<TD ALIGN="center" VALIGN="TOP" style="padding-left: 10px; padding-right: 10px;"><?php  putGS("Order"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP" style="padding-left: 10px; padding-right: 10px;"><?php  putGS("Language"); ?></TD>
	<TD ALIGN="LEFT" VALIGN="TOP"><?php  putGS("Topic"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP"></TD>
</TR>

<?php

$counter = 0;
$color= 0;
$isFirstTopic = true;
$aTopicOrder = array();

//$t = new Topic(1);
//$t->fetch();
//var_dump($topics[0]);
//die;
foreach ($topics as $topicPath) {
	$currentTopic = camp_array_peek($topicPath, false, -1);

	$parentId = $currentTopic->getParentId();
	if (!isset($aTopicOrder[$parentId])) {
	    $sql = 'SELECT DISTINCT(TopicOrder) FROM Topics'
	        .' WHERE ParentId = '.$parentId
	        .' ORDER BY TopicOrder ASC, LanguageId ASC';
	    $aTopicOrder[$parentId] = $g_ado_db->GetCol($sql);
    }
	
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
		
		<?php if ($isFirstTranslation && count($aTopicOrder[$parentId]) > 1) { ?>
		<TD ALIGN="right" valign="middle" NOWRAP <?php if (!$isFirstTopic && $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?>>
		<?php
		    $topicOrder = $currentTopic->getProperty('TopicOrder');
		    $topicPosition =  array_search($topicOrder, $aTopicOrder[$parentId]);
	    ?>
			<table cellpadding="0" cellspacing="0">
			<tr>
				<td width="18px">
					<?php if ($topicPosition > 0) { ?>
						<A HREF="/<?php echo $ADMIN; ?>/topics/do_position.php?f_topic_number=<?php p($currentTopic->getTopicId()); ?>&f_move=up_rel&f_position=1"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/up-16x16.png" width="16" height="16" border="0"></A>
					<?php } ?>
				</td>
				<td width="20px">
					<?php if ($topicOrder < camp_array_peek($aTopicOrder[$parentId], false, -1)) { ?>
						<A HREF="/<?php echo $ADMIN; ?>/topics/do_position.php?f_topic_number=<?php p($currentTopic->getTopicId()); ?>&f_move=down_rel&f_position=1"><img src="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/down-16x16.png" width="16" height="16" border="0" style="padding-left: 3px; padding-right: 3px;"></A>
					<?php } ?>
				</td>

				<td>
					<select name="f_position_<?php p($counter);?>" onChange="positionValue = this.options[this.selectedIndex].value; url = '/<?php p($ADMIN);?>/topics/do_position.php?f_topic_number=<?php p($currentTopic->getTopicId());?>&f_move=abs&f_position='+positionValue; location.href=url;" class="input_select" style="font-size: smaller;">
					<?php
					for ($j = 0; $j < count($aTopicOrder[$parentId]); $j++) {
						if ($topicPosition == $j) {
							echo "<option value=\"{$aTopicOrder[$parentId][$j]}\" selected>" . ($j + 1) . "</option>\n";
						} else {
							echo "<option value=\"{$aTopicOrder[$parentId][$j]}\">" . ($j + 1) . "</option>\n";
						}
					}
					?>
					</select>
				</td>

			</tr>
			</table>
		</TD>
		<?php } else { ?>
			<TD ALIGN="right" valign="middle" NOWRAP <?php if (!$isFirstTopic && $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?>>&nbsp;</TD>
		<?php } ?>
		<TD <?php if (!$isFirstTopic & $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?> valign="middle" align="center">
			<?php
			$topicLanguage = new Language($topicLanguageId);
			p($topicLanguage->getCode());
			?>
		</TD>

		<TD <?php if (!$isFirstTopic && $isFirstTranslation) { ?>style="border-top: 2px solid #8AACCE;"<?php } ?> valign="middle" align="left" width="450px">
			<?php
			// Append decoration of tree
			//			
			// It's hasn't got sense to describe relations between root topics
		    if ($parentId != 0) {
		        // Drawing parent parts
			    foreach ($topicPath as $topicId => $topic) {
			        // Skip root topic and current topic
			        if ($topic->getParentId() == 0 || $topicId == $currentTopic->getTopicId()) {
			            continue;
			        }
			        
			        // Is last?
			        $parentTopicPosition = array_search($topic->getProperty('TopicOrder'), $aTopicOrder[$topic->getParentId()]);
			        $lastTopicOrder = camp_array_peek($aTopicOrder[$topic->getParentId()], false, -1);
			        $isLast = $aTopicOrder[$topic->getParentId()][$parentTopicPosition] == $lastTopicOrder;
			        
		            if (!$isLast && count($aTopicOrder[$topic->getParentId()]) > 1) {
		                // If previous topic wasn't last...
		                echo '<img alt="" src="/css/tree-I.png">';
			        } else {
			            echo '<img alt="" src="/css/tree-blank.png">';
			        }
			    }

			    // Drawing for current topic
			    $lastTopicOrder = camp_array_peek($aTopicOrder[$parentId], false, -1);
			    $isLast = ($aTopicOrder[$parentId][$topicPosition] == $lastTopicOrder);
			    if ($isFirstTranslation) {
    			    if ($isLast || count($aTopicOrder[$parentId]) == 1) {
    			        // If last or only
    			        echo '<img alt="" src="/css/tree-L.png">';
    			    } else {
    			        // If non-last and non-only
    			        echo '<img alt="" src="/css/tree-T.png">';
    			    }
			    } else {
			        if (!$isLast) {
    			        // Non-first translations should be marked with I
        			    echo '<img alt="" src="/css/tree-I.png">';
			        } else {
			            // Non-first translations should be ident
			            // if topic in branch is last
			            echo '<img alt="" src="/css/tree-blank.png">';
			        }
    			}
			}

			echo " <a href='/$ADMIN/topics/edit.php"
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
	    	<td colspan="4">
	    		<FORM method="POST" action="do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
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
    	<td colspan="4">
    		<FORM method="POST" action="do_add.php" onsubmit="return <?php camp_html_fvalidate(); ?>;">
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
    $counter++;
}
?>
<?php } ?>
</table>
<?php camp_html_copyright_notice(); ?>
