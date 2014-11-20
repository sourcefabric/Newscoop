<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;

/**
 * Update Image Storage Command
 */
class ClearOldStatisticsCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('newscoop:statistics:clean-old')
            ->setDescription('Remove old statistics from database')
            ->setHelp('Find and remove old session and request logs from database.');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $em = $this->getApplication()->getKernel()->getContainer()->getService('em');
        $sessionDiff = 3600 * 48;
        $requestTimeDiff = 3600;

        // select sections older than 48 hours
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult('\Newscoop\Entity\Session', 's');
        $rsm->addFieldResult('s', 'id', 'id');
        $rsm->addFieldResult('s', 'start_time', 'start_time');
        $rsm->addFieldResult('s', 'user_id', 'user_id');
        $rsm->addJoinedEntityResult('\Newscoop\Entity\Request', 'r', 's', null);
        $rsm->addFieldResult('r', 'last_stats_update', 'last_stats_update');
        $rsm->addFieldResult('r', 'session_id', 'session');
        $rsm->addScalarResult('last_update_diff', 'last_update_diff');

        $sql = "SELECT
                    s.id,
                    s.start_time,
                    s.user_id,
                    MIN(TIME_TO_SEC(TIMEDIFF(NOW(), r.last_stats_update))) AS last_update_diff
                FROM
                    Sessions s
                LEFT JOIN
                    Requests r ON s.id = r.session_id
                WHERE
                    TIME_TO_SEC(TIMEDIFF(NOW(), s.start_time)) >= $sessionDiff
        ";
        $query = $em->createNativeQuery($sql, $rsm);
        $sessions = $query->getResult();

        if (count($sessions) == 0 || $sessions[0][0] == null) {
            $output->writeln('<error>There is nothing to remove.</error>');

            return;
        }

        foreach ($sessions as $session) {
            if ($session['last_update_diff'] < $requestTimeDiff) {
                // if there was a request for this session less than one hour ago do not process the session
                continue;
            }

            foreach ($session[0]->getRequests() as $request) {
                $em->remove($request);
                $output->writeln('<info>Request for session with id: '.$session[0]->getId().' was removed.</info>');
            }

            $em->remove($session[0]);
            $output->writeln('<info>Session with id: '.$session[0]->getId().' was removed.</info>');
        }

        $em->flush();

        return true;
    }
}
