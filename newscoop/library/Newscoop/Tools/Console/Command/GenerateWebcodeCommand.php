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
    static $map = array(
        0 => 'A',
        1 => 'B',
        2 => 'C',
        3 => 'D',
        4 => 'E',
        5 => 'F',
        6 => 'G',
        7 => 'H',
        8 => 'I',
        9 => 'J',
        10 => 'K',
        11 => 'L',
        12 => 'M',
        13 => 'N',
        14 => 'O',
        15 => 'P',
        16 => 'Q',
        17 => 'R',
        18 => 'S',
        19 => 'T',
        20 => 'U',
        21 => 'V',
        22 => 'W',
        23 => 'X',
        24 => 'Y',
        25 => 'Z'
    );

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
        $number = (int)$input->getArgument('number');

        try {

            $output->writeln('<info>Generating webcodes. Please wait! It can take up to few minutes, depends on database size.</info>');
            ini_set('memory_limit', '-1');

            $articles = $em->getRepository('Newscoop\Entity\Article')
                ->createQueryBuilder('a')
                ->getQuery()
                ->getResult();

            foreach ($articles as $key => $article) {
                if ($article->getNumber() > $number && $number) {
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
    private function base26($articleNumber) {

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

        $cleanCode = $this->base26($articleNumber);
        $letterCode = '';
        foreach($cleanCode as $no) {
            $letterCode .= self::$map[$no];
        }

        $returnCode = $letterCode;
        for ($i = 0; $i < (5 - strlen($letterCode)); $i ++) {
            $returnCode = self::$map[0] . $returnCode;
        }

        return strtolower($returnCode);
    }

    /**
     * Updates article webcode
     *
     * @param EntityManager $em             Entity Manager
     * @param string        $webcode        Article webcode
     * @param int           $articleNumber  Article number
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
}
