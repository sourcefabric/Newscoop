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
 * @title Hello World
 * @author Sourcefabric o.p.s.
 * @description Widget sample.
 * @homepage http://www.sourcefabric.org
 * @version 1.0
 */
class HelloWorld extends Widget
{
    public function render()
    {
        echo '<p>', $this->_('Hello world!'), '</p>';
    }
}
