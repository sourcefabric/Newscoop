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
class UpdateArticlePopularityRankingCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('popularity:ranking_update')
        ->setDescription('Update Article Popularity Ranking.')
        ->setHelp(<<<EOT
Update article popularity ranking.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $popularity = $this->getHelper('container')->getService('article.popularity');
        $popularity->updateRanking();

        $output->writeln('Article Popularity Ranking updated.');
    }
}
