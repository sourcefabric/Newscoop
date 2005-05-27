<?php
class Display {

	/**
	 * Prepare a string for display.
	 * @param string p_str
	 * @param boolean p_stripSlashes
	 * @param boolean p_changeQuotes
	 * @param boolean p_htmlSpecialChars
	 * @return string
	 */
    function ToWebString($p_str, $p_stripSlashes, $p_changeQuotes, $p_htmlSpecialChars) {
        if ($p_stripSlashes) {
            $p_str = stripslashes($p_str);
        }
        if ($p_changeQuotes) {
          	$p_str = str_replace('"', '&#34;', $p_str);
        }
        if ($p_htmlSpecialChars) {
            $p_str = htmlspecialchars($p_str);
        }
        return $p_str;
    }



    
//    function sourceLangMenu($currId, $file) {
//    	$localizer =& Localizer::getInstance();
//        $languages = $localizer->getLanguages();
//        if (!$languages) {
//        	return getGS('cannot read $1', 'language file').'<br>';
//        }

//        foreach ($languages as $key=>$val) {
//            if (!(file_exists(LOCALIZER_BASE_DIR.LOCALIZER_ADMIN_DIR.$file['dir'].'/'.$file['base'].$val['Id'].'.xml') && ($file['Id'] != $val['Id']))) {
//                 unset($languages[$key]);
//            }
//        }

//        $menu .= getGS('Translate from:').'
//            &nbsp;
//            <SELECT NAME="localizer_source_language">';
//        $menu .= Display::_langMenuOptions($languages, $currId);
//        $menu .= '</select>';
//        return $menu;
//    }

    
    function LanguageMenu($languages, $currId) {
    	$options = '';
        foreach($languages as $key=>$val) {
            if ($currId == $val['Id']) {
                $curr = 'selected';
            } 
            else {
                $curr = '';
            }
            $options .= '<option value="'.$val['Id'].'" '.$curr.'>'.$val['NativeName'].'</option>';
        }
        return $options;
    }
    

    function manageLangForm() {
        $languages = Localizer::GetLanguages();

        $html .= '
            <table border="1">
              <tr>
                <th>'.getGS('name').'</th>
                <th>'.getGS('native name').'</th>
                <th>'.getGS('code').'</th>
                <th>'.getGS('edit').'</th>
                <th>'.getGS('delete').'</th>
              </tr>
            ';

        foreach($languages as $nr=>$l) {
            $editLink = '<a href="'.LOCALIZER_PANEL_SCRIPT.'?action=editLanguage&Id='.$l['Code'].'.'.$l['Name'].'">'.getGS('edit').'</a>';
            $delLink  = '<a href="'.LOCALIZER_PANEL_SCRIPT.'?action=delLanguage&Id='.$l['Id'].'">'.getGS('delete').'</a>';
            $html .= "<tr><td>$l[Name]</td><td>$l[NativeName]</td><td>$l[Code]</td><th>$editLink</th><th>$delLink</th></tr>";
        }
        return $html;
    }
    

//    function parseFolder($dirname, $depth=0) {
//        $space = 2;
//
//        $structure = File_Find::mapTreeMultiple($dirname);
//        ksort($structure, SORT_STRING);
//        #print_r($structure);
//
//        if ($depth == 0) {
//            $html .= str_repeat(' ',$depth * $space).'<b><a href="'.LOCALIZER_PANEL_SCRIPT.'?action=newLangFilePref&dir='.$dirname.'/'.$dir.'" target="'.LOCALIZER_PANEL_FRAME.'">'.strtoupper(' / ')."</a></b>\n";
//        }
//
//        foreach($structure as $dir=>$file) {
//            if (is_array($file)) {              // it's a directory
//                unset($base);
//                unset($baseadd);
//
//                if (!(substr($dir, 0, strlen(LOCALIZER_PREFIX_HIDE)) == LOCALIZER_PREFIX_HIDE)) {   // hide special dirs
//                    $html .= str_repeat(' ', ($depth+1) * $space).'<b><a href="'.LOCALIZER_PANEL_SCRIPT.'?action=newLangFilePref&dir='.$dirname.'/'.$dir.'" target="'.LOCALIZER_PANEL_FRAME.'">'.strtoupper($dir)."</a></b>\n";
//                    $html .= Display::parseFolder($dirname.'/'.$dir, $depth+1);
//                }
//            } else {                       
//            	// it's a file
//                if (((strpos(' '.$file, LOCALIZER_PREFIX) == 1) || (strpos(' '.$file, LOCALIZER_PREFIX_GLOBAL) == 1))
//                     &&
//                   (substr($file, strlen($file) - 4) == '.xml')) {
//
//                    if (!LOCALIZER_MAINTAINANCE && preg_match("/[^.]*\.".LOCALIZER_DEFAULT_LANG."\.xml/", $file)) {
//                        // skip default language if not maintainance mode
//                    } else {
//                    $Id = explode('.', $file);
//                    $html .= str_repeat(' ', ($depth+1) * $space).'<a href="'.LOCALIZER_PANEL_SCRIPT.'?action=translate&Id='.$Id[1].'.'.$Id[2].'&base='.$Id[0].'&dir='.$dirname.'" target="'.LOCALIZER_PANEL_FRAME.'">'.$file."</a>\n";
//                    }
//                }
//            }
//
//        }
//
//        if ($depth == 0) {
//            return "<pre>$html</pre>";
//        } else {
//            return $html;
//        }
//    }

    
            
    function newLangFilePref($dir) {
        // at first check if default files already exists
        $handle = opendir ($dir);
        while (false !== ($file = readdir ($handle))) {
            $exists[$file] = true;
        }
        closedir($handle);

        $html .= '
            '.getGS('create new language file in').' '.strtoupper($dir).'
            <form action="index.php" target="'.LOCALIZER_PANEL_FRAME.'" method="post">
              <input type="hidden" name="action" value="newLangFileForm">
              <input type="hidden" name="dir" value="'.$dir.'">';

        if ($dir == LOCALIZER_START_DIR.'/') {
            if ($exists[LOCALIZER_PREFIX.'.'.LOCALIZER_DEFAULT_LANG.'.xml'] && $exists[LOCALIZER_PREFIX_GLOBAL.'.'.LOCALIZER_DEFAULT_LANG.'.xml']) {
                return getGS('$1 and $2 files already exist in $3', LOCALIZER_PREFIX, LOCALIZER_PREFIX_GLOBAL, strtoupper($dir));
            } else {
                if ($exists[LOCALIZER_PREFIX_GLOBAL.'.'.LOCALIZER_DEFAULT_LANG.'.xml']) {
                    $globals .= ' disabled';
                    $locals  .= ' checked';
                }
                if ($exists[LOCALIZER_PREFIX.'.'.LOCALIZER_DEFAULT_LANG.'.xml']) {
                    $locals  .= ' disabled';
                    $globals .= ' checked';
                }

            $html .= '
              Type:<br>
              <input type="radio" name="base" value="'.LOCALIZER_PREFIX.'"'.$locals.'>'.LOCALIZER_PREFIX.'
              <input type="radio" name="base" value="'.LOCALIZER_PREFIX_GLOBAL.'"'.$globals.'>'.LOCALIZER_PREFIX_GLOBAL;
            }
        } else {
            if ($exists[LOCALIZER_PREFIX.'.'.LOCALIZER_DEFAULT_LANG.'.xml']) {
                return getGS('$1 file already exist in $2', LOCALIZER_PREFIX, strtoupper($dir));
            } else {
                $html .= '<input type="hidden" name="base" value="'.LOCALIZER_PREFIX.'">';
            }
        }

        $html .= '
              <br>
              '.getGS('entrys:').'<br>
              <input name="amount" value="1" size="2">

              <input type="submit" value="'.getGS('ok').'">
            </form>';

    	return $html;
    }

    
    function newLangFileForm($amount, $base, $dir) {
        // check input
        if (!$base) {
            return getGS('go').' <a href="JavaScript:history.back()">'.getGS('back').'</a> '.getGS('and select file type');
        }
        if (!isInt($amount)) {
            return getGS('go').' <a href="JavaScript:history.back()">'.getGS('back').'</a> '.getGS('and enter a positive integer value');
        }

        $html .= '
            '.getGS('create new language file $1', strtoupper($dir).'/'.$base.'.'.LOCALIZER_DEFAULT_LANG.'.xml').'
            <form action="index.php" target="'.LOCALIZER_PANEL_FRAME.'" method="post">
             <table border="0">
              <input type="hidden" name="action" value="storeNewLangFile">
              <input type="hidden" name="base" value="'.$base.'">
              <input type="hidden" name="dir" value="'.$dir.'">';

        for($n=1; $n<=$amount; $n++) {
            $html .= "<tr><td><input name='newKey[$n]' size='50'></td></tr>";
        }

        $html .=
            '<tr><td><input type="submit" value="'.getGS('save to file').'"></td></tr>
             </table>
            </form>';
    	return $html;
    }
}
?>