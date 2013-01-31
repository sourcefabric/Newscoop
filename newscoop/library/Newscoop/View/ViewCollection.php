<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\View;

use ArrayIterator;

/**
 */
class ViewCollection extends ArrayIterator
{
    /**
     * @inheritDocs
     */
    public function current()
    {
        $entity = parent::current();
        return $entity->getView();
    }
}
