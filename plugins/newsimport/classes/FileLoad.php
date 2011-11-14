<?php

/*
 * Auxiliary XML load methods
 */
class FileLoad
{

/*
 * Loads XML files with fixing ecnoding errors
 */
    public static function LoadFix($p_fileName)
    {
        $content = '';

        try {
            $content = file_get_contents($p_fileName);
        }
        catch (Exception $exc) {
            return null;
        }

        $replace_from = array(
            ''.chr(195).chr(34),
            ''.chr(195).chr(45),
            'u&#776;',
            'o&#776;',
            'a&#776;',
            'U&#776;',
            'O&#776;',
            'A&#776;',
        );
        $replace_to = array(
            'Ä',
            'Ö',
            'ü',
            'ö',
            'ä',
            'Ü',
            'Ö',
            'Ä',
        );

        $content = str_replace($replace_from, $replace_to, $content);
        $content = iconv('UTF-8','UTF-8//IGNORE', $content);

        //$content = str_replace(array('&#32;', '&#40;', '&#41;', '&#45;', '&#46;', '&#47;', '&#58;', '&#64;', '&#13;&#10;'), array(' ', '(', ')', '-', '.', '/', ':', '@', "\n"), $content);
        $forbidden_contractions = array('38', '60', '62'); // do not translate into &, <, >

        $content_lines = explode('&#', $content);
        $content = array_shift($content_lines);
        foreach ($content_lines as $one_line) {
            $one_line_parts = explode(';', $one_line, 2);
            if ( (2 == count($one_line_parts)) && (is_numeric($one_line_parts[0])) && (!in_array($one_line_parts[0], $forbidden_contractions)) ) {
                $content .= mb_convert_encoding('&#' . intval($one_line_parts[0]) . ';', 'UTF-8', 'HTML-ENTITIES');
                $content .= $one_line_parts[1];
            }
            else {
                $content .= '&#' . $one_line;
            }
        }

        //$content = str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $content);
        $content = str_replace(array('&',), array('&amp;',), $content);

        return $content;
    } // fn LoadFix

} // class FileLoad


?>
