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
 * Article popularity update command
 */
class UpdateArticlePopularityMetricsCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('popularity:metrics_update')
        ->setDescription('Update Article Popularity Metrics.')
        ->setHelp(<<<EOT
Update article popularity metrics.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $popularity = $this->getHelper('container')->getService('article.popularity');
        $popularity->updateMetrics();

        $output->writeln('Article Popularity Metrics updated.');
    }
}
