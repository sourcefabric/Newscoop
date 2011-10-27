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

        $content = str_replace(array(''.chr(195).chr(34), ''.chr(195).chr(45)), array('Ä', 'Ö'), $content);
        $content = iconv('UTF-8','UTF-8//IGNORE', $content);
        $content = str_replace(array('&#32;', '&#40;', '&#41;', '&#45;', '&#46;', '&#47;', '&#58;', '&#64;'), array(' ', '(', ')', '-', '.', '/', ':', '@'), $content);
        $content = str_replace(array('&'), array('&amp;'), $content);

        return $content;
    } // fn LoadFix

} // class FileLoad


?>
