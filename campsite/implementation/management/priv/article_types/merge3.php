<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/common.php');
load_common_include_files("article_types");
require_once($_SERVER['DOCUMENT_ROOT']."/$ADMIN_DIR/camp_html.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/ArticleType.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Input.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Article.php');
// Check permissions
list($access, $User) = check_basic_access($_REQUEST);
if (!$access) {
	header("Location: /$ADMIN/logout.php");
	exit;
}

if (!$User->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to merge article types."));
	exit;
}

$f_src = trim(Input::get('f_src'));
$f_dest = trim(Input::get('f_dest'));
$f_ok = trim(Input::get('Ok'));
$f_action = trim(Input::get('f_action', 'string', 'NULL')); // Preview actions: either NEXT, PREV, ORIG

if (ereg('Back to Step 1', $f_ok)) {
	header("Location: /$ADMIN/article_types/merge.php?f_src=$f_src&f_dest=$f_dest");
	exit;
}	



$src =& new ArticleType($f_src);
$dest =& new ArticleType($f_dest);


if (ereg('Back to Step 2', $f_ok)) {
	$string = "";
	foreach ($dest->m_dbColumns as $destColumn) {
		$string .= "&f_src_". $destColumn->getName() ."=". trim(Input::get('f_src_'. $destColumn->getName()));
		}
	header("Location: /$ADMIN/article_types/merge2.php?f_src=$f_src&f_dest=$f_dest". $string);
	exit;
}	

foreach ($dest->m_dbColumns as $destColumn) {
    $tmp = trim(Input::get('f_src_'. $destColumn->getName())); 
	$f_src_c[$destColumn->getName()] = $tmp;
}

       
// calculate the merge rules
// Text->Text = OK
// Text->Body = OK
// Body->Text = NO
// Body->Body = OK
// Text->Date = NO
// Text->Topic = NO
// Body->Date = NO
// Body->Topic = NO
// Date->Text = OK
// Date->Body = OK
// Date->Date = OK
// Date->Topic = NO
// Topic->Text = OK
// Topic->Body = OK
// Topic->Date = NO
// Topic->Topic = OK


foreach ($f_src_c as $destColumn => $srcColumn) {
	$destATF =& new ArticleTypeField($f_dest, $destColumn);
	$srcATF =& new ArticleTypeField($f_src, $srcColumn);
	$ok = true;
	$errMsgs = array();
	if ($srcATF->getType() == 'Body' && $dest->getType == 'Text') {
		$errMsgs[] = 'Cannot convert a body into a text.';
		$ok = false;
	}
	if (($srcATF->getType() == 'Text' || $srcATF->getType() == 'Body' || $srcATF->getType() == 'Topic') && $dest->getType() == 'Date') {
		$errMsgs[] = 'Cannot convert a '. $srcATF->getType() .' into a date.';
		$ok = false;
	}
	if (($srcATF->getType() == 'Text' || $srcATF->getType() == 'Body' || $srcATF->getType() == 'Date') && $dest->getType() == 'Topic') {
		$errMsgs[] = 'Cannot convert a '. $srcATF->getType() .' into a topic.';
		$ok = false;
	}

}

if (!$ok) {
    // TODO print out errMsgs[]    
	header("Location: /$ADMIN/article_types/merge.php?f_src=$f_src&f_dest=$f_dest");
	exit;    
}

if (ereg('Merge!', $f_ok)) { 
	$res = ArticleType::merge($f_src, $f_dest, $f_src_c);
	header("Location: /$ADMIN/article_types/");
	exit;	
}

$articlesArray = $src->getArticlesArray();

$f_cur_preview = trim(Input::get('f_cur_preview', 'int', $articlesArray[0])); // The currently previewed article
$tmp = array_keys($articlesArray, $f_cur_preview);	
$curPos = $tmp[0];
if ($f_action == 'Orig') {
    $f_orig_article = trim(Input::get('f_orig_article', 'int', 0));
    $f_orig_lang = trim(Input::get('f_orig_lang', 'int', 0));
    $curPreview =& new Article($f_orig_lang, $f_orig_article); 
    $articleCreator =& new User($curPreview->getCreatorId());
    $articleData = $curPreview->getArticleData();
    $dbColumns = $articleData->getUserDefinedColumns();
} else { 
    $curPreview = ArticleType::merge($f_src, $f_dest, $f_src_c, $f_cur_preview, 1);	
    $articleCreator =& new User($curPreview->getCreatorId());
    $articleData = $curPreview->getArticleData();
    $dbColumns = $articleData->getUserDefinedColumns();
}
$origLang = 1; // TODO
$getString = '';
foreach ($_GET as $k => $v) {
    if ($k != 'f_action')
        $getString .= "&$k=$v";        
}
foreach ($_POST as $k => $v) {
    if ($k != 'f_action')
        $getString .= "&$k=$v";
}
$getString = substr($getString, 1);




$crumbs = array();
$crumbs[] = array(getGS("Configure"), "");
$crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
$crumbs[] = array(getGS("Merge article type"), "");
echo camp_html_breadcrumbs($crumbs);

?>
<P>
<FORM NAME="dialog" METHOD="POST" ACTION="merge3.php?f_src=<?php print $f_src; ?>&f_dest=<?php print $f_dest; ?>">
<TABLE BORDER="0" CELLSPACING="0" CELLPADDING="6" CLASS="table_input">
<TR>
	<TD COLSPAN="2">Merge Article Types<BR>Step 3 of 3</TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<b>Merge configuration for merging <?php print $src->getDisplayName(); ?> into <?php print $dest->getDisplayName(); ?>.</b><BR>
	<UL>
	<?php
	foreach ($f_src_c as $destColumn => $srcColumn) {
		$tmp = array_keys($f_src_c, $srcColumn);

		if ($srcColumn == '--None--') { 
			print "<LI><FONT COLOR=\"TAN\">Merge <b>NOTHING</b> into <b>". substr($destColumn, 1) ."</b> (Null merge warning.).</FONT></LI>";
		} else if (count($tmp) > 1) {
			print "<LI><FONT COLOR=\"TAN\">Merge <b>$srcColumn</b> into <b>". substr($destColumn, 1) ."</b></FONT> (Duplicate warning.)</FONT></LI>";
		} else {
			print "<LI><FONT COLOR=\"GREEN\">Merge <b>$srcColumn</b> into <b>". substr($destColumn, 1) ."</b>.</FONT></LI>";
		}

	} ?>


	<?php 
	// do the warning if they select NONE in red
	foreach ($src->m_dbColumns as $srcColumn) {
		if (!in_array($srcColumn->getPrintName(), $f_src_c)) 
			print "<LI><FONT COLOR=\"RED\">(!) Do <B>NOT</B> merge <b>". $srcColumn->getPrintName() ."</b> (No merge warning.)</FONT></LI>"; 
	} ?>
	</UL>	
	</TD>
	
</TR>
<TR>
	<TD COLSPAN="2">
	<B>Preview a sample of the merge configuration.</B> <SMALL>(Cycle through your articles to verify that the merge configuration is correct.)</SMALL>
	</TD>
</TR>

<TR>
	<TD COLSPAN="2">
    <?php if ($f_action == 'Orig') { ?>
        <B>View of original (<?php print wordwrap(htmlspecialchars($curPreview->getType())); ?>) <?php print $curPreview->getTitle(); ?> (<A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?<?php print $getString; ?>">To return to the preview click here</a>)</B>    
    <?php } else { ?>
    	<B>Preview of <?php print wordwrap(htmlspecialchars(str_replace($curPreview->getType() .'_', '', $curPreview->getTitle())), 60, '<BR>'); ?> (<A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?f_action=Orig&<?php print $getString; ?>&f_orig_article=<?php print $articlesArray[$curPos]; ?>&f_orig_lang=<?php print $origLang; ?>">View the source (<?php print $src->getDisplayName(); ?>) version of <?php print wordwrap(htmlspecialchars(str_replace($curPreview->getType() .'_', '', $curPreview->getTitle())), 60, '<BR>'); ?></A>) 
    	<?php print $curPos + 1; ?> of <?php print count($articlesArray); ?>. 
        <?php 
        if (isset($articlesArray[$curPos - 1])) {
            $prevArticle = $articlesArray[$curPos - 1];
        ?>
        	<A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?<?php print $getString; ?>&f_cur_preview=<?php print $prevArticle; ?>"><IMG BORDER="0" SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/previous.png" BORDER="0"></a>&nbsp;
        <?php  
        }
        if (isset($articlesArray[$curPos + 1])) {
            $nextArticle = $articlesArray[$curPos + 1];
            ?>
            <A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?<?php print $getString; ?>&f_cur_preview=<?php print $nextArticle; ?>"><IMG BORDER="0" SRC="<?php echo $Campsite["ADMIN_IMAGE_BASE_URL"]; ?>/next.png" BORDER="0"></a>
        <?php
        }
    } // else
    ?>

    </TD>
</TR>
<TR>
	<TD COLSPAN="2">
	<TABLE BORDER="1">
    <tr>
	<td valign="top">
	<!-- BEGIN article content -->	
	<table>
	<TR>
		<TD style="padding-top: 3px;">
			<TABLE>
			<TR>
				<TD ALIGN="RIGHT" valign="top" ><b><?php  putGS("Name"); ?>:</b></TD>
				<TD align="left" valign="top">
				    <?php print wordwrap(htmlspecialchars(str_replace($curPreview->getType() .'_', '', $curPreview->getTitle())), 60, "<br>"); ?>
				</TD>
				<TD ALIGN="RIGHT" valign="top"><b><?php  putGS("Created by"); ?>:</b></TD>
				<TD align="left" valign="top"><?php p(htmlspecialchars($articleCreator->getRealName())); ?></TD>
				<TD ALIGN="RIGHT" valign="top"></TD>
				<TD align="left" valign="top" style="padding-top: 0.25em;">
				<?php  putGS('Show article on front page'); ?>
				</TD>
			</TR>
			<TR>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><?php  putGS("Type"); ?>:</b></TD>
				<TD align="left" valign="top">
					<?php print htmlspecialchars($dest->getDisplayName()); ?>
				</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><nobr><?php  putGS("Creation date"); ?>:</nobr></b></TD>
				<TD align="left" valign="top" nowrap>		
					<?php print $curPreview->getCreationDate(); ?>			
				</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"></TD>
				<TD align="left" valign="top"  style="padding-top: 0.25em;">
				<?php  putGS('Show article on section page'); ?>
				</TD>
			</TR>
			<TR>
			    <td align="right" valign="top" nowrap><b><?php putGS("Number"); ?>:</b></td>
			    <td align="left" valign="top"  style="padding-top: 2px; padding-left: 4px;"><?php p($curPreview->getArticleNumber()); ?> <?php if (isset($publicationObj) && $publicationObj->getUrlTypeId() == 2) { ?>
&nbsp;(<a href="/<?php echo $languageObj->getCode()."/".$issueObj->getUrlName()."/".$sectionObj->getUrlName()."/".$curPreview->getUrlName(); ?>"><?php putGS("Link to public page"); ?></a>)<?php } ?></td>

				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"><b><?php  putGS("Publish date"); ?>:</b></TD>
				<TD align="left" valign="top">
					<?php print htmlspecialchars($curPreview->getPublishDate()); ?>
				</TD>
				<TD ALIGN="RIGHT" valign="top" style="padding-left: 1em;"></TD>
				<TD align="left" valign="top" style="padding-top: 0.25em;">
				<?php putGS('Allow users without subscriptions to view the article'); ?>
				</TD>
			</TR>
			</TABLE>
		</TD>
	</TR>

	<TR>
		<TD style="border-top: 1px solid #8baed1; padding-top: 3px;">
			<TABLE>
			<TR>
				<td align="left" style="padding-right: 5px;">
				</td>
				<TD ALIGN="RIGHT" ><?php  putGS("Keywords"); ?>:</TD>
				<TD>
					<?php print htmlspecialchars($curPreview->getKeywords()); ?>
				</TD>
			</TR>

			<?php
			// Display the article type fields.
			foreach ($dbColumns as $dbColumn) {

				if (stristr($dbColumn->getType(), "char")
				    /* DO NOT DELETE */ || stristr($dbColumn->getType(), "binary") /* DO NOT DELETE */ ) {
					// The "binary" comparizon is needed for Fedora distro; MySQL on Fedora changes ALL
					// "char" types to "binary".

					// Single line text fields
			?>
			<TR>
				<td align="left" style="padding-right: 5px;">
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<TD>
				<?php print $articleData->getProperty($dbColumn->getName()); ?>  
				</TD>
			</TR>
			<?php
			} elseif (stristr($dbColumn->getType(), "date")) {
				// Date fields
				if ($articleData->getProperty($dbColumn->getName()) == "0000-00-00") {
					$articleData->setProperty($dbColumn->getName(), "CURDATE()", true, true);
				}
			?>
			<TR>
				<td align="left" style="padding-right: 5px;">
				</td>
				<td align="right">
					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
				</td>
				<TD>
					<span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;"><?php echo htmlspecialchars($articleData->getProperty($dbColumn->getName())); ?></span>
				<?php putGS('YYYY-MM-DD'); ?>
				</TD>
			</TR>
			<?php
			} elseif (stristr($dbColumn->getType(), "blob")) {
				// Multiline text fields
				// Transform Campsite-specific tags into editor-friendly tags.
				$text = $articleData->getProperty($dbColumn->getName());

				// Subheads
				$text = preg_replace("/<!\*\*\s*Title\s*>/i", "<span class=\"campsite_subhead\">", $text);
				$text = preg_replace("/<!\*\*\s*EndTitle\s*>/i", "</span>", $text);

				// Internal Links with targets
				$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*target\s*([\w_]*)\s*>/i", '<a href="campsite_internal_link?$1" target="$2">', $text);
				// Internal Links without targets
				$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*>/i", '<a href="campsite_internal_link?$1">', $text);
				// End link
				$text = preg_replace("/<!\*\*\s*EndLink\s*>/i", "</a>", $text);
				// Images
				preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*/i",$text, $imageMatches);
				if (isset($imageMatches[1][0])) {
					foreach ($imageMatches[1] as $templateId) {
						// Get the image URL
						$articleImage =& new ArticleImage($f_article_number, null, $templateId);
						$image =& new Image($articleImage->getImageId());
						$imageUrl = $image->getImageUrl();
						$text = preg_replace("/<!\*\*\s*Image\s*".$templateId."\s*/i", '<img src="'.$imageUrl.'" id="'.$templateId.'" ', $text);
					}
				}
			?>
			<TR>
			<TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 8px; padding-right: 5px;">
			</td>
			<td align="right" valign="top" style="padding-top: 8px;">
				<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
			</td>
			<TD align="left" valign="top">
				<table cellpadding="0" cellspacing="0" width="100%">
				<tr>
					<td align="left" style="padding: 5px; <?php if (!empty($text)) {?>border: 1px solid #888; margin-right: 5px;<?php } ?>" <?php if (!empty($text)) {?>bgcolor="#EEEEEE"<?php } ?>><?php p($text); ?></td>
				</tr>
				</table>
			</TD>
			</TR>
			<?php
			} elseif (stristr($dbColumn->getType(), "topic")) {
				$articleTypeField = new ArticleTypeField($curPreview->getType(),
														 substr($dbColumn->getName(), 1));
				$rootTopicId = $articleTypeField->getTopicTypeRootElement();
				$rootTopic = new Topic($rootTopicId);
				$subtopics = Topic::GetTree($rootTopicId);
				$articleTopicId = $articleData->getProperty($dbColumn->getName());
			?>
			<tr>
			<TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 8px; padding-right: 5px;">
			</td>
			<td align="right">
				<?php echo $articleTypeField->getDisplayName(); ?>:
			</td>
			<td>
			</td>
			</tr>
			<?php
			}
		} // foreach ($dbColumns as $dbColumn)
		?>
			</TABLE>
		</TD>
	</TR>
	</TABLE>
	<!-- END Article Content -->
    
    

	</TD></TR></TABLE>
	</TD>
</TR>

<TR>
	<TD>
	<INPUT TYPE="CHECKBOX" NAME="f_del_src">Delete the source article type (<?php print $src->getDisplayName(); ?>) when finished.
	</TD>
	<TD>
	<b>Clicking "Merge" will merge <?php print $src->getNumArticles(); ?> articles.</b>
	</TD>
<TR>	
	<TD COLSPAN="2">
	<DIV ALIGN="CENTER">
	
	<?php foreach ($dest->m_dbColumns as $destColumn) { ?>
	<INPUT TYPE="HIDDEN" NAME="f_src_<?php print $destColumn->getName(); ?>" VALUE="<?php print $f_src_c[$destColumn->getName()]; ?>">
	<?php } ?>

	<INPUT TYPE="HIDDEN" NAME="f_cur_preview" VALUE="<?php $curPreview->getArticleNumber(); ?>">
	
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Back to Step 2'); ?>">
	<INPUT TYPE="submit" class="button" NAME="Ok" VALUE="<?php  putGS('Merge!'); ?>">
	</DIV>
	</TD>
</TR>
</TABLE>
</FORM>
<P>

<?php
// delete the preview object
$AT =& new ArticleType($curPreview->getType());
$AT->delete();
$curPreview->delete();
?>

<?php camp_html_copyright_notice(); ?>