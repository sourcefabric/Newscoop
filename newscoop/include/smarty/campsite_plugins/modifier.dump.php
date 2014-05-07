<?php
/**
 * @package Newscoop
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Newscoop dump modifier plugin
 *
 * Type:     modifier
 * Name:     dump
 * Purpose:  Dump given object
 *
 * @param Object to dump
 *     $object
 */

function smarty_modifier_dump($object)
{
    ladybug_dump($object);
}
