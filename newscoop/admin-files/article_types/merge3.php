<?php
camp_load_translation_strings("article_types");
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleType.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Input.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleImage.php');

// Check permissions
if (!$g_user->hasPermission('ManageArticleTypes')) {
	camp_html_display_error(getGS("You do not have the right to merge article types."));
	exit;
}

$f_src = trim(Input::get('f_src'));
$f_dest = trim(Input::get('f_dest'));

$f_prev_action = trim(Input::get('f_action', 'string', 'NULL')); // Preview actions: either NEXT, PREV, ORIG
$f_action = trim(Input::get('f_action')); // either Step1, Step2, Preview or Merge


if ($f_action == 'Step1') {
	camp_html_goto_page("/$ADMIN/article_types/merge.php?f_src=$f_src&f_dest=$f_dest");
}

$src = new ArticleType($f_src);
$dest = new ArticleType($f_dest);

$getString = '';
foreach ($dest->getUserDefinedColumns(null, true, true) as $destColumn) {
	$getString .= "&f_src_". $destColumn->getPrintName() ."=". trim(Input::get('f_src_'. $destColumn->getPrintName()));
}

if ($f_action == 'Step2') {
	camp_html_goto_page("/$ADMIN/article_types/merge2.php?f_src=$f_src&f_dest=$f_dest". $getString);
}

foreach ($dest->getUserDefinedColumns(null, true, true) as $destColumn) {
    $tmp = trim(Input::get('f_src_'. $destColumn->getPrintName()));
    if (empty($tmp)) {
    	$tmp = 'NULL';
    }
	$f_src_c[$destColumn->getPrintName()] = $tmp;
}


// Verify the merge rules
$ok = true;
$errMsgs = array();

foreach ($f_src_c as $destColumn => $srcColumn) {
	if ($srcColumn == 'NULL') {
		continue;
	}
	$destATF = new ArticleTypeField($f_dest, $destColumn);
	$srcATF = new ArticleTypeField($f_src, $srcColumn);

	if (!$destATF->isConvertibleFrom($srcATF)) {
        $errMsgs[] = getGS('Cannot merge a $1 field ($2) into a $3 field ($4).',
                            getGS($srcATF->getType()), $srcATF->getDisplayName(),
                            getGS($destATF->getType()), $destATF->getDisplayName());
        $ok = false;
	}
}

//
// if f_action is Merge, do the merge and return them to article_types/ screen (or an error)
//
if ($ok && $f_action == 'Merge') {
	if (!SecurityToken::isValid()) {
		camp_html_display_error(getGS('Invalid security token!'));
		exit;
	}

	$res = ArticleType::merge($f_src, $f_dest, $f_src_c);
    if (!$res) {
        $errMsgs[] = getGS("Merge failed.");
        $ok = false;
    }
    if ($ok) {
    	$f_delete = Input::get('f_delete', 'checkbox', 0);
        if ($f_delete) {
            // delete the source article type
            $at = new ArticleType($f_src);
            $at->delete();
        }

        camp_html_goto_page("/$ADMIN/article_types/");
        exit(0);
    }
}


//
// Otherwise, we are in preview mode, so render up a preview
//
if ($ok) {
    //
    // calculate where this article is in relation to all the articles of the src type
    //
    $articlesArray = $src->getArticlesArray();
    if (!count($articlesArray)) {
        $errMsgs[] = getGS("No articles.");
        $ok = false;
    }
    if ($ok) {
        $f_cur_preview = trim(Input::get('f_cur_preview', 'int', $articlesArray[0])); // The currently previewed article
        $tmp = array_keys($articlesArray, $f_cur_preview);
        $curPos = $tmp[0]; // used for calculating the next / prev arrows

        // calculate the first language of an article number
        // and also the number of translations associated with an article number
        global $g_ado_db;
        $sql = "SELECT * FROM X$f_src WHERE NrArticle=$f_cur_preview";
        $rows = $g_ado_db->GetAll($sql);
        if (!count($rows)) {
            $errMsgs[] = getGS('There is no article associated with the preview.');
            $ok = false;
        }
    }

    if ($ok) {
        $numberOfTranslations = count($rows);
        $firstLanguage = $rows[0]['IdLanguage'];
        $curPreview = new Article($firstLanguage, $f_cur_preview);
        $articleCreator = new User($curPreview->getCreatorId());
        $articleData = $dest->getPreviewArticleData();
        $dbColumns = $articleData->getUserDefinedColumns(1, true);
        $srcArticleData = $curPreview->getArticleData();
        $srcDbColumns = $srcArticleData->getUserDefinedColumns(1, true);
        $getString = '';
        foreach ($_GET as $k => $v) {
            if ( ($k != 'f_action') && ($k != 'f_preview_action') ) {
                $getString .= "&$k=$v";
            }
        }
        foreach ($_POST as $k => $v) {
            if ( ($k != 'f_action') && ($k != 'f_prev_action') ) {
                $getString .= "&$k=$v";
            }
        }
        $getString = substr($getString, 1);

        $crumbs = array();
        $crumbs[] = array(getGS("Configure"), "");
        $crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
        $crumbs[] = array(getGS("Merge article type"), "");
        echo camp_html_breadcrumbs($crumbs);

        ?>
        <P>
        <FORM NAME="dialog" METHOD="POST" ACTION="/<?php echo $ADMIN; ?>/article_types/merge3.php?f_src=<?php print $f_src; ?>&f_dest=<?php print $f_dest; ?>">
		<?php echo SecurityToken::FormParameter(); ?>
        <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="0" CLASS="box_table">
        <TR>
        	<TD COLSPAN="2">
        		<b><?php putGS("Merge Article Types: Step $1 of $2", "3", "3"); ?></b>
				<HR NOSHADE SIZE="1" COLOR="BLACK">
        	</TD>
        </TR>
        <TR>
        	<TD COLSPAN="2">
        		<b><?php putGS("Merge configuration for merging $1 into $2.", $src->getDisplayName(), $dest->getDisplayName()); ?></b>
        	<BR>
        	<UL>
        	<?php
        	foreach ($f_src_c as $destColumn => $srcColumn) {
        		$tmp = array_keys($f_src_c, $srcColumn);

        		if ($srcColumn == 'NULL') {
        			?>
        			<LI><FONT COLOR="TAN"><?php putGS("Merge $1 into $2", "<b>".getGS("NOTHING")."</b>", "<b>". $destColumn ."</b>"); ?> <?php putGS("(Null merge warning.)"); ?></FONT></LI>
        			<?php
        		} else if (count($tmp) > 1) {
        			?>
        			<LI><FONT COLOR="TAN"><?php putGS("Merge $1 into $2", "<b>".$srcColumn."</b>", "<b>". $destColumn ."</b>"); ?> <?php putGS("(Duplicate warning.)"); ?></FONT></LI>
        			<?php
        		} else {
        			?>
        			<LI><FONT COLOR="GREEN"><?php putGS("Merge $1 into $2", "<b>".$srcColumn."</b>", "<b>". $destColumn ."</b>"); ?></FONT></LI>
        			<?php
        		}
        	}

        	// display the warning in red if the user select NONE
        	foreach ($src->getUserDefinedColumns(null, true, true) as $srcColumn) {
        		if (array_search($srcColumn->getPrintName(), $f_src_c) === false) {
        			?><LI><FONT COLOR="RED"><?php putGS("(!) Do NOT merge $1", "<B>". $srcColumn->getPrintName() ."</B>"); ?> <?php putGS("(No merge warning.)"); ?></FONT></LI><?php
        		}
        	} ?>
        	</UL>
        	</TD>

        </TR>
        <TR>
        	<TD COLSPAN="2">
        	<B><?php putGS("Preview a sample of the merge configuration."); ?></B> <SMALL><?php putGS("Cycle through your articles to verify that the merge configuration is correct."); ?></SMALL>
        	</TD>
        </TR>

        <TR>
        	<TD COLSPAN="2">
            <?php if ($f_prev_action == 'Orig') { ?>
                <B><?php putGS("View of original ($1) $2", htmlspecialchars($curPreview->getType()), $curPreview->getTitle()); ?>
                (<A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?<?php print $getString; ?>">
                <?php putGS("To return to the preview click here"); ?></a>)</B>
            <?php } else { ?>
            	<B><?php putGS("Preview of $1", wordwrap(htmlspecialchars($curPreview->getTitle()), 60, '<BR>')); ?>
            	   (<A HREF="/<?php print $ADMIN; ?>/article_types/merge3.php?f_action=Orig&<?php print $getString; ?>"><?php putGS("View the source ($1) version of $2", $src->getDisplayName(), wordwrap(htmlspecialchars($curPreview->getTitle()), 60, '<BR>')); ?></A>)
            	<?php putGS("$1 of $2", $curPos + 1, count($articlesArray)); ?>.
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
            <BR><?php putGS("This is the first translation of $1", $numberOfTranslations); ?>

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
        			$i = 0;
        			if ($f_prev_action == 'Orig') $dbColumns = $srcDbColumns;
        			foreach ($dbColumns as $dbColumn) {
                        if ($f_prev_action == 'Orig') {
                            $text = $srcArticleData->getProperty($dbColumn->getName());
                        } elseif ($f_src_c[$dbColumn->getPrintName()] != 'NULL') {
            				$text = $srcArticleData->getProperty('F'. $f_src_c[$dbColumn->getPrintName()]);
                        } else {
                            $text = '';
                        }

        				if ($dbColumn->getType() == ArticleTypeField::TYPE_TEXT) {
        					// Single line text fields
        			?>
        			<TR>
        				<td align="left" style="padding-right: 5px;">
        				</td>
        				<td align="right">
        					<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
        				</td>
        				<TD>
        				<?php print htmlspecialchars($text); ?>
        				</TD>
        			</TR>
        			<?php
        			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_DATE) {
        				// Date fields
        			?>
        			<TR>
        				<td align="left" style="padding-right: 5px;">
        				</td>
        				<td align="right">
        					<?php
                            echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
        				</td>
        				<TD>
        					<span style="padding-left: 4px; padding-right: 4px; padding-top: 1px; padding-bottom: 1px; border: 1px solid #888; margin-right: 5px; background-color: #EEEEEE;">
        					<?php echo htmlspecialchars($text); ?>
        					</span>
        				<?php putGS('YYYY-MM-DD'); ?>
        				</TD>
        			</TR>
        			<?php
        			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
        				// Multiline text fields
        				// Transform Campsite-specific tags into editor-friendly tags.
        				// Subheads
        				$text = preg_replace("/<!\*\*\s*Title\s*>/i", "<span class=\"campsite_subhead\">", $text);
        				$text = preg_replace("/<!\*\*\s*EndTitle\s*>/i", "</span>", $text);

        				// Internal Links with targets
        				$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*target\s*([\w_]*)\s*>/i", '<a href="/campsite/campsite1_internal_link?$1" target="$2">', $text);
        				// Internal Links without targets
        				$text = preg_replace("/<!\*\*\s*Link\s*Internal\s*([\w=&]*)\s*>/i", '<a href="/campsite/campsite1_internal_link?$1">', $text);
        				// End link
        				$text = preg_replace("/<!\*\*\s*EndLink\s*>/i", "</a>", $text);
        				// Images
        				preg_match_all("/<!\*\*\s*Image\s*([\d]*)\s*/i",$text, $imageMatches);
        				if (isset($imageMatches[1][0])) {
        					foreach ($imageMatches[1] as $templateId) {
        						// Get the image URL
        						$articleImage = new ArticleImage($srcArticleData->getProperty('NrArticle'), null, $templateId);
        						$image = new Image($articleImage->getImageId());
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
        			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_TOPIC) {
        			?>
        			<tr>
        			<TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 8px; padding-right: 5px;">
        			</td>
        			<td align="right">
        				<?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:
        			</td>
        			<td>
                        <?php
                        $topic = new Topic((int)$text);
                        echo $topic->getName(camp_session_get('LoginLanguageId', 1));
                        ?>
        			</td>
        			</tr>
        			<?php
        			} elseif ($dbColumn->getType() == ArticleTypeField::TYPE_SWITCH) {
                        $checked = $srcArticleData->getFieldValue($dbColumn->getPrintName()) ? 'checked' : '';
                    ?>
                    <tr>
                    <TD ALIGN="RIGHT" VALIGN="TOP" style="padding-top: 8px; padding-right: 5px;">
                    </td>
                    <td align="right"><?php echo htmlspecialchars($dbColumn->getDisplayName()); ?>:</td>
                    <td>
                    <input type="checkbox" <?php echo $checked; ?> class="input_checkbox" name="<?php echo $dbColumn->getName(); ?>" id="<?php echo $dbColumn->getName(); ?>" disabled>
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
        	<INPUT TYPE="CHECKBOX" NAME="f_delete"><?php putGS("Delete the source article type ($1) when finished.", $src->getDisplayName()); ?>
        	</TD>
        	<TD>
        	<b><?php putGS('Clicking "Merge" will merge ($1) article(s).', $src->getNumArticles()); ?></b>
        	</TD>
        <TR>
        	<TD COLSPAN="2">
        	<DIV ALIGN="CENTER">

        	<?php foreach ($dest->getUserDefinedColumns(null, true, true) as $destColumn) { ?>
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
} // end if ok
if (!$ok) {
    $crumbs = array();
    $crumbs[] = array(getGS("Configure"), "");
    $crumbs[] = array(getGS("Article Types"), "/$ADMIN/article_types/");
    $crumbs[] = array(getGS("Merge article type"), "");

    echo camp_html_breadcrumbs($crumbs);

    ?>
    <P>
    <TABLE BORDER="0" CELLSPACING="0" CELLPADDING="8" class="message_box">
    <TR>
    	<TD COLSPAN="2">
    		<B> <?php  putGS("Merge article type"); ?> </B>
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

} ?>
