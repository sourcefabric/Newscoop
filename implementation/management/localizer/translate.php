<?php    
/**
 * Creates an form for translation
 * from $p_source to $p_target language
 * @param LocalizerLanguage $p_source
 * @param LocalizerLanguage $p_target
 * @param array $p_file
 */
function translationForm($p_request) {
	if (!isset($p_request['localizer_target_language'])) {
		$p_request['localizer_target_language'] = LOCALIZER_DEFAULT_LANG;
	}
	if (!isset($p_request['localizer_source_language'])) {
		$tmpLanguage =& new LocalizerLanguage(null, null, $p_request['TOL_Language']);
		$p_request['localizer_source_language'] = $tmpLanguage->getLocalizerLanguageCode();
	}
	
	// translate an xml-file
	$directory = $p_request['dir'];
	$base = 'locals';
	$screenDropDownSelection = $directory;
	if (!isset($p_request['dir']) || ($directory == '/globals')) {
		$base = 'globals';
		$directory = '/';
		$screenDropDownSelection = '/globals';
	}
	//echo "Base: $base, Directory: $directory<br>";
	$sourceLang =& new LocalizerLanguage($base, $directory, $p_request['localizer_source_language']);
	$targetLang =& new LocalizerLanguage($base, $directory, $p_request['localizer_target_language']);
	$defaultLang =& new LocalizerLanguage($base, $directory, LOCALIZER_DEFAULT_LANG);
	
	if (!$sourceLang->loadGsFile()) {
    	$sourceLang->loadXmlFile();
    	$targetLang->loadXmlFile();
		$defaultLang->loadXmlFile();
    }
    else {
    	$targetLang->loadGsFile();
    	$defaultLang->loadGsFile();
    }

    $sourceStrings = $sourceLang->getTranslationTable();
	$targetStrings = $targetLang->getTranslationTable();
	$localizer =& Localizer::getInstance();
	$languages = $localizer->getLanguages($sourceLang->getMode());
	
	$screens = array();
	$screens[] = "";
	$screens["/"] = getGS("Home");
	$screens["/globals"] = getGS("Globals");
	$screens["/pub"] = getGS("Publications");
	$screens["/pub/issues"] = getGS("Issues");
	$screens["/pub/issues/sections"] = getGS("Sections");
	$screens["/pub/issues/sections/articles"] = getGS("Articles");
	$screens["/pub/issues/sections/articles/images"] = getGS("Article Images");
	$screens["/pub/issues/sections/articles/topics"] = getGS("Article Topics");
	$screens["/imagearchive"] = getGS("Image Archive");
	$screens["/templates"] = getGS("Templates");
	$screens["/a_types"] = getGS("Article Types");
	$screens["/a_types/fields"] = getGS("Article Type Fields");
	$screens["/topics"] = getGS("Topics");
	$screens["/languages"] = getGS("Languages");
	$screens["/country"] = getGS("Countries");
	$screens["/localizer"] = getGS("Localizer");
	$screens["/logs"] = getGS("Logs");
	$screens["/users"] = getGS("Users");
	$screens["/u_types"] = getGS("User Types");
	$screens["/users/subscriptions"] = getGS("User Subscriptions");
	$screens["/users/subscriptions/sections"] = getGS("User Subscriptions Sections");			
	?>
	<table width="100%">
	<tr>
		<td align="center" valign="top" width="100%"> <!-- Begin top control panel -->
	
		<table border="0" class="message_box" style="border: 1px solid black;" width="600px;">
		<form action="index.php" method="post">
	    <INPUT TYPE="hidden" name="action" value="translate">
	    <INPUT TYPE="hidden" name="base" value="<?php echo $base; ?>">
	    <INPUT TYPE="hidden" name="localizer_lang_id" value="<?php echo $targetLang->getLocalizerLanguageCode(); ?>">
		<tr>
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
		        		<?php echo Display::LanguageMenu($languages, $p_request['localizer_source_language']); ?>
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
				    	<?php echo Display::LanguageMenu($languages, $p_request['localizer_target_language']); ?>
				        </select>
					</td>
				</tr>
				</table>
			</td>

			<td>
				<table>
				<tr>
					<td>
						<?php putGS('Screen:'); ?>
					</td>
				</tr>
				<tr>
					<td>
						<?PHP
						$extras = ' onchange="this.form.submit();" ';
						$extras .= ' class="input_select"';
						CampsiteInterface::CreateSelect('dir', $screens, $screenDropDownSelection, $extras, true);
						?>
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
						<?php
						// submit button will override hidden input field.
						$hideTranslated = "";
			            if (isset($p_request['hide_translated'])) { 
			            	$hideTranslated = "CHECKED";
			            } 
						?>
			           	<input type="checkbox" name="hide_translated" value="" <?php echo $hideTranslated; ?> class="input_checkbox" onchange="this.form.submit();"><?php putGS('Hide translated strings?'); ?>
					</td>					
				</tr>
				</table>
			</td>
		</tr>
        </form>
		</table>
		
		</td><!-- End left-hand column -->
	</tr>
	
	<tr>
		<td align="center" valign="top"><!-- Begin right-hand column -->
	
	<?PHP
	$missingStrings = Localizer::FindMissingStrings($directory);
	if ((count($missingStrings) > 0)  && ($screenDropDownSelection != '/globals')) {
		?>
		<table align="center" style="background-color: #D0DB63; border: 1px solid #357654;" width="600px">
        <form action="index.php" target="<?php echo LOCALIZER_PANEL_FRAME; ?>" method="post">
        <input type="hidden" name="action" value="add_missing_translation_strings">
        <input type="hidden" name="base" value="<?php echo $base; ?>">
        <input type="hidden" name="dir" value="<?php echo $screenDropDownSelection; ?>">
		<tr>
			<td width="1%">
				<img src="<?php echo LOCALIZER_ICONS_DIR; ?>/add.png">
			</td>
			
			<td width="98%">
				<?php putGS("The following strings are missing from the translation files:"); ?>
				<div style="overflow: auto; height: 50px; background-color: #EEEEEE; border: 1px solid black; padding-left: 3px;">
				<?PHP
				foreach ($missingStrings as $missingString) {
					echo htmlspecialchars($missingString)."<br>";
				}
				?>
				</div>
			</td>
			
			<td width="1%">
		        <input type="submit" value="<?php putGS("Add"); ?>" class="button">			
			</td>
		</tr>
		</form>
		</table>
		<?php
	}
	
	$unusedStrings = Localizer::FindUnusedStrings($directory);
	if ((count($unusedStrings) > 0) && ($screenDropDownSelection != '/globals')) {
		?>
		<p>
		<table align="center" style="background-color: #F5817D; border: 1px solid #C51325;" width="600px">
        <form action="index.php" target="<?php echo LOCALIZER_PANEL_FRAME; ?>" method="post">
        <input type="hidden" name="action" value="delete_unused_translation_strings">
        <!--<input type="hidden" name="Id" value="<?php echo $sourceLang->getLocalizerLanguageCode(); ?>">-->
        <input type="hidden" name="base" value="<?php echo $base; ?>">
        <input type="hidden" name="dir" value="<?php echo $screenDropDownSelection; ?>">
		<tr>
			<td width="1%">
				<img src="<?php echo LOCALIZER_ICONS_DIR; ?>/delete.png">
			</td>
			
			<td width="98%">
				<?php putGS("The following strings are not used:"); ?>
				<div style="overflow: auto; height: 50px; background-color: #EEEEEE; border: 1px solid black; padding-left: 3px;">
				<?PHP
				foreach ($unusedStrings as $unusedString) {
					echo htmlspecialchars($unusedString)."<br>";
				}
				?>
				</div>
			</td>
			
			<td width="1%">
		        <input type="submit" value="<?php putGS("Delete"); ?>" class="button">			
			</td>
		</tr>
		</form>
		</table>
		<?php
	}	
	?>
	<div style="overflow: auto; width: 600px; height: 350px; border: 1px solid black; margin-top: 5px;">
	<table border="0">
	<form action="index.php" method="post">
    <INPUT TYPE="hidden" name="action" value="save_translation">
    <INPUT TYPE="hidden" name="base" value="<?php echo LOCALIZER_PREFIX; ?>">
    <INPUT TYPE="hidden" name="dir" value="<?php echo $screenDropDownSelection; ?>">
    <INPUT TYPE="hidden" name="localizer_target_language" value="<?php echo $targetLang->getLocalizerLanguageCode(); ?>">
    <INPUT TYPE="hidden" name="localizer_source_language" value="<?php echo $sourceLang->getLocalizerLanguageCode(); ?>">
	<?PHP 
	$foundUntranslatedString = false;
	if (count($sourceStrings) <= 0) {
		$foundUntranslatedString = true;
		?>
		<tr><td><?php putGS("There are no source strings");?> </td></tr>
		<?php
	}
	$count = 0;
	foreach ($sourceStrings as $key => $value) { 	
	    if (isset($targetStrings[$key]) && ($targetStrings[$key]!='')) {
	        $targetValueDisplay = Display::ToWebString($targetStrings[$key], 0, 1, 0);
	        $pre  = '';
	        $post = '';
	    } else {
	        $targetValueDisplay = '';
	        $pre    = '<FONT COLOR="red">';
	        $post   = '</FONT>';
	    }
	
		$sourceKeyDisplay = htmlspecialchars($key);
	
		// Dont display translated strings
	    if (isset($p_request['hide_translated']) && !empty($targetStrings[$key])) {
	    	?>
	        <input name="data[<?php echo $count; ?>][key]" type="hidden" value="<?php echo $sourceKeyDisplay; ?>">
	        <input name="data[<?php echo $count; ?>][value]" type="hidden" value="<?php echo $targetValueDisplay; ?>">
	        <?php
	    } 
	    else { 
	    	$foundUntranslatedString = true;
	    	// Display string
	    	?>
	        <tr>
	        	<td style="padding-top: 7px;">
				<?php 
            	// If the string exists in the source language, display that
	            if ($sourceStrings[$key]) {
	            	?>
	                <b><?php echo $sourceLang->getLocalizerLanguageCode(); ?>:</b> <?php echo $pre.htmlspecialchars($sourceStrings[$key]).$post; ?>
	                <?php
	            } 
	            // Otherwise, display it in english.
	            else {
	            	?>
	                <b><?php echo LOCALIZER_DEFAULT_LANG; ?>:</b> <?php echo $pre.$sourceKeyDisplay.$post; ?>
	                <?php
	            }
				?>
				</td>
			</tr>
			<tr>
			<td>
		        <input name="data[<?php echo $count; ?>][key]" type="hidden" value="<?php echo $sourceKeyDisplay; ?>">
		        <input name="data[<?php echo $count; ?>][value]" type="text" size="<?php echo LOCALIZER_INPUT_SIZE; ?>" value="<?php echo $targetValueDisplay; ?>" class="input_text">
	        </td>
	        
			<?php
			// default language => can change keys
	        if ($targetLang->getLocalizerLanguageCode() == LOCALIZER_DEFAULT_LANG) {     
				echo "<td>";
	            $fileparms = "localizer_target_language=".$targetLang->getLocalizerLanguageCode()
	            	."&base=".$base
	            	."&dir=".urlencode($screenDropDownSelection);
	
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
	
	            $removeLink    = LOCALIZER_PANEL_SCRIPT."?action=remove_string&pos=$count&$fileparms"
	            	."&string=".urlencode($key);
	            $moveUpLink    = LOCALIZER_PANEL_SCRIPT."?action=move_string&pos1=$count&pos2=$prev&$fileparms";
	            $moveDownLink  = LOCALIZER_PANEL_SCRIPT."?action=move_string&pos1=$count&pos2=$next&$fileparms";
				?>
	            <a href="<?php echo $moveUpLink; ?>" target="<?php echo LOCALIZER_PANEL_FRAME; ?>"><img src="<?php echo LOCALIZER_ICONS_DIR; ?>/up.png" border="0"></a>
	            </td>
	           	<td>
	            <a href="<?php echo $moveDownLink; ?>" target="<?php echo LOCALIZER_PANEL_FRAME; ?>"><img src="<?php echo LOCALIZER_ICONS_DIR; ?>/down.png" border="0"></a>
       	        </td>
	            <td>
	            <a href="<?php echo $removeLink; ?>" onClick="return confirm('<?php putGS('Are you sure you want to delete this entry?'); ?>');" target="<?php echo LOCALIZER_PANEL_FRAME; ?>"><img src="<?php echo LOCALIZER_ICONS_DIR; ?>/delete.png" border="0" vspace="4"></a>
	            </td>
				</tr>
	            <?php 
	        }
			?>
	        <?php
	    }
	    $count++;
	}
	if (!$foundUntranslatedString) {
		?>
		<tr><td valign="middle"><?php putGS("All strings have been translated."); ?></td></tr>
		<?php	
	}
	?>
	</table>
	</div>
	
	<table>
	<tr>
		<td>
			<input type="submit" name="save_button" value="<?php putGS('Save'); ?>" class="button">
		</td>
	</tr>
	</table>
	</form>
	
		</td> <!-- End right-hand column -->
	</tr>
	</table>
	<?php
} // fn translationForm
?>