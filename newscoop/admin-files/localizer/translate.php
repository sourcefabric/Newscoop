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
    global $g_localizerConfig, $ADMIN;
	$localizerTargetLanguage = camp_session_get('localizer_target_language', $g_localizerConfig['DEFAULT_LANGUAGE']);
	$localizerSourceLanguage = camp_session_get('localizer_source_language', '');
	if (empty($localizerSourceLanguage)) {
		if (isset($p_request['TOL_Language'])) {
			$lang = $p_request['TOL_Language'];
		} else {
			$lang = $g_localizerConfig['DEFAULT_LANGUAGE'];
		}
		$tmpLanguage = new LocalizerLanguage(null, $lang);
		$localizerSourceLanguage = $tmpLanguage->getLanguageId();
	}

	$prefix = camp_session_get('prefix', '');
	$screenDropDownSelection = $prefix;

	// Load the language files.
	//echo "Prefix: $prefix<br>";
	$sourceLang = new LocalizerLanguage($prefix, $localizerSourceLanguage);
	$targetLang = new LocalizerLanguage($prefix, $localizerTargetLanguage);
	$defaultLang = new LocalizerLanguage($prefix, $g_localizerConfig['DEFAULT_LANGUAGE']);

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
        	camp_html_add_msg($sourceSaveSuccess);
        }
        $modified = $targetLang->syncToDefault();
        if ($modified) {
        	$targetSaveSuccess = $targetLang->saveFile($mode);
        	camp_html_add_msg($targetSaveSuccess);
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
    $mapPrefixToDisplay["home"] = getGS("Dashboard");
    $mapPrefixToDisplay["api"] = getGS("API");
    $mapPrefixToDisplay["library"] = getGS("Libraries");
    $mapPrefixToDisplay["pub"] = getGS("Publications");
    $mapPrefixToDisplay["issues"] = getGS("Issues");
    $mapPrefixToDisplay["sections"] = getGS("Sections");
    $mapPrefixToDisplay["articles"] = getGS("Articles");
    $mapPrefixToDisplay["article_images"] = getGS("Article Images");
    $mapPrefixToDisplay["article_files"] = getGS("Article Files");
    $mapPrefixToDisplay["article_topics"] = getGS("Article Topics");
    $mapPrefixToDisplay["article_comments"] = getGS("Article Comments");
    $mapPrefixToDisplay["media_archive"] = getGS("Media Archive");
    $mapPrefixToDisplay["geolocation"] = getGS("Geo-location");
    $mapPrefixToDisplay["comments"] = getGS("Comments");
    $mapPrefixToDisplay["system_pref"] = getGS("System Preferences");
    $mapPrefixToDisplay["themes"] = getGS("Themes");
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
    $mapPrefixToDisplay["preview"] = getGS("Preview Window");
    $mapPrefixToDisplay["tiny_media_plugin"] = getGS("Editor Media Plugin");
    $mapPrefixToDisplay["plugins"] = getGS("Plugins");
    $mapPrefixToDisplay["extensions"] = getGS("Extensions");
    $mapPrefixToDisplay["authors"] = getGS("Authors");

    foreach (CampPlugin::GetPluginsInfo(true) as $info) {
    	if (array_key_exists('localizer', $info) && is_array($info['localizer'])) {
    		$mapPrefixToDisplay[$info['localizer']['id']] = getGS($info['localizer']['screen_name']);
    	}
    }

	// Whether to show translated strings or not.
	$hideTranslated = camp_session_get('hide_translated', 'off');

    camp_html_display_msgs();
	?>
	<table>
	<tr>
		<td valign="top"> <!-- Begin top control panel -->

        <form action="/<?php echo $ADMIN; ?>/localizer/index.php" method="post">
		<?php echo SecurityToken::FormParameter(); ?>
        <input type="hidden" name="localizer_lang_id" value="<?php echo $targetLang->getLanguageId(); ?>">
        <input type="hidden" name="search_string" value="<?php echo htmlspecialchars($searchString); ?>">
		<table border="0" cellpadding="0" cellspacing="0" class="box_table">
		<tr>
			<td>
				<table>
				<tr>
					<td>
						<?php putGS('Area to localize'); ?>:
					</td>
				</tr>
				<tr>
					<td>
						<SELECT name="prefix" class="input_select" onchange="this.form.submit();">
						<?PHP
						foreach ($mapPrefixToDisplay as $prefix => $displayStr) {
						    if (!empty($prefix)) {
						        $transl_status[$prefix] = Localizer::GetTranslationStatus($prefix, $localizerTargetLanguage);
                            } else {
                                $transl_status[$prefix] = true;
                            }
						    camp_html_select_option($prefix, $screenDropDownSelection, $displayStr, $transl_status[$prefix]['untranslated'] ? array('style' => 'color:red') : array());
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

			<td>
				<table>
				<tr>
					<td>
						<?php putGS('Translation status:'); ?>
					</td>
				</tr>
				<tr>
					<td>
				        <?php
                        $all = null;
                        $transl = null;
                        $untransl = null;
				        if ($screenDropDownSelection) {
				            $all = $transl_status[$screenDropDownSelection]['all'];
				            $transl = $transl_status[$screenDropDownSelection]['translated'];
				            $untransl = $transl_status[$screenDropDownSelection]['untranslated'];
				        } else {
    				        foreach ($transl_status as $screen) {
    				            $all += $screen['all'];
    				            $transl += $screen['translated'];
    				            $untransl += $screen['untranslated'];
    				        }
				        }
				        if ($all) {
				            putGS("$1 of $2 strings translated", $transl, $all);
				            echo '<br>'.round(100 - 100 / $all * $untransl, 2) . ' %';
				        } else {
				            echo 'N/A';
				        }
				        ?>
					</td>
				</tr>
				</table>
			</td>

		</tr>
		<tr>
			<td align="center" colspan="4">
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
		</table>
        </form>

		</td><!-- End top controls -->
	</tr>

	<!-- Begin search dialog -->
	<tr>
		<td valign="top">
            <form>
            <input type="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
            <input type="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
            <input type="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
			<table border="0" cellspacing="0" cellpadding="0" class="box_table">
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
			</table>
            </form>
		</td>
	</tr>

	<!-- Begin Missing and Unused Strings popups -->
	<tr>
		<td valign="top">

	<?PHP
	if ((count($missingStrings) > 0)  && ($screenDropDownSelection != 'globals')) {
		?>
            <form action="/<?php echo $ADMIN; ?>/localizer/do_add_missing_strings.php" method="post">
		<?php echo SecurityToken::FormParameter(); ?>
        <input type="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
        <input type="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
        <input type="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
        <table border="0" cellspacing="0" cellpadding="0" class="box_table">
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
		</table>
        </form>
		<?php
	}

	if ((count($unusedStrings) > 0) && ($screenDropDownSelection != 'globals')) {
		?>
            <form action="/<?php echo $ADMIN; ?>/localizer/do_delete_unused_strings.php" method="post">
		<?php echo SecurityToken::FormParameter(); ?>
        <input type="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
        <input type="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
        <input type="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
        <table border="0" cellspacing="0" cellpadding="0" class="box_table">
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
		</table>
        </form>
		<?php
	}
	?>
	<!-- Begin translated strings box -->
    <form action="/<?php echo $ADMIN; ?>/localizer/do_save.php" method="post">
	<?php echo SecurityToken::FormParameter(); ?>
    <INPUT TYPE="hidden" name="prefix" value="<?php echo $screenDropDownSelection; ?>">
    <INPUT TYPE="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLanguageId(); ?>">
    <INPUT TYPE="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLanguageId(); ?>">
    <INPUT TYPE="hidden" name="search_string" value="<?php echo $searchString; ?>">
	<table border="0" cellpadding="0" cellspacing="0" class="box_table">
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
	            	if (isset($defaultStrings[$sourceKey])) {
	            		$defaultValue = $defaultStrings[$sourceKey];
	            	} else {
	            		$defaultValue = '';
	            	}
	            	?>
	                <b><?php echo $g_localizerConfig['DEFAULT_LANGUAGE']; ?>:</b> <?php echo $pre.$defaultValue.$post; ?>
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
            	            	."&string=".urlencode($sourceKey).'&'.SecurityToken::URLParameter();
            	            $moveUpLink    = "do_reorder_string.php?pos1=$count&pos2=$prev&$fileparms&".SecurityToken::URLParameter();
            	            $moveDownLink  = "do_reorder_string.php?pos1=$count&pos2=$next&$fileparms&".SecurityToken::URLParameter();
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
								<input type="button" name="" value="Move" onclick="location.href='do_string_switch_file.php?string=<?php echo urlencode($sourceKey); ?>&new_prefix='+this.form.change_prefix_<?php echo $count; ?>.options[this.form.change_prefix_<?php echo $count; ?>.selectedIndex].value+'&<?php echo $fileparms; ?>&<?php echo SecurityToken::URLParameter(); ?>';" class="button">
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

	<table style="margin-left: 12px; margin-top: 5px;">
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
