<?php
/**
 * @package Newscoop
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

/**
 * @author Sourcefabric o.p.s.
 * @description Wikipedia search.
 * @homepage http://www.sourcefabric.org
 * @version 1.0
 * @license GPLv3
 */
class SearchWikipedia extends Widget
{
    protected $title;

    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Wikipedia Search', array(), 'extensions');
    }

    public function render()
    {
        include_once dirname(__FILE__) . '/search.phtml';
    }
}
