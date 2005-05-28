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

    
            

}
?>