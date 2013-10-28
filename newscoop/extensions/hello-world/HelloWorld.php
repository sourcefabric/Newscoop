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
 * @title Hello World!
 * @author Sourcefabric o.p.s.
 * @description Widget sample.
 * @homepage http://www.sourcefabric.org
 * @version 1.0
 * @license GPLv3
 */
class HelloWorld extends Widget
{
    public function __construct()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->title = $translator->trans('Hello World!', array(), 'extensions');
    }

    public function render()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        echo '<p>', $this->$translator->trans('Hello world!', array(), 'extensions'), '</p>';
    }
}
