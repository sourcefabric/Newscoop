<?php

class XMLLoad
{

    public static function FixUTF8($p_fileName)
    {
        $content = '';

        $content = conv('UTF-8','UTF-8//IGNORE', file_get_contents($p_fileName));

        return $content;
    }

} // class XMLLoad


?>
