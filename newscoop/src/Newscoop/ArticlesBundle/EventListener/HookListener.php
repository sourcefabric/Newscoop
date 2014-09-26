<?php
/**
 * @package   Newscoop\ArticlesBundle
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric ź.u.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesBundle\EventListener;

use Doctrine\ORM\EntityManager;

/**
 * Editorial comments hook listener
 */
class HookListener
{
	protected $em;

    public function __construct(EntityManager $em)
    {
        // TODO: write logic here
    }
}
