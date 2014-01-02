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
            ->setDescription('Generates webcodes for articles without it.')
            ->addArgument('number', InputArgument::OPTIONAL, 'Article number range to start from, e.g. 300', 1);
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getApplication()->getKernel()->getContainer()->getService('em');
        $number = (int) $input->getArgument('number');

        try {
            $output->writeln('<info>Generating webcodes. Please wait! It can take up to few minutes, depends on database size.</info>');
            ini_set('memory_limit', '-1');

            $articles = $em->getRepository('Newscoop\Entity\Article')
                ->createQueryBuilder('a')
                ->getQuery()
                ->getResult();

            foreach ($articles as $key => $article) {
                if ($article->getNumber() > $number && $number) {
                    $this->clearWebcode($em, $article->getNumber());
                    $webcode = $this->encode($article->getNumber());
                    $webcodeEntity = new \Newscoop\Entity\Webcode($webcode, $article);
                    $em->persist($webcodeEntity);
                    $this->updateArticleWebcode($em, $webcode, $article->getNumber());
                }
            }

            $em->flush();
            $output->writeln('<info>Webcodes generated successfully!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->getMessage().'</error>');
        }
    }

    /**
     * Base26 coding
     *
     * @param int $articleNumber Article number
     *
     * @return array
     */
    private function base26($articleNumber)
    {
        $base26Array = array();
        $num = $articleNumber;
        $index = 0;
        while ($num != 0) {
            $base26Array[$index] = $num % 26;
            $num = floor($num / 26);
            $index ++;
        }

        return array_reverse($base26Array);
    }

    /**
     * Encodes webcode
     *
     * @param int $articleNumber Article number
     *
     * @return array
     */
    private function encode($articleNumber)
    {
        if (!is_numeric($articleNumber)) {
            return false;
        }

        $map = explode(",", strtoupper(implode(",", range("A", "Z"))));
        $cleanCode = $this->base26($articleNumber);
        $letterCode = '';

        foreach ($cleanCode as $no) {
            $letterCode .= $map[$no];
        }

        $returnCode = $letterCode;
        for ($i = 0; $i < (5 - strlen($letterCode)); $i ++) {
            $returnCode = $map[0] . $returnCode;
        }

        return strtolower($returnCode);
    }

    /**
     * Updates article webcode
     *
     * @param EntityManager $em            Entity Manager
     * @param string        $webcode       Article webcode
     * @param int           $articleNumber Article number
     *
     * @return void
     */
    private function updateArticleWebcode($em, $webcode, $articleNumber)
    {
        $queryBuilder = $em->createQueryBuilder();
        $query = $queryBuilder->update('Newscoop\Entity\Article', 'a')
            ->set('a.webcode', $queryBuilder->expr()->literal($webcode))
            ->where('a.number = :number')
            ->setParameter('number', $articleNumber)
            ->getQuery();
        $query->execute();
    }

    /**
     * Clears old webcode
     *
     * @param EntityManager $em            Entity Manager
     * @param string        $articleNumber Article
     *
     * @return void
     */
    private function clearWebcode($em, $articleNumber)
    {
        $webcode = $em->getRepository('Newscoop\Entity\Webcode')
                ->createQueryBuilder('w')
                ->leftJoin('w.article', 'a')
                ->where('a.number = :number')
                ->setParameter('number', $articleNumber)
                ->getQuery()
                ->getOneOrNullResult();

        if ($webcode) {
            $em->remove($webcode);
            $em->flush();
        }
    }
}