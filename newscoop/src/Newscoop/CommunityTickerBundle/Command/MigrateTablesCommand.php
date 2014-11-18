<?php
/**
 * @package Newscoop\CommunityTickerBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\CommunityTickerBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Query\ResultSetMapping;
use Newscoop\CommunityTickerBundle\Entity\CommunityTickerEvent;

/**
 * Migrate community ticker data to plugin table
 */
class MigrateTablesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ticker:migrate')
            ->setDescription('Migrates community ticker data to plugin table and removes old one from core.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $em = $this->getApplication()->getKernel()->getContainer()->getService('em');
            $this->migrateData($em, $output);

            $connection = $em->getConnection();
            $connection->beginTransaction();
            try {
                $connection->query('SET FOREIGN_KEY_CHECKS=0');
                $connection->query('DROP TABLE community_ticker_event');
                $connection->query('SET FOREIGN_KEY_CHECKS=1');
                $connection->commit();
                $output->writeln('<info>Successfully removed. Finished!</info>');
            } catch (\Exception $e) {
                $connection->rollback();
                $output->writeln('<error>Using rollback. Failed!</error>');
            }

        } catch (\Exception $e) {
            throw new \Exception('Something went wrong!');
        }
    }

    /**
     * Migrate data to plugin database from core table
     *
     * @param EntityManager   $em
     * @param OutputInterface $output
     *
     * @return void
     */
    public function migrateData($em, $output)
    {
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('Newscoop\CommunityTickerBundle\Entity\CommunityTickerEvent', 'e');
        $rsm->addFieldResult('e', 'id', 'id');
        $rsm->addFieldResult('e', 'event', 'event');
        $rsm->addFieldResult('e', 'params', 'params');
        $rsm->addFieldResult('e', 'created', 'created');
        $rsm->addJoinedEntityResult('Newscoop\Entity\User', 'u', 'e', 'user');
        $rsm->addFieldResult('u', 'Id', 'id');
        $query = $em->createNativeQuery('SELECT e.id, e.event, e.params, e.created, u.Id FROM community_ticker_event e ' .
            'LEFT JOIN liveuser_users u ON u.id = e.user_id',
            $rsm
        );
        $events = $query->getArrayResult();
        foreach ($events as $key => $event) {
            $user = $em->getRepository('Newscoop\Entity\User')->findOneBy(array('id' => $event['user']['id']));
            $existingEvent = $em->getRepository('Newscoop\CommunityTickerBundle\Entity\CommunityTickerEvent')
                ->findOneBy(array(
                    'created' => $event['created'],
                    'params' => $event['params']
            ));

            if (!$existingEvent) {
                $newEvent = new CommunityTickerEvent();
                $newEvent->setEvent($event['event']);
                $newEvent->setParams($event['params'] != '[]' ? json_decode($event['params'], true) : array());
                $newEvent->setCreated($event['created']);
                $newEvent->setIsActive(true);
                if ($user) {
                    $newEvent->setUser($user);
                }

                $em->persist($newEvent);
            }
        }

        $em->flush();
        $output->writeln('<info>Data migrated to plugin table!</info>');
        $output->writeln('<info>Removing old table...</info>');
    }
}
