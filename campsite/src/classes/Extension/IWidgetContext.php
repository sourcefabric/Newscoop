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
 * Widget context interface
 */
interface IWidgetContext
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return bool
     */
    public function isHorizontal();

    /**
     * @return bool
     */
    public function isVertical();
}
