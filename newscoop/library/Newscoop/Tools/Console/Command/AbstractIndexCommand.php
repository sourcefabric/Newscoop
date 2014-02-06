<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 * Base Index Command
 */
abstract class AbstractIndexCommand extends Console\Command\Command
{
    /**
     * Get all indexers
     *
     * @return array
     */
    protected function getIndexers()
    {
        return array(
            'articles' => $this->getHelper('container')->getService('search_indexer_article'),
            'comments' => $this->getHelper('container')->getService('search_indexer_comment'),
            'users' => $this->getHelper('container')->getService('search_indexer_user'),
            'twitter' => $this->getHelper('container')->getService('search_indexer_twitter'),
        );
    }
}
