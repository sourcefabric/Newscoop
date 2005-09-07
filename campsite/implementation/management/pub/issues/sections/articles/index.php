<?php 
require_once($_SERVER['DOCUMENT_ROOT']. "/$ADMIN_DIR/pub/issues/sections/articles/article_common.php");
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/DbObjectArray.php');
require_once($_SERVER['DOCUMENT_ROOT']. '/classes/ArticlePublish.php');

list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

$Pub = Input::Get('Pub', 'int', 0);
$Issue = Input::Get('Issue', 'int', 0);
$Section = Input::Get('Section', 'int', 0);
$Language = Input::Get('Language', 'int', 0);
$sLanguage = Input::Get('sLanguage', 'int', 0, true);
$ArticleOffset = Input::Get('ArtOffs', 'int', 0, true);
$ArticlesPerPage = Input::Get('lpp', 'int', 10, true);

if (!Input::IsValid()) {
	CampsiteInterface::DisplayError(getGS('Invalid input: $1', Input::GetErrorString()), $_SERVER['REQUEST_URI']);
	exit;	
}

if ($ArticleOffset < 0) {
	$ArticleOffset = 0;
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
	CampsiteInterface::DisplayError(getGS('Section does not exist.'), $BackLink);
	exit;		
}

$languageObj =& new Language($Language);
$sLanguageObj =& new Language($sLanguage);
$allArticleLanguages =& Article::GetAllLanguages();

if ($sLanguage) {
	// Only show a specific language.
	$allArticles = Article::GetArticles($Pub, $Issue, $Section, $sLanguage, null, $Language,
		$ArticlesPerPage, $ArticleOffset);
	$totalArticles = count(Article::GetArticles($Pub, $Issue, $Section, $sLanguage));
	$numUniqueArticles = $totalArticles;
	$numUniqueArticlesDisplayed = count($allArticles);
} else {
	// Show articles in all languages.
	$allArticles =& Article::GetArticles($Pub, $Issue, $Section, null, null, $Language,
		$ArticlesPerPage, $ArticleOffset, true);
	$totalArticles = count(Article::GetArticles($Pub, $Issue, $Section, null));
	$numUniqueArticles = Article::GetNumUniqueArticles($Pub, $Issue, $Section);
	$numUniqueArticlesDisplayed = count(array_unique(DbObjectArray::GetColumn($allArticles, 'Number')));
}

$previousArticleId = 0;

$topArray = array('Pub' => $publicationObj, 'Issue' => $issueObj, 
				  'Section' => $sectionObj);
CampsiteInterface::ContentTop(getGS('Articles'), $topArray);
?>
<P>
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" WIDTH="100%">
<TR>
<?php if ($User->hasPermission('AddArticle')) { ?>
	<TD>
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="1">
		<TR>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>" ><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/add_article.png" BORDER="0"></A></TD>
			<TD><A HREF="add.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&Back=<?php  p(urlencode($_SERVER['REQUEST_URI'])); ?>" ><B><?php  putGS("Add new article"); ?></B></A></TD>
		</TR>
		</TABLE>
	</TD>
<?php  } ?>
	<TD ALIGN="RIGHT">
		<FORM METHOD="GET" ACTION="index.php" NAME="">
		<INPUT TYPE="HIDDEN" NAME="Pub" VALUE="<?php  p($Pub); ?>">
		<INPUT TYPE="HIDDEN" NAME="Issue" VALUE="<?php  p($Issue); ?>">
		<INPUT TYPE="HIDDEN" NAME="Section" VALUE="<?php  p($Section); ?>">
		<INPUT TYPE="HIDDEN" NAME="Language" VALUE="<?php  p($Language); ?>">
		<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="3" class="table_input">
		<TR>
			<TD><?php  putGS('Language'); ?>:</TD>
			<TD valign="middle">
				<SELECT NAME="sLanguage" class="input_select">
				<option></option>
				<?php 
				foreach ($allArticleLanguages as $languageItem) {
					echo '<OPTION value="'.$languageItem->getLanguageId().'"' ;
					if ($languageItem->getLanguageId() == $sLanguage) {
						echo " selected";
					}
					echo '>'.htmlspecialchars($languageItem->getName()).'</option>';
				} ?>
				</SELECT>
			</TD>
			<TD><INPUT TYPE="submit" NAME="Search" VALUE="<?php  putGS('Search'); ?>" class="button"></TD>
		</TR>
		</TABLE>
		</FORM>
	</TD>
</tr>
</TABLE>

<P>
<?php 
if ($numUniqueArticlesDisplayed > 0) {
	$counter = 0;
	$color = 0;
?>
<TABLE BORDER="0" CELLSPACING="1" CELLPADDING="3" WIDTH="100%" class="table_list">
<TR class="table_list_header">
	<TD ALIGN="LEFT" VALIGN="TOP"  ><?php  putGS("Name <SMALL>(click to edit)</SMALL>"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Type"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Language"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Status"); ?></TD>
	<?php if ($User->hasPermission('Publish')) { ?>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Order"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Scheduled Publishing"); ?></TD>
	<?php } ?>	
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Preview"); ?></TD>
	<?php  if ($User->hasPermission('AddArticle')) { ?>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Translate"); ?></TD>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Duplicate"); ?></TD>
	<?php  } ?>
	<?php  if ($User->hasPermission('DeleteArticle')) { ?>
	<TD ALIGN="center" VALIGN="TOP" WIDTH="1%" ><?php  putGS("Delete"); ?></TD>
	<?php  } ?>	
</TR>
<?php 
$uniqueArticleCounter = 0;
foreach ($allArticles as $articleObj) {
	if ($articleObj->getArticleId() != $previousArticleId) {
		$uniqueArticleCounter++;
	}
	if ($uniqueArticleCounter > $ArticlesPerPage) {
		break;
	}
	$timeDiff = camp_time_diff_str($articleObj->getLockTime());
	if ($articleObj->isLocked() && ($timeDiff['days'] <= 0) && ($articleObj->getLockedByUser() != $User->getId())) {
	    $rowClass = "article_locked";
	}
	else {
    	if ($color) { 
    	    $color=0; 
    	    $rowClass = "list_row_even";
    	} else { 
    	    $color=1; 
    	    $rowClass = "list_row_odd";
    	} 
	}
	?>	
	<TR class="<?php p($rowClass); ?>">
		<TD <?php if ($articleObj->getArticleId() == $previousArticleId) { ?>style="padding-left: 20px;"<?php } ?>>
		
		<?php
		if ($articleObj->isLocked() && ($timeDiff['days'] <= 0) && ($articleObj->getLockedByUser() != $User->getId())) {
            $lockUserObj =& new User($articleObj->getLockedByUser());
			if ($timeDiff['hours'] > 0) {
				$lockInfo = getGS('The article has been locked by $1 ($2) $3 hour(s) and $4 minute(s) ago.',
					  htmlspecialchars($lockUserObj->getName()),
					  htmlspecialchars($lockUserObj->getUserName()),
					  $timeDiff['hours'], $timeDiff['minutes']); 
			}
			else {
				$lockInfo = getGS('The article has been locked by $1 ($2) $3 minute(s) ago.',
					  htmlspecialchars($lockUserObj->getName()),
					  htmlspecialchars($lockUserObj->getUserName()),
					  $timeDiff['minutes']);
			}
		    
		    ?>
		    <img src="/<?php echo $ADMIN; ?>/img/icon/lock.png" width="22" height="22" border="0" alt="<?php  p($lockInfo); ?>" title="<?php p($lockInfo); ?>">
		    <?php
		}
		// Can the user edit the article?
		$userCanEdit = $articleObj->userCanModify($User);
		if ($userCanEdit) { ?>
		<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/edit.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>"><?php } ?><?php  p(htmlspecialchars($articleObj->getTitle())); ?>&nbsp;<?php if ($userCanEdit) { ?></A><?php } ?>
		</TD>
		<TD ALIGN="RIGHT">
			<?php p(htmlspecialchars($articleObj->getType()));  ?>
		</TD>

		<TD>
			<?php
			p(htmlspecialchars($articleObj->getLanguageName())); 
			?>
		</TD>
		<TD ALIGN="CENTER">
			<?php 
			$statusLink = "<A HREF=\"/$ADMIN/pub/issues/sections/articles/status.php?Pub=". $Pub
				.'&Issue='.$Issue.'&Section='.$Section.'&Article='.$articleObj->getArticleId()
				.'&Language='.$Language.'&sLanguage='.$articleObj->getLanguageId()
				.'&Back='.urlencode($_SERVER['REQUEST_URI']).'">';
			if ($articleObj->getPublished() == "Y") { 
				$statusWord = "Published";
			}
			elseif ($articleObj->getPublished() == "N") { 
				$statusWord = "New";
			}
			elseif ($articleObj->getPublished() == "S") { 
				$statusWord = "Submitted";
			}
			$enableStatusLink = false;
			if ($User->hasPermission('Publish')) {
				$enableStatusLink = true;
			}
			elseif ($User->hasPermission('ChangeArticle') 
					&& ($articleObj->getPublished() != 'Y')) {
				$enableStatusLink = true;
			}
			elseif ( ($User->getId() == $articleObj->getUserId())
					&& ($articleObj->getPublished() == "N")) {
				$enableStatusLink = true;
			}
			if ($enableStatusLink) {
				echo $statusLink;
			}
			putGS($statusWord);
			if ($enableStatusLink) {
				echo "</a>";
			}
			?>
		</TD>
		
		<?php
		// The MOVE links  
		if ($User->hasPermission('Publish')) { 
			if (($articleObj->getArticleId() == $previousArticleId) || ($numUniqueArticles <= 1))  {
				?>
				<TD ALIGN="CENTER" valign="middle" NOWRAP></TD>
				<?php
			}
			else {
				?>
				<TD ALIGN="right" valign="middle" NOWRAP>
					<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="22px">
							<?php if (($ArticleOffset > 0) || ($uniqueArticleCounter != 1)) { ?>
								<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/do_move.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>&ArticleLanguage=<?php p($articleObj->getLanguageId()); ?>&move=up_rel&pos=1&ArtOffs=<?php p($ArticleOffset); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/up.png" width="16" height="16" border="0"></A>
							<?php } ?>
						</td>
						<td width="22px">
							<?php if (($uniqueArticleCounter+$ArticleOffset) < $numUniqueArticles) { ?>
								<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/do_move.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>&ArticleLanguage=<?php p($articleObj->getLanguageId()); ?>&move=down_rel&pos=1&ArtOffs=<?php p($ArticleOffset); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><img src="/<?php echo $ADMIN; ?>/img/icon/down.png" width="16" height="16" border="0" style="padding-left: 3px; padding-right: 3px;"></A>
							<?php } ?>
						</td>
						<form method="GET" action="do_move.php">
						<input type="hidden" name="Pub" value="<?php p($Pub); ?>">
						<input type="hidden" name="Issue" value="<?php p($Issue); ?>">
						<input type="hidden" name="Section" value="<?php p($Section); ?>">
						<input type="hidden" name="Language" value="<?php p($Language); ?>">
						<input type="hidden" name="sLanguage" value="<?php p($sLanguage); ?>">
						<input type="hidden" name="ArticleLanguage" value="<?php p($articleObj->getLanguageId()); ?>">
						<input type="hidden" name="Article" value="<?php p($articleObj->getArticleId()); ?>">
						<input type="hidden" name="ArtOffs" value="<?php p($ArticleOffset); ?>">
						<input type="hidden" name="Back" value="<?php p($_SERVER['REQUEST_URI']); ?>">
						<input type="hidden" name="move" value="abs">
						<td>
							<select name="pos" onChange="this.form.submit();" class="input_select">
							<?php
							for ($j = 1; $j <= $numUniqueArticles; $j++) {
								if (($ArticleOffset + $uniqueArticleCounter) == $j) {
									echo "<option value=\"$j\" selected>$j</option>\n";
								} else {
									echo "<option value=\"$j\">$j</option>\n";
								}
							}
							?>
							</select>
						</td>
						</form>
					</tr>
					</table>
				</TD>
				<?php  
				}
		} // if user->hasPermission('publish') 
		?>
		
		<?php if ($User->hasPermission('Publish')) { ?>
		<TD ALIGN="CENTER">
			<?php if ($articleObj->getPublished() != 'N') { 
				$events =& ArticlePublish::GetArticleEvents($articleObj->getArticleId(),
					$articleObj->getLanguageId());?>
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/autopublish.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language);?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><img src="/<?php p($ADMIN); ?>/img/icon/<?php p((count($events) > 0) ? 'automatic_publishing_active.png':'automatic_publishing.png'); ?>" alt="<?php  putGS("Scheduled Publishing"); ?>" title="<?php  putGS("Scheduled Publishing"); ?>" border="0" width="22" height="22"></A>
			<?php 
			} else { ?>
				&nbsp;<?PHP
			}
			?>
		</TD>
		<?php } ?>
		<TD ALIGN="CENTER">
			<A HREF="" ONCLICK="window.open('/<?php echo $ADMIN; ?>/pub/issues/sections/articles/preview.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php  p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($articleObj->getLanguageId()); ?>', 'fpreview', 'resizable=yes, menubar=no, toolbar=yes, width=800, height=600'); return false"><img src="/<?php p($ADMIN); ?>/img/icon/preview.png" alt="<?php  putGS("Preview"); ?>" title="<?php putGS('Preview'); ?>" border="0" width="22" height="22"></A>
		</TD>

		<?php  if ($User->hasPermission('AddArticle')) { ?>
		<TD ALIGN="CENTER">
			<?php  if ($articleObj->getArticleId() != $previousArticleId) { ?>
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/translate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><img src="/<?php p($ADMIN); ?>/img/icon/translate.png" alt="<?php  putGS("Translate"); ?>" title="<?php  putGS("Translate"); ?>" border="0" width="22" height="22"></A>
			<?php  } else { ?>
				&nbsp;
			<?php  } ?>
		</TD>
		
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/duplicate.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($articleObj->getLanguageId()); ?>&Back=<?php p(urlencode($_SERVER['REQUEST_URI'])); ?>"><img src="/<?php p($ADMIN); ?>/img/icon/duplicate.png" alt="<?php  putGS("Duplicate"); ?>" title="<?php  putGS("Duplicate"); ?>" border="0" width="22" height="22"></A>
		</TD>
		<?php  } ?>

		<?php  if ($User->hasPermission('DeleteArticle')) { ?>
		<TD ALIGN="CENTER">
			<A HREF="/<?php echo $ADMIN; ?>/pub/issues/sections/articles/do_del.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Article=<?php p($articleObj->getArticleId()); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php p($articleObj->getLanguageId()); ?>&ArtOffs=<?php p($ArticleOffset); ?>" onclick="return confirm('<?php putGS('Are you sure you want to delete the article $1 ($2)?', '&quot;'.camp_javascriptspecialchars($articleObj->getTitle()).'&quot', camp_javascriptspecialchars($articleObj->getLanguageName())); ?>');"><IMG SRC="/<?php echo $ADMIN; ?>/img/icon/delete.png" BORDER="0" ALT="<?php  putGS('Delete'); ?>" title="<?php  putGS('Delete'); ?>" width="16" height="16"></A>
		</TD>
		<?php  }
		if ($articleObj->getArticleId() != $previousArticleId)
			$previousArticleId = $articleObj->getArticleId();
		?>	
	</TR>
	<?php 
} // foreach
?>	
<TR>
	<TD NOWRAP>
		<?php 
    	if ($ArticleOffset > 0) { ?>
			<B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p(max(0, ($ArticleOffset - $ArticlesPerPage))); ?>">&lt;&lt; <?php  putGS('Previous'); ?></A></B>
		<?php  }

    	if ( ($ArticleOffset + $ArticlesPerPage) < $numUniqueArticles) { 
    		if ($ArticleOffset > 0) {
    			?>|<?php
    		}
    		?>
			 <B><A HREF="index.php?Pub=<?php  p($Pub); ?>&Issue=<?php  p($Issue); ?>&Section=<?php  p($Section); ?>&Language=<?php  p($Language); ?>&sLanguage=<?php  p($sLanguage); ?>&ArtOffs=<?php  p(min( ($numUniqueArticles-1), ($ArticleOffset + $ArticlesPerPage))); ?>"><?php  putGS('Next'); ?> &gt;&gt</A></B>
		<?php  } ?>
	</TD>
	<td colspan="3">
		<?php putGS("$1 articles found", $numUniqueArticles); ?>
	</td>
</TR>
</TABLE>
<?php  } else { ?><BLOCKQUOTE>
	<LI><?php  putGS('No articles.'); ?></LI>
	</BLOCKQUOTE>
<?php  } ?>
<?php CampsiteInterface::CopyrightNotice(); ?>