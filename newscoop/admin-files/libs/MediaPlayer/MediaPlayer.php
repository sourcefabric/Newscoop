<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * Media player component
 */
class MediaPlayer
{
    /** @var string */
    private $src;

    /** @var string */
    private $type;

    /** @var string */
    private $alt;

    /** @var bool */
    private static $playerLoaded = FALSE;

    /**
     * @param string $src
     * @param string $type
     * @param string $alt
     */
    public function __construct($src, $type, $alt = '')
    {
        $this->src = (string) $src;
        $this->type = (string) $type;
        $this->alt = empty($alt) ? array_shift(explode('?', basename($this->src))) : (string) $alt; // remove ?x=.. part from basename
    }

    /**
     * Outputs html for given media type
     * @return string
     */
    public function __toString()
    {
        global $Campsite;

        $translator = \Zend_Registry::get('container')->getService('translator');
        
        ob_start();
        echo '<div class="mediaplayer ', str_replace('/', ' ', $this->type), '">';

        // present by content type
        switch ($this->type) {
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
                echo '<img src="', $this->src, '" height="240" alt="', $this->alt, '" />';
                break;

            case 'audio/mpeg': 
            case 'audio/ogg': 
                echo '<audio src="', $this->src, '" controls="controls">';
                echo '</audio>';
                break;

            case 'video/mp4':
            case 'video/ogg':
            case 'video/webm':
                // html5 + flow player fallback
                include dirname(__FILE__) . '/video.phtml';
                break;

            case 'video/flv':
                $player = $Campsite['WEBSITE_URL'] . '/public/videos/player.swf';
                include dirname(__FILE__) . '/flash.phtml';
                break;
        }

        // download link
        echo '<p><strong>', $translator->trans('Download file', array(), 'library'), ':</strong> ';
        echo '<a href="', $this->src, '">', $this->alt, '</a></p>';

        echo '</div>';

        if (!self::$playerLoaded) {
            //include_once dirname(__FILE__) . '/player.phtml';
            self::$playerLoaded = TRUE;
        }

        return ob_get_clean();
    }
}
