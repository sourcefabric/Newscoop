<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generates webcode for articles based on their numbers
 */
class GenerateWebcodeCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('webcode:generate')
            ->setDescription('Generate webcodes for articles without webcode. It\'s always safe to run the command without the clear parameters.')
            ->addArgument('number', InputArgument::OPTIONAL, 'Article number range to start from, e.g. 300', 1)
            ->addOption('clear', null, InputOption::VALUE_NONE, 'If set, clears webcodes and starts generating webcodes afterards. Use this the first time.')
            ->addOption('only-clear', null, InputOption::VALUE_NONE, 'If set, clears webcodes. Doesn\'t start generation.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getApplication()->getKernel()->getContainer()->getService('em');
        $webcodeService = $this->getApplication()->getKernel()->getContainer()->getService('webcode');

        $number = (int) $input->getArgument('number');

        try {
            ini_set('memory_limit', '-1');

            if ($input->getOption('clear') || $input->getOption('only-clear')) {
                $output->writeln('<info>Clearing webcodes.</info>');

                $qb = $em->createQueryBuilder();
                $result = $qb->update('Newscoop\Entity\Article', 'a')
                    ->set('a.webcode', 'null')
                    ->getQuery()
                    ->execute();

                $output->writeln('<info>Articles clear from webcode: '.$result.'.</info>');

                $result = $qb->update('Newscoop\Entity\Webcode', 'w')
                    ->delete()
                    ->getQuery()
                    ->execute();

                $output->writeln('<info>Webcodes cleared: '.$result.'.</info>');
                if ($input->getOption('only-clear')) {
                    return 0;
                }
            }

            $output->writeln('<info>Generating webcodes. Please wait! It can take up to few minutes, depends on database size.</info>');

            $articlesCount = (int) $em->getRepository('Newscoop\Entity\Article')
                ->createQueryBuilder('a')
                ->select('COUNT(a.number)')
                ->where('a.webcode IS NULL')
                ->getQuery()
                ->getSingleScalarResult();

            $output->writeln('<info>Articles to generate webcode for: '.$articlesCount.'.</info>');

            $batch = 100;
            $steps = ($articlesCount > $batch) ? ceil($articlesCount/$batch) : 1;

            for ($i = 0; $i < $steps; $i++) {

                $offset = $i * $batch;

                $articles = $em->getRepository('Newscoop\Entity\Article')
                    ->createQueryBuilder('a')
                    ->where('a.webcode IS NULL')
                    ->orderBy('a.number', 'ASC')
                    ->setFirstResult($offset)
                    ->setMaxResults($batch)
                    ->getQuery()
                    ->getResult();

                foreach ($articles as $key => $article) {
                    if ($article->getNumber() > $number && $number) {
                        $this->clearWebcode($em, $article->getNumber(), $article->getLanguage());
                        $webcodeService->setArticleWebcode($article);
                    }
                }
            }

            $output->writeln('<info>Webcodes generated successfully!</info>');
            return 0;
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
            return 1;
        }
    }

    /**
     * Clears old webcode
     *
     * @param EntityManager $em            Entity Manager
     * @param string        $articleNumber Article
     *
     * @return void
     */
    private function clearWebcode($em, $articleNumber, $articleLanguage)
    {
        $webcode = $em->getRepository('Newscoop\Entity\Webcode')
                ->createQueryBuilder('w')
                ->leftJoin('w.article', 'a')
                ->where('a.number = :number')
                ->andWhere('a.language = :language')
                ->setParameter('number', $articleNumber)
                ->setParameter('language', $articleLanguage)
                ->getQuery()
                ->getOneOrNullResult();

        if ($webcode) {
            $em->remove($webcode);
            $em->flush();
        }
    }
}
