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

$f_prev_action = trim(Input::get('f_action', 'string', 'NULL')); // Preview actions: either NEXT, PREV, ORIG
$f_action = trim(Input::get('f_action')); // either Step1, Step2, Preview or Merge


if ($f_action == 'Step1') {
	header("Location: /$ADMIN/article_types/merge.php?f_src=$f_src&f_dest=$f_dest");
	exit;
}	

$src =& new ArticleType($f_src);
$dest =& new ArticleType($f_dest);

$getString = '';
foreach ($dest->m_dbColumns as $destColumn) {
	$getString .= "&f_src_". $destColumn->getPrintName() ."=". trim(Input::get('f_src_'. $destColumn->getPrintName()));
}

if ($f_action == 'Step2') {
	header("Location: /$ADMIN/article_types/merge2.php?f_src=$f_src&f_dest=$f_dest". $getString);
	exit;
}	

foreach ($dest->m_dbColumns as $destColumn) {
    $tmp = trim(Input::get('f_src_'. $destColumn->getPrintName())); 
	$f_src_c[$destColumn->getPrintName()] = $tmp;
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
// Topic->Topic = NO

$ok = true;
$errMsgs = array();

foreach ($f_src_c as $destColumn => $srcColumn) {
	$destATF =& new ArticleTypeField($f_dest, $destColumn);
	$srcATF =& new ArticleTypeField($f_src, $srcColumn);
    $tmp = $srcATF->getType();
    $tmp2 = $destATF->getType();
    
	if (stristr($srcATF->getType(), 'blob') && stristr($destATF->getType(), 'char')) {
		$errMsgs[] = 'Cannot merge a body ('. $srcATF->getDisplayName() .') into a text ('. $destATF->getDisplayName() .').';
		$ok = false;
	}
	if ((stristr($srcATF->getType(), 'char') || stristr($srcATF->getType(), 'blob') || stristr($srcATF->getType(), 'topic')) && stristr($destATF->getType(), 'date')) {
		$errMsgs[] = 'Cannot merge a '. $srcATF->getType() .' ('. $srcATF->getPrintName() .') into a date ('. $destATF->getDisplayName() .').';
		$ok = false;
	}
	if ((stristr($srcATF->getType(), 'topic') || stristr($srcATF->getType(), 'char') || stristr($srcATF->getType(), 'blob') || stristr($srcATF->getType(), 'date')) && stristr($destATF->getType(), 'topic')) {
		$errMsgs[] = 'Cannot merge a '. $srcATF->getType() .' ('. $srcATF->getPrintName() .') into a topic ('. $destATF->getDisplayName() .').';
		$ok = false;
	}

}

if (!$ok) {
    $crumbs = array();
    $crumbs[] = array(getGS("Configure"), "");
    $crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
    $crumbs[] = array(getGS("Renaiming article type"), "");
    
    echo camp_html_breadcrumbs($crumbs);
    
    ?>
    <P>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
    <TR>
    	<TD COLSPAN="2">
    		<B> <?php  putGS("Merging article type"); ?> </B>
    		<HR NOSHADE SIZE="1" COLOR="BLACK">
    	</TD>
    </TR>
    <TR>
    	<TD COLSPAN="2">
    		<BLOCKQUOTE>
    		<?php 
    		foreach ($errMsgs as $errorMsg) { 
    			echo "<li>".$errorMsg."</li>";
    		}
    		?>
    		</BLOCKQUOTE>
    	</TD>
    </TR>
    <TR>
    	<TD COLSPAN="2">
    	<DIV ALIGN="CENTER">
    	<INPUT TYPE="button" class="button" NAME="OK" VALUE="<?php  putGS('OK'); ?>" ONCLICK="location.href='/<?php p($ADMIN); ?>/article_types/merge2.php?f_src=<?php echo $f_src; ?>&f_dest=<?php echo $f_dest . $getString ?>'">
    	</DIV>
    	</TD>
    </TR>
    </TABLE>
    <P>
    
    <?php echo camp_html_copyright_notice(); ?>
    <?php           
  
}


if ($ok && $f_action == 'Merge') {
	$res = ArticleType::merge($f_src, $f_dest, $f_src_c);
    $f_delete = Input::get('f_delete', 'int', 0);
    if ($f_delete) {
	// delete the source TODO
    }
	header("Location: /$ADMIN/article_types/");
	exit;	
}


if ($ok) {
    
    
    //
    // otherwise, do the preview
    //
    $articlesArray = $src->getArticlesArray();
    $f_cur_preview = trim(Input::get('f_cur_preview', 'int', $articlesArray[0])); // The currently previewed article
    $tmp = array_keys($articlesArray, $f_cur_preview);	
    $curPos = $tmp[0];
    
    
    // this only grabs the first language associated with an ArticleNumber
    // for preview purposes
    global $g_ado_db;
    $ok = true;
    	$sql = "SELECT * FROM X$f_src WHERE NrArticle=$f_cur_preview";		    
    $rows = $g_ado_db->GetAll($sql);
    if (!count($rows)) {
      $errMessages[] = 'There is no article associated with the preview.';
      $ok = false;
          
    }       
    if ($ok) {
        $numberOfTranslations = count($rows);
        $firstLanguage = $rows[0]['IdLanguage'];
        $curPreview =& new Article($firstLanguage, $f_cur_preview);
        $articleCreator =& new User($curPreview->getCreatorId());
        //$articleData = ArticleType::__getPreviewData($curPreview, $prevTable, $f_src_c);
        // ensure that the destination has atleast one article in it, if not, populate it with
        // a dummy article 
        $articleData = $dest->getPreviewArticleData();
        $dbColumns = $articleData->getUserDefinedColumns(1);
        $srcArticleData = $curPreview->getArticleData();
        $srcDbColumns = $srcArticleData->getUserDefinedColumns(1);      
    
        
    }
    
    if (!$ok) {
        print "ERROR";
        exit;
    }
    
    
    
    $getString = '';
    foreach ($_GET as $k => $v) {
        if ($k != 'f_action' && $k != 'f_preview_action')
            $getString .= "&$k=$v";        
    }
    foreach ($_POST as $k => $v) {
        if ($k != 'f_action' && $k != 'f_prev_action')
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
    
    		if ($srcColumn == 'NULL') { 
    			print "<LI><FONT COLOR=\"TAN\">Merge <b>NOTHING</b> into <b>". $destColumn ."</b> (Null merge warning.).</FONT></LI>";
    		} else if (count($tmp) > 1) {
    			print "<LI><FONT COLOR=\"TAN\">Merge <b>$srcColumn</b> into <b>". $destColumn ."</b></FONT> (Duplicate warning.)</FONT></LI>";
    		} else {
    			print "<LI><FONT COLOR=\"GREEN\">Merge <b>$srcColumn</b> into <b>". $destColumn ."</b>.</FONT></LI>";
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
        <?php if ($f_prev_action == 'Orig') { ?>
            <B>View of original (<?php print htmlspecialchars($curPreview->getType()); ?>) <?php print $curPreview->getTitle(); ?> (<A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?<?php print $getString; ?>">To return to the preview click here</a>)</B>    
        <?php } else { ?>
        	<B>Preview of <?php print wordwrap(htmlspecialchars($curPreview->getTitle()), 60, '<BR>'); ?> 
        	   (<A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?f_action=Orig&<?php print $getString; ?>">View the source (<?php print $src->getDisplayName(); ?>) version of <?php print wordwrap(htmlspecialchars($curPreview->getTitle()), 60, '<BR>'); ?></A>) 
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
        <BR>This is the first translation of <?php print $numberOfTranslations; ?>
        
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
    				    <?php print wordwrap(htmlspecialchars($curPreview->getTitle()), 60, "<br>"); ?>
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
    				<?php 
    				if ($f_prev_action == 'Orig')
    				    print htmlspecialchars($articleData->getProperty($dbColumn->getName()));
    				else if ($f_src_c[$dbColumn->getPrintName()] != 'NULL')	
    	       			print htmlspecialchars($srcArticleData->getProperty('F'. $f_src_c[$dbColumn->getPrintName()]));	
    	       		else 
    	       		    print '';		
    				?>  
    				</TD>
    			</TR>
    			<?php
    			} elseif (stristr($dbColumn->getType(), "date")) {
    				// Date fields
    				//if ($srcArticleData->getProperty($f_src_c[$dbColumn->getPrintName()]) == "0000-00-00") {
    				//	$articleData->setProperty($dbColumn->getName(), "CURDATE()", false, true);
    				//}
    			?>
    			<TR>
    				<td align="left" style="padding-right: 5px;">
    				</td>
    				<td align="right">
    					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
    				</td>
    				<TD>
    					<span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;">
    					<?php 
    					if ($f_prev_action == 'Orig')
    					   echo htmlspecialchars($articleData->getProperty($dbColumn->getName()));	   
    					else if ($srcArticleData->getProperty($f_src_c[$dbColumn->getPrintName()]) != 'NULL')
        					echo htmlspecialchars($srcArticleData->getProperty('F'. $f_src_c[$dbColumn->getPrintName()])); 					
                        else 
                            echo '';
        				?>					
    					</span>
    				<?php putGS('YYYY-MM-DD'); ?>
    				</TD>
    			</TR>
    			<?php
    			} elseif (stristr($dbColumn->getType(), "blob")) {
    				// Multiline text fields
    				// Transform Campsite-specific tags into editor-friendly tags.
                    if ($f_prev_action == 'Orig')
                        $text = $articleData->getProperty($dbColumn->getName());
                    else if ($f_src_c[$dbColumn->getPrintName()] != 'NULL')
        				$text = $srcArticleData->getProperty('F'. $f_src_c[$dbColumn->getPrintName()]);
                    else    
                        $text = '';
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
    			?>
    			<tr>
    			<TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 8px; padding-right: 5px;">
    			</td>
    			<td align="right">
    				<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
    			</td>
    			<td>
    			    <?php echo ''; ?>
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
    	<INPUT TYPE="HIDDEN" NAME="f_src_<?php print $destColumn->getPrintName(); ?>" VALUE="<?php print $f_src_c[$destColumn->getPrintName()]; ?>">
    	<?php } ?>
    
    	<INPUT TYPE="HIDDEN" NAME="f_cur_preview" VALUE="<?php $curPreview->getArticleNumber(); ?>">
    	<INPUT TYPE="HIDDEN" NAME="f_action" VALUE="">
    	<INPUT TYPE="submit" class="button" NAME="Ok" ONCLICK="dialog.f_action.value='Step2'" VALUE="<?php  putGS('Back to Step 2'); ?>">
    	<INPUT TYPE="submit" class="button" NAME="Ok" ONCLICK="dialog.f_action.value='Merge'" VALUE="<?php  putGS('Merge!'); ?>">
    	</DIV>
    	</TD>
    </TR>
    </TABLE>
    </FORM>
    <P>
    
    <?php camp_html_copyright_notice(); ?>
<?php
} // end if ok
?>