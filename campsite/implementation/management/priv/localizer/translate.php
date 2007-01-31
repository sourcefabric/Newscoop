<?php

/**
 * Display a drop-down list of languages.
 * @param array p_languageMetadata
 * @param string p_selectedValue
 * @return string
 *      HTML string of <options>.
 */
function LanguageMenu($p_languageMetadata, $p_selectedValue)
{
	$options = '';
    foreach($p_languageMetadata as $language) {
        if ($p_selectedValue == $language->getLanguageId()) {
            $selectedString = 'selected';
        }
        else {
            $selectedString = '';
        }
        $options .= '<option value="'.$language->getLanguageId().'" '.$selectedString.'>'.$language->getNativeName().'</option>';
    }
    return $options;
} // fn LanguageMenu


/**
 * Creates a form for translation.
 * @param array $p_request
 */
function translationForm($p_request)
{
    global $g_localizerConfig;
	$localizerTargetLanguage = camp_session_get('localizer_target_language', $g_localizerConfig['DEFAULT_LANGUAGE']);
	$localizerSourceLanguage = camp_session_get('localizer_source_language', '');
	if (empty($localizerSourceLanguage)) {
		if (isset($p_request['TOL_Language'])) {
			$lang = $p_request['TOL_Language'];
		} else {
			$lang = $g_localizerConfig['DEFAULT_LANGUAGE'];
		}
		$tmpLanguage =& new LocalizerLanguage(null, $lang);
		$localizerSourceLanguage = $tmpLanguage->getLanguageId();
	}

	$prefix = camp_session_get('prefix', '');
	$screenDropDownSelection = $prefix;

	// Load the language files.
	//echo "Prefix: $prefix<br>";
	$sourceLang =& new LocalizerLanguage($prefix, $localizerSourceLanguage);
	$targetLang =& new LocalizerLanguage($prefix, $localizerTargetLanguage);
	$defaultLang =& new LocalizerLanguage($prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);

	$mode = Localizer::GetMode();
	if (!empty($prefix)) {
    	// If the language files do not exist, create them.
        if (!$defaultLang->loadFile($mode)) {
        	$defaultLang->saveFile($mode);
        }
    	if (!$sourceLang->loadFile($mode)) {
    		$sourceLang->saveFile($mode);
    	}
    	if (!$targetLang->loadFile($mode)) {
    		$targetLang->saveFile($mode);
        }

        // Make sure that the languages have the same strings and are in the same
        // order as the default language file.
        $modified = $sourceLang->syncToDefault();
        if ($modified) {
        	$sourceSaveSuccess = $sourceLang->saveFile($mode);
        	camp_html_add_msg(camp_get_error_message($sourceSaveSuccess));
        }
        $modified = $targetLang->syncToDefault();
        if ($modified) {
        	$targetSaveSuccess = $targetLang->saveFile($mode);
        	camp_html_add_msg(camp_get_error_message($targetSaveSuccess));
        }
	}


    $defaultStrings = $defaultLang->getTranslationTable();
    $searchString = camp_session_get('search_string', '');
    if (!empty($searchString)) {
    	$sourceStrings = $sourceLang->search($searchString);
    }
    else {
    	$sourceStrings = $sourceLang->getTranslationTable();
    }
	$targetStrings = $targetLang->getTranslationTable();
	$languages = Localizer::GetAllLanguages($mode);


	$missingStrings = Localizer::FindMissingStrings($prefix);
	$unusedStrings = Localizer::FindUnusedStrings($prefix);

	// Mapping of language prefixes to human-readable strings.
    $mapPrefixToDisplay = array();
    $mapPrefixToDisplay[] = "";
    $mapPrefixToDisplay["globals"] = getGS("Globals");
    $mapPrefixToDisplay["home"] = getGS("Home");
    $mapPrefixToDisplay["api"] = getGS("API");
    $mapPrefixToDisplay["pub"] = getGS("Publications");
    $mapPrefixToDisplay["issues"] = getGS("Issues");
    $mapPrefixToDisplay["sections"] = getGS("Sections");
    $mapPrefixToDisplay["articles"] = getGS("Articles");
    $mapPrefixToDisplay["article_images"] = getGS("Article Images");
    $mapPrefixToDisplay["article_files"] = getGS("Article Files");
    $mapPrefixToDisplay["article_topics"] = getGS("Article Topics");
    $mapPrefixToDisplay["article_comments"] = getGS("Article Comments");
    $mapPrefixToDisplay["imagearchive"] = getGS("Image Archive");
    $mapPrefixToDisplay["comments"] = getGS("Comments");
    $mapPrefixToDisplay["system_pref"] = getGS("System Preferences");
    $mapPrefixToDisplay["templates"] = getGS("Templates");
    $mapPrefixToDisplay["article_types"] = getGS("Article Types");
    $mapPrefixToDisplay["article_type_fields"] = getGS("Article Type Fields");
    $mapPrefixToDisplay["topics"] = getGS("Topics");
    $mapPrefixToDisplay["languages"] = getGS("Languages");
    $mapPrefixToDisplay["country"] = getGS("Countries");
    $mapPrefixToDisplay["localizer"] = getGS("Localizer");
    $mapPrefixToDisplay["logs"] = getGS("Logs");
    $mapPrefixToDisplay["users"] = getGS("Users");
    $mapPrefixToDisplay["user_subscriptions"] = getGS("User Subscriptions");
    $mapPrefixToDisplay["user_subscription_sections"] = getGS("User Subscriptions Sections");
    $mapPrefixToDisplay["user_types"] = getGS("Staff User Types");
    $mapPrefixToDisplay["bug_reporting"] = getGS("Bug Reporting");
    $mapPrefixToDisplay["feedback"] = getGS("Feedback");
	// Whether to show translated strings or not.
	$hideTranslated = camp_session_get('hide_translated', 'off');

    camp_html_display_msgs();
	?>
	<table>
	<tr>
		<td valign="top"> <!-- Begin top control panel -->

		<table border="0" style="background-color: #d5e2ee; border: 1px solid #8baed1; margin-left: 10px; margin-top: 5px;" width="700px;">
		<form action="index.php" method="post">
	    <input type="hidden" name="localizer_lang_id" value="<?php echo $targetLang->getLanguageId(); ?>">
	    <input type="hidden" name="search_string" value="<?php echo htmlspecialchars($searchString); ?>">
		<tr>
			<td>
				<table>
				<tr>
					<td>
						<?php putGS('Screen:'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<SELECT name="prefix" class="input_select" onchange="this.form.submit();">
						<?PHP
						foreach ($mapPrefixToDisplay as $prefix => $displayStr) {
							camp_html_select_option($prefix, $screenDropDownSelection, $displayStr);
						}
						?>
						</SELECT>
					</td>
				</tr>
				</table>
			</td>

			<td>
				<table>
				<tr>
					<td>
						<?php putGS('Translate from:'); ?>
					</td>
				</tr>
				<tr>
					<td>
		        		<SELECT NAME="localizer_source_language" onchange="this.form.submit();" class="input_select">
		        		<?php echo LanguageMenu($languages, $localizerSourceLanguage); ?>
		        		</select>
					</td>
				</tr>
				</table>
			</td>

			<td>
				<table>
				<tr>
					<td>
						<?php putGS('Translate to:'); ?>
					</td>
				</tr>
				<tr>
					<td>
				        <SELECT NAME="localizer_target_language" onChange="this.form.submit();" class="input_select">
				    	<?php echo LanguageMenu($languages, $localizerTargetLanguage); ?>
				        </select>
					</td>
				</tr>
				</table>
			</td>

		</tr>
		<tr>
			<td align="center" colspan="3">
				<table>
				<tr>
					<td>
						<select name="hide_translated" onChange="this.form.submit();" class="input_select">
						<?php camp_html_select_option('off', $hideTranslated, getGS('Show translated strings')); ?>
						<?php camp_html_select_option('on', $hideTranslated, getGS('Hide translated strings')); ?>
						</select>
					</td>

					<td style="padding-left: 10px;">
						<INPUT type="submit" value="<?php putGS("Submit"); ?>" class="button">
					</td>
				</tr>
				</table>
			</td>
		</tr>
        </form>
		</table>

		</td><!-- End top controls -->
	</tr>

	<!-- Begin search dialog -->
	<tr>
		<td valign="top">
			<table border="0" style="background-color: #FAEFFF; border: 1px solid black; margin-left: 10px;" width="700px;" align="center">
			<form>
	        <input type="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
	        <input type="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
	        <input type="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
			<tr>
				<td width="1%" style="padding-left: 5px;">
					<img src="<?php echo $g_localizerConfig['ICONS_DIR']; ?>/preview.png">
				</td>

				<td style="padding-left: 10px;">
					<input type="text" name="search_string" value="<?php echo $searchString; ?>" class="input_text" size="50">
				</td>

				<td width="1%" nowrap>
					<input type="button" value="<?php putGS("Search"); ?>" onclick="this.form.submit();" class="button">
				</td>
			</tr>
			</form>
			</table>
		</td>
	</tr>

	<!-- Begin Missing and Unused Strings popups -->
	<tr>
		<td valign="top">

	<?PHP
	if ((count($missingStrings) > 0)  && ($screenDropDownSelection != 'globals')) {
		?>
		<table align="center" style="background-color: #EDFFDF; border: 1px solid #357654; margin-left: 10px; margin-bottom: 5px;" width="700px">
        <form action="do_add_missing_strings.php" method="post">
        <input type="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
        <input type="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
        <input type="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
		<tr>
			<td>
				<img src="<?php echo $g_localizerConfig['ICONS_DIR']; ?>/add.png">
			</td>

			<td>
				<?php putGS("The following strings are missing from the translation files:"); ?>
				<div style="overflow: auto; height: 50px; background-color: #EEEEEE; border: 1px solid black; padding-left: 3px;">
				<?PHP
				foreach ($missingStrings as $missingString) {
					echo htmlspecialchars($missingString)."<br>";
				}
				?>
				</div>
			</td>

			<td>
		        <input type="submit" value="<?php putGS("Add"); ?>" class="button">
			</td>
		</tr>
		</form>
		</table>
		<?php
	}

	if ((count($unusedStrings) > 0) && ($screenDropDownSelection != 'globals')) {
		?>
		<table style="background-color: #FFE0DF; border: 1px solid #C51325; margin-top: 3px; margin-left: 10px; margin-bottom: 5px;" width="700px">
        <form action="do_delete_unused_strings.php" method="post">
        <input type="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
        <input type="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
        <input type="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
		<tr>
			<td>
				<img src="<?php echo $g_localizerConfig['ICONS_DIR']; ?>/delete.png">
			</td>

			<td>
				<?php putGS("The following strings are not used:"); ?>
				<div style="overflow: auto; height: 50px; background-color: #EEEEEE; border: 1px solid black; padding-left: 3px;">
				<?PHP
				foreach ($unusedStrings as $unusedString) {
					echo htmlspecialchars($unusedString)."<br>";
				}
				?>
				</div>
			</td>

			<td>
		        <input type="submit" value="<?php putGS("Delete"); ?>" class="button">
			</td>
		</tr>
		</form>
		</table>
		<?php
	}
	?>
	<!-- Begin translated strings box -->
	<table border="0" class="table_input" style="padding-left: 10px; padding-bottom: 10px; margin-left: 10px;" width="700px">
	<form action="do_save.php" method="post">
    <INPUT TYPE="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
    <INPUT TYPE="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
    <INPUT TYPE="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
    <INPUT TYPE="hidden" name="search_string" value="<?php echo $searchString; ?>">
	<?PHP
	$foundUntranslatedString = false;
	$count = 0;
	foreach ($sourceStrings as $sourceKey => $sourceValue) {
	    if (!empty($targetStrings[$sourceKey])) {
	        $targetValueDisplay = str_replace('"', '&#34;', $targetStrings[$sourceKey]);
	        $targetValueDisplay = str_replace("\\", "\\\\", $targetValueDisplay);
	        $pre  = '';
	        $post = '';
	    } else {
	        $targetValueDisplay = '';
	        $pre    = '<FONT COLOR="red">';
	        $post   = '</FONT>';
	    }

		$sourceKeyDisplay = htmlspecialchars(str_replace("\\", "\\\\", $sourceKey));

		// Dont display translated strings
	    if ($hideTranslated == 'on' && !empty($targetStrings[$sourceKey])) {
	    	?>
	        <input name="data[<?php echo $count; ?>][key]" type="hidden" value="<?php echo $sourceKeyDisplay; ?>">
	        <input name="data[<?php echo $count; ?>][value]" type="hidden" value="<?php echo $targetValueDisplay; ?>">
	        <?php
	    }
	    else {
	    	// Display the interface for translating a string.

	    	$foundUntranslatedString = true;
	    	// Display string
	    	?>
	        <tr>
	        	<td style="padding-top: 7px;" width="500px">
				<?php
            	// If the string exists in the source language, display that
	            if (!empty($sourceValue)) {
	            	?>
	                <b><?php echo $sourceLang->getLanguageId(); ?>:</b> <?php echo $pre.htmlspecialchars(str_replace("\\", "\\\\", $sourceValue)).$post; ?>
	                <?php
	            }
	            // Otherwise, display it in the default language.
	            else {
	            	?>
	                <b><?php echo $g_localizerConfig['DEFAULT_LANGUAGE']; ?>:</b> <?php echo $pre.$defaultStrings[$sourceKey].$post; ?>
	                <?php
	            }
				?>
				</td>
			</tr>
			<tr>
				<td>
			        <input name="data[<?php echo $count; ?>][key]" type="hidden" value="<?php echo $sourceKeyDisplay; ?>">
			        <table cellpadding="0" cellspacing="0">
			        <tr>
			             <td style="padding-right: 5px;">
					       <input type="image" src="<?php echo $g_localizerConfig['ICONS_DIR']; ?>/save.png" name="save" value="save">
					     </td>
					     <td>
			                 <input name="data[<?php echo $count; ?>][value]" type="text" size="<?php echo $g_localizerConfig['INPUT_SIZE']; ?>" value="<?php echo $targetValueDisplay; ?>" class="input_text">
			             </td>

			   			<?php
            			// default language => can change keys
            	        if ($targetLang->getLanguageId() == $g_localizerConfig['DEFAULT_LANGUAGE']) {
            	            $fileparms = "localizer_target_language=".$targetLang->getLanguageId()
            	           		."&localizer_source_language=".$sourceLang->getLanguageId()
            	            	."&prefix=".urlencode($screenDropDownSelection)
            	            	."&search_string=".urlencode($searchString);

            	            if ($count == 0) {
            	            	// swap last and first entry
            	                $prev = count($sourceStrings)-1;
            	                $next = $count+1;
            	            }
            	            elseif ($count == count($sourceStrings)-1) {
            	            	// swap last and first entry
            	                $prev = $count-1;
            	                $next = 0;
            	            }
            	            else {
            	            	// swap entrys linear
            	            	$prev = $count-1;
            	            	$next = $count+1;
            	            }

            	            $removeLink    = "do_delete_string.php?pos=$count&$fileparms"
            	            	."&string=".urlencode($sourceKey);
            	            $moveUpLink    = "do_reorder_string.php?pos1=$count&pos2=$prev&$fileparms";
            	            $moveDownLink  = "do_reorder_string.php?pos1=$count&pos2=$next&$fileparms";
                			if (empty($searchString)) {
            				?>
            				<td style="padding-left: 3px;">
            	            <a href="<?php echo $moveUpLink; ?>"><img src="<?php echo $g_localizerConfig['ICONS_DIR']; ?>/up.png" border="0"></a>
            	            </td>
            	           	<td style="padding-left: 3px;">
            	            <a href="<?php echo $moveDownLink; ?>"><img src="<?php echo $g_localizerConfig['ICONS_DIR']; ?>/down.png" border="0"></a>
                   	        </td>
                   	        <?php
            	            }
            	            ?>
            	            <td style="padding-left: 3px;">
            	            <a href="<?php echo $removeLink; ?>" onClick="return confirm('<?php putGS('Are you sure you want to delete this entry?'); ?>');"><img src="<?php echo $g_localizerConfig['ICONS_DIR']; ?>/delete.png" border="0" vspace="4"></a>
            	            </td>

            	            <td style="padding-left: 5px;" nowrap>
								<SELECT name="change_prefix_<?php echo $count; ?>" class="input_select">
								<?PHP
								foreach ($mapPrefixToDisplay as $prefix => $displayStr) {
									if ($prefix != $screenDropDownSelection) {
										camp_html_select_option($prefix, null, $displayStr);
									}
								}
								?>
								</SELECT>
								<input type="button" name="" value="Move" onclick="location.href='do_string_switch_file.php?string=<?php echo urlencode($sourceKey); ?>&new_prefix='+this.form.change_prefix_<?php echo $count; ?>.options[this.form.change_prefix_<?php echo $count; ?>.selectedIndex].value+'&<?php echo $fileparms; ?>';" class="button">
            	            </td>
                            <?php
                	        }
                			?>
			         </tr>
			         </table>
		        </td>

				</tr>
	        <?php
	    }
	    $count++;
	}
	if (count($sourceStrings) <= 0) {
		if (empty($searchString)) {
			?>
			<tr><td align="center" style="padding-top: 10px; font-weight: bold;"><?php putGS("No source strings found.");?> </td></tr>
			<?php
		}
		else {
			?>
			<tr><td align="center" style="padding-top: 10px; font-weight: bold;"><?php putGS("No matches found.");?> </td></tr>
			<?php
		}
	}
	elseif (!$foundUntranslatedString) {
		if (empty($searchString)) {
			?>
			<tr><td align="center" style="padding-top: 10px; font-weight: bold;"><?php putGS("All strings have been translated."); ?></td></tr>
			<?php
		}
		else {
			?>
			<tr><td align="center" style="padding-top: 10px; font-weight: bold;"><?php putGS("No matches found.");?> </td></tr>
			<?php
		}
	}
	?>
	</table>

	<table style="margin-left: 8px; margin-top: 5px;">
	<tr>
		<td>
			<input type="submit" name="save_button" value="<?php putGS('Save'); ?>" class="button">
		</td>
	</tr>
	</table>
	</form>

		</td> <!-- End translate strings box -->
	</tr>
	</table>
	<?php
} // fn translationForm
?>