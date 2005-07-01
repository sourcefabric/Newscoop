<?php  
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/pub/issues/sections/articles/topics/topic_common.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Topic.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/DbObjectArray.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0);
$Article = Input::Get('Article', 'int', 0);
$TopicId = Input::Get('IdCateg', 'int', 0, true);
$TopicOffset = Input::Get('CatOffs', 'int', 0, true);
if ($TopicOffset < 0) {
	$TopicOffset = 0;
}
$TopicsPerPage = Input::Get('lpp', 'int', 20, true);
$searchTopicsString = trim(Input::Get('search_topics_string', 'string', '', true));

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

$publicationObj =& new Publication($Pub);
if (!$publicationObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Publication does not exist.'));
	exit;	
}

$issueObj =& new Issue($Pub, $Language, $Issue);
if (!$issueObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Issue does not exist.'));
	exit;	
}

$sectionObj =& new Section($Pub, $Issue, $Language, $Section);
if (!$sectionObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Section does not exist.'));
	exit;		
}

$articleObj =& new Article($Pub, $Issue, $Section, $Language, $Article);
if (!$articleObj->exists()) {
	CampsiteInterface::DisplayError(getGS('Article does not exist.'));
	exit;		
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);

$articleTopics =& ArticleTopic::GetArticleTopics($Article);
$articleTopicsIds = DbObjectArray::GetColumn($articleTopics, 'Id');

$viewTopic =& new Topic($TopicId);
$topicParents =& $viewTopic->getPath();

$Top = '';
if ($TopicId != 0) {
	$Top = "<A HREF=index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage> Top </A>";
}

$Path = '';
foreach ($topicParents as $parent) {
	$Path .= "<A HREF=index.php?Pub=$Pub&Issue=$Issue&Section=$Section&Article=$Article&Language=$Language&sLanguage=$sLanguage&IdCateg=".$parent->getTopicId()."> ".$parent->getName()."</A> /";
}
$Path = $Top . '/' . $Path;

if ($searchTopicsString != '') {
	$subtopics =& Topic::GetByName($searchTopicsString);
	$totalSubtopics = count($subtopics);
}
else {
	$subtopics =& $viewTopic->getSubtopics(null, /*$sLanguage, */
		array('LIMIT' => array('START' => $TopicOffset, 'MAX_ROWS'=>($TopicsPerPage))));
	$totalSubtopics = count($viewTopic->getSubtopics());
}

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj, 'Article'=>$articleObj);
CampsiteInterface::ContentTop(getGS('Article topics'), $topArray);
?>

<p>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
<TR>
	<TD><A HREF="../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/back.png" BORDER="0"></A></TD>
	<TD><A HREF="../edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>" ><B><?php  putGS("Back to Edit Article"); ?></B></A></TD>
</TR>
</TABLE>

<P>
<?php 
if (count($articleTopics) > 0) {
	$color= 0;
	?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP" style="padding: 5px;"><B><?php  putGS("Topic name"); ?></B></TD>
		<?php  if ($articleObj->userCanModify($User)) { ?>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" style="padding: 5px;"><B><?php  putGS("Delete"); ?></B></TD>
		<?php  } ?>
	</TR>
	
	<?php 
	foreach ($articleTopics as $topic) { 
		$topicPath =& $topic->getPath();
		?>
		<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
			<TD style="padding-left: 8px;">
				<?php 
				foreach ($topicPath as $item) {
					echo ' / '.htmlspecialchars($item->getName()); 
				}
				?>&nbsp;
			</TD>
			<?php  if ($articleObj->userCanModify($User)) { ?>
			<TD ALIGN="CENTER">
				<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/topics/do_del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&DelTopic=<?php p($topic->getTopicId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&IdCateg=<?php p($TopicId); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete'); ?>" title="<?php  putGS('Delete'); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the topic $1?', htmlspecialchars($topic->getName())); ?>');"></A>
			</TD>
			<?php  } ?>
		</TR>
		<?php 
	} // foreach
	?>	
	</TABLE>
	<p>
	<?php  
} else { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No article topics.'); ?></LI>
	</BLOCKQUOTE>
	<?php  
} 
?>

<br><br>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
	<TD ALIGN="LEFT" style="padding-left: 10px;"><b><?php  putGS("Available topics"); ?></b></TD>
	<TD ALIGN="RIGHT" style="padding-right: 10px;">
		<FORM METHOD="GET" ACTION="index.php" NAME="">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Article" VALUE="<?php  p($Article); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<INPUT TYPE="HIDDEN" NAME="sLanguage" VALUE="<?php  p($sLanguage); ?>">
		<INPUT TYPE="HIDDEN" NAME="IdCateg" VALUE="<?php  p($TopicId); ?>">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_input">
		<TR>
			<TD><?php  putGS('Topic'); ?>:</TD>
			<TD><INPUT TYPE="TEXT" NAME="search_topics_string" SIZE="8" MAXLENGTH="20" value="<?php p($searchTopicsString); ?>" class="input_text"></TD>
			<TD><INPUT TYPE="submit" NAME="Search" VALUE="<?php  putGS('Search'); ?>" class="button"></TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>
</TABLE>

<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="1" WIDTH="100%" style="padding-left: 10px; padding-right: 10px;">
<TR>
	<TD ALIGN="RIGHT" WIDTH="1%" NOWRAP VALIGN="TOP" >&nbsp;<?php  putGS("Topic"); ?>:</TD>
	<TD class="list_row_even" VALIGN="TOP" style="padding-left: 5px;"><B><?php p($Path);?></B></TD>
</TR>
</TABLE>
<p>
<?php 
if (count($subtopics) > 0) {
	$color= 0;
	?>
	<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
	<TR class="table_list_header">
		<TD ALIGN="LEFT" VALIGN="TOP"  style="padding: 5px;"><B><?php  putGS("Name"); ?></B></TD>
		<TD ALIGN="LEFT" VALIGN="TOP" WIDTH="1%" style="padding: 5px;" ><B><?php  putGS("Add"); ?></B></TD>
	</TR>
	<?php 
	foreach ($subtopics as $subtopic) {
		?>
		<TR <?php  if ($color) { $color=0; ?>class="list_row_even"<?php  } else { $color=1; ?>class="list_row_odd"<?php  } ?>>
		<TD style="padding-left: 8px;">
			<?php if ($subtopic->hasSubtopics()) { ?>
			<A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&IdCateg=<?php p($subtopic->getTopicId());?>">
			<?php } ?>
			<?php p(htmlspecialchars($subtopic->getName())); ?>
			<?php if ($subtopic->hasSubtopics()) { ?>
			</A>
			<?php } ?>
		</TD>
		
		<TD ALIGN="CENTER">
		<?php 
		if (!in_array($subtopic->getTopicId(), $articleTopicsIds)) {
		?>
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/topics/do_add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&IdCateg=<?php p($TopicId);?>&AddTopic=<?php p($subtopic->getTopicId()); ?>&AddTopicLanguage=<?php p($subtopic->getLanguageId()); ?>"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add_topic_to_article.png" BORDER="0" ALT="<?php  putGS('Add'); ?>" title="<?php  putGS('Add'); ?>"></A>
		<?php 
		} else {
		    echo "&nbsp;";
		}
		?>
		</TD>
    </TR>
	<?php 
	} // foreach
	?>	
	
	<?php if ($searchTopicsString == '') { ?>
	<TR>
		<TD COLSPAN="2" NOWRAP>
		<?php  if ($TopicOffset <= 0) { ?>
			&lt;&lt; <?php  putGS('Previous'); ?>
		<?php  } else { ?>
			<B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&IdCateg=<?php p($TopicId);?>&CatOffs=<?php  print (max(0, ($TopicOffset - $TopicsPerPage))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
		<?php  } ?>
		<?php  if (($TopicOffset + $TopicsPerPage) >= $totalSubtopics) { ?>
			 | <?php  putGS('Next'); ?> &gt;&gt;
		<?php  } else { ?>
			 | <B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($Article); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&IdCateg=<?php p($TopicId);?>&CatOffs=<?php  print ($TopicOffset + $TopicsPerPage); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
		<?php } ?>	
		</TD>
	</TR>
	<?php } ?>
</TABLE>
<?php  
}
else { ?>
	<BLOCKQUOTE>
	<LI><?php  putGS('No topics.'); ?></LI>
	</BLOCKQUOTE>
	<?php  
} 

CampsiteInterface::CopyrightNotice(); ?>