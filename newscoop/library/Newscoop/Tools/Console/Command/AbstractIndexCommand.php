<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;

/**
 * Base Index Command
 */
abstract class AbstractIndexCommand extends Console\Command\Command
{
    /**
     * Get all search services for indexable documents
     *
     * @return array
     */
    protected function getIndexers()
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $servicIds = $container->getServiceIds();
        $indexingServices = array();

        foreach ($servicIds AS $serviceId) {
            if (strpos($serviceId, 'indexer.') === false) continue;

            $indexingServices[$serviceId] = $container->get($serviceId);
        }

        return $indexingServices;
    }
}
