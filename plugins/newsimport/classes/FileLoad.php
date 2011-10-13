<?php

class FileLoad
{

    public static function LoadFix($p_fileName)
    {
        $content = '';

        try {
            $content = file_get_contents($p_fileName);
        }
        catch (Exception $exc) {
            return null;
        }

        $content = str_convert(array(''.chr(195).chr(34), ''.chr(195).chr(45)), array('Ä', 'Ö'), $content);
        $content = conv('UTF-8','UTF-8//IGNORE', $content);
        $content = str_convert(array('&'), array('&amp;'), $content);

        return $content;
    } // fn LoadFix

} // class FileLoad


?>
