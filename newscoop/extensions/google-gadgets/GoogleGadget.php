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
 * @title Google gadget
 * @multi
 */
class GoogleGadget extends Widget
{
    /**
     * @setting
     * @label Paste google generated code
     */
    protected $code = '';

    public function render()
    {
        $code = $this->getCode();
        if (empty($code)) {
            echo '<p>Go to <a href="http://www.google.com/ig/directory?num=24&synd=open" target="_blank">Google gadgets</a>,';
            echo ' pick any gadget you want and copy generated code into settings form.</p>';
            return;
        }

        // get size
        $width = 300;
        $height = 300;
        $matches = array();
        if (preg_match_all('/([hw]=[0-9]+)/', $code, $matches)) {
            foreach ($matches[1] as $match) {
                list($direction, $size) = explode('=', $match);
                if ($direction == 'h') {
                    $height = (int) $size;
                } else {
                    $width = (int) $size;
                }
            }
        }

        // get url
        $url = '';
        $matches = array();
        if (preg_match('/src="([^"]+)"/', $code, $matches)) {
            $url = $matches[1];
        }

        // change output
        $url = str_replace('output=js', 'output=html', $url);

        // return iframe
        echo '<iframe src="', $url, '&output=html" width="', $width, '" height="', $height, '"></iframe>';
    }
}
