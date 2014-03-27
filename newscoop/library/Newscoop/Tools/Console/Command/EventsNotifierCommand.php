<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;
use Newscoop\Services\FilesystemService;

/**
 * Send events notifications command
 */
class EventsNotifierCommand extends Console\Command\Command
{
    private $em;

    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('newscoop:notifier:events')
        ->setDescription('Send events notifications');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $this->em = $this->getApplication()->getKernel()->getContainer()->get('doctrine')->getManager();

        $etcDirectory = APPLICATION_PATH . '/../conf/';
        $notifierTemplate = '_events_notifier.tpl';
        $message = array();

        if (!FilesystemService::isReadable($etcDirectory . '/install_conf.php')) {
            exit;
        }

        // includes installation configuration file
        require_once($etcDirectory . '/install_conf.php');
        // includes campsite initialisation
        require_once(APPLICATION_PATH . '/../include/campsite_init.php');

        if (!is_file($etcDirectory . '/database_conf.php')) {
            $output->writeln('Database configuration file is missing!');

            return;
        }

        $message['reply'] = $this->getReplyAddress();
        $autoId = $this->getAutoId();
        $logTimestamp = $autoId->getLogTimestamp();

        $logs = $this->em->getRepository('Newscoop\Entity\Log')
            ->createQueryBuilder('l')
            ->select('l, e, u')
            ->leftJoin('l.eventId', 'e')
            ->leftJoin('l.userId', 'u')
            ->where('l.eventId = e.id')
            ->andWhere('l.userId = u.id')
            ->andWhere('e.notify = :notify')
            ->andWhere('l.created > :logTimestamp')
            ->getQuery()
            ->setParameters(array(
                'logTimestamp' => $logTimestamp,
                'notify' => 'Y'
            ))
            ->getResult();

        if (count($logs) == 0) {
            return false;
        }

        $tpl = $this->initSmarty();
        $recipients = $this->getApplication()->getKernel()->getContainer()->getService('notification')->findRecipients();
        $lastTimestamp = null;

        if ($input->getOption('verbose')) {
            $output->writeln('<info>Number of found logs: ' . count($logs). '.<info>');
        }

        foreach ($logs as $log) {
            $lastTimestamp = $log->getCreated();

            $tpl->assign('user_real_name', $log->getUser()->getFirstName());
            $tpl->assign('user_name', $log->getUser()->getUsername());
            $tpl->assign('user_email', $log->getUser()->getEmail());
            $tpl->assign('event_text', $log->getMessage());
            $tpl->assign('event_timestamp', $log->getCreated()->format('Y-m-d h:i:s'));

            $message['text'] = $tpl->fetch($notifierTemplate);

            if (count($recipients) <= 0) {
                if ($input->getOption('verbose')) {
                    $output->writeln('<error>There is no recipients.<error>');
                }

                continue;
            }

            $message['recipients'] = $recipients;
            $message['subject'] = $log->getEvent()->getName();

            $this->sendEmail($message);

            if ($input->getOption('verbose')) {
                $output->writeln('<info>Send message for event: ' . $log->getEvent()->getName() . '.<info>');
            }
        }

        if ($lastTimestamp != null) {
            $autoId->setLogTimestamp($lastTimestamp);
        }

        $this->em->flush();
    }

    /**
     * Reads reply address
     *
     * @return string
     */
    private function getReplyAddress()
    {
        $adminUser = $this->em->getRepository('Newscoop\Entity\User')
            ->findOneByUsername('admin');

        if (!$adminUser) {
            return false;
        }

        return $adminUser->getEmail();
    }

    /**
     * @return string
     */
    private function getAutoId()
    {
        $autoId = $this->em->getRepository('Newscoop\Entity\AutoId')
            ->createQueryBuilder('a')
            ->getQuery()
            ->getSingleResult();

        return $autoId;
    }

    /**
     * @return object $tpl Smarty object
     */
    private function initSmarty()
    {
        $tpl = new \Smarty();

        // inits smarty configuration settings
        $tpl->left_delimiter = '{{';
        $tpl->right_delimiter = '}}';
        $tpl->force_compile = true;
        $tpl->config_dir = APPLICATION_PATH . '/../include/smarty/configs';
        $tpl->template_dir = APPLICATION_PATH . '/../themes/system_templates';
        $tpl->compile_dir = APPLICATION_PATH . '/../cache';
        $tpl->auto_literal = false;

        return $tpl;
    }

    /**
     * @return boolean true on success, false on failure
     */
    private function sendEmail($message)
    {
        if (!is_array($message) || empty($message)) {
            return false;
        }

        $mail = new \Zend_Mail('utf-8');
        $mail->addTo($message['recipients']);
        $mail->setSubject($message['subject']);
        $mail->setBodyText($message['text']);

        if (!empty($message['reply'])) {
            $mail->setReplyTo($message['reply']);
        }

        return $mail->send();
    }
}
