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

    /**
     * @param string $src
     * @param string $type
     * @param string $alt
     */
    public function __construct($src, $type = '', $alt = '')
    {
        $this->src = (string) $src;
        $this->type = (string) $type;
        $this->alt = (string) $alt;
    }

    /**
     * Outputs html for given media type
     * @return string
     */
    public function __toString()
    {
        global $Campsite;

        ob_start();
        echo '<div class="mediaplayer">';

        switch ($this->type) {
            case 'image/jpeg':
            case 'image/png':
            case 'image/gif':
                echo '<img src="', $this->src, '" height="240" alt="', $this->alt, '" />';
                break;

            case 'audio/mpeg':  
            case 'audio/ogg':  
                echo '<audio src="', $this->src, '" controls></audio>';
                break;

            case 'video/mp4':
            case 'video/ogg':
            case 'video/webm':
            case 'application/octetstream':
                echo '<video src="', $this->src, '" width="320" height="240" controls></video>';
                break;

            case 'video/flv':
                $player = $Campsite['WEBSITE_URL'] . '/videos/player.swf';
                echo '<object width="320" height="240">';
                echo '<param name="movie" value="', $player, '"></param>';
                echo '<param name="flashvars" value="src=', urlencode($this->src), '"></param>';
                echo '<embed src="', $player, '" type="application/x-shockwave-flash" width="320" height="240" flashvars="src=', urlencode($this->src), '"></embed>';
                echo '</object>';
                break;

            default:
                if (empty($alt)) {
                    $alt = basename($this->src);
                }
                echo '<p><a href="', $this->src, '">', $alt, '</a></p>';
                break;
        }

        echo '</div>';
        return ob_get_clean();
    }
}
