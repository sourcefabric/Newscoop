<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * @title News Desk
 * @description News Desk widget.
 * @homepage http://www.sourcefabric.org
 * @author Sourcefabric o.p.s.
 * @version 1.0
 * @license GPLv3
 */
class NewsDesk extends Widget
{
    /**
     * Render widget
     */
    public function render()
    {
        echo $GLOBALS['controller']->view->action('widget', 'ingest', 'admin');
    }
}
