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
 * Send subscriptions notifications command
 */
class SubscriptionsNotifierCommand extends Console\Command\Command
{
    private $em;

    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('newscoop:notifier:subscriptions')
        ->setDescription('Send subscriptions notifications');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $this->em = $this->getApplication()->getKernel()->getContainer()->getService('em');

        $etcDirectory = APPLICATION_PATH . '/../conf/';
        $notifierTemplate = '_subscription_notifier.tpl';
        $message = array();
        $notifiedIndex = false;

        if (!FilesystemService::isReadable($etcDirectory . '/install_conf.php')) {
            exit;
        }

        require_once($etcDirectory . '/install_conf.php');
        require_once(APPLICATION_PATH . '/../include/campsite_init.php');

        if (!is_file($etcDirectory . '/database_conf.php')) {
            $output->writeln('Database configuration file is missed!');

            return;
        }

        $message['reply'] = $this->getReplyAddress();
        $endingSubscriptions = $this->getEndingSubscriptions();

        if (count($endingSubscriptions) == 0) {
            if ($input->getOption('verbose')) {
                $output->writeln('<info>There is no ending subscriptions.<info>');
            }

            return;
        }

        $tpl = $this->initSmarty();
        $notifiedIndex = 0;
        foreach ($endingSubscriptions as $subscription) {
            $issueMaxNumber = $this->getIssueMaxNumber($subscription->getPublication()->getId(), $subscription->getPublication()->getDefaultLanguage()->getId());
            $subscriptionSectionToUpdate = $this->em->getRepository('Newscoop\Subscription\Section')
                ->createQueryBuilder('ss')
                ->where('ss.subscription = :subscription')
                ->setParameter('subscription', $subscription->getId());

            if ($issueMaxNumber === false) {
                if ($input->getOption('verbose')) {
                    $output->writeln('<info>There is o issues for publication default language.<info>');
                }

                return;
            }

            $sectionsCount = $this->getSectionCounts($subscription, $issueMaxNumber);
            $subscritpionsSections = $this->getSubscritpionsSections($subscription->getId());
            if ($subscritpionsSections <= 0) {
                continue;
            }

            $text = '';
            $notify = false;
            $subsSections = 0;
            $counter = 0;
            $sections = '';

            foreach ($subscritpionsSections as $subscriptionSection) {
                $startDate = $subscriptionSection[0]->getStartDate();
                $formatedStartDate = $subscriptionSection['formated_start_date'];
                $paidDays = $subscriptionSection[0]->getPaidDays();
                $toDaysStartDate = $subscriptionSection['to_days_start_date'];
                $toDaysNow = $subscriptionSection['to_days_now'];
                $formatedEndDate = $subscriptionSection['formated_end_date'];

                if ($toDaysNow > ($paidDays + $toDaysStartDate)) {
                    continue;
                }

                $remainedDays = $paidDays + $toDaysStartDate - $toDaysNow;
                if ($remainedDays > 14 || $remainedDays <= 0) {
                    continue;
                }

                $notify = true;
                if (count($subscritpionsSections) == 1) {
                    $subSectionsCount = $this->em->getRepository('Newscoop\Subscription\Section')
                        ->createQueryBuilder('ss')
                        ->select('ss')
                        ->where('ss.subscription = :subscription')
                        ->andWhere('ss.noticeSent = :noticeSent')
                        ->andWhere('ss.startDate = :startDate')
                        ->andWhere('ss.paidDays = :paidDays')
                        ->getQuery()
                        ->setParameters(array(
                            'subscription' =>$subscription->getId(),
                            'noticeSent' => 'N',
                            'startDate' => $startDate,
                            'paidDays' => $paidDays
                        ))
                        ->getResult();
                }

                if ($counter == 0) {
                    $subsType = ($subscription->getType() == 'P') ? 'paid' : 'trial';
                    $tpl->assign('user_title', $subscription->getUser()->getFirstName());
                    $tpl->assign('user_name', $subscription->getUser()->getFirstName());
                    $tpl->assign('subs_type', $subsType);
                    $tpl->assign('subs_date', $formatedStartDate);
                    $tpl->assign('publication_name', $subscription->getPublication()->getName());
                }

                if ($subsSections == $sectionsCount && count($subscritpionsSections) == 1) {
                    $tpl->assign('subs_expire', 1);
                    $tpl->assign('subs_expire_date', $formatedEndDate);
                    $tpl->assign('subs_remained_days', $remainedDays);
                } else {
                    $sectionData = $this->em->getRepository('Newscoop\Entity\Section')
                        ->createQueryBuilder('s')
                        ->select('s.name, s.number')
                        ->from('Newscoop\Subscription\Section', 'ss')
                        ->andWhere('ss.subscription = :subscription')
                        ->andWhere('ss.noticeSent = :noticeSent')
                        ->andWhere('ss.startDate = :startDate')
                        ->andWhere('ss.paidDays = :paidDays')
                        ->andWhere('s.publication = :publication')
                        ->andWhere('s.issue = :issue')
                        ->andWhere('s.language = :language')
                        ->andWhere('s.number = ss.sectionNumber')
                        ->getQuery()
                        ->setParameters(array(
                            'subscription' =>$subscription->getId(),
                            'noticeSent' => 'N',
                            'startDate' => $startDate,
                            'paidDays' => $paidDays,
                            'publication' => $subscription->getPublication()->getId(),
                            'issue' => $issueMaxNumber,
                            'language' => $subscription->getPublication()->getDefaultLanguage()
                        ))
                        ->getResult();

                    if ($counter == 0) {
                        $tpl->assign('subs_expire_plan', 1);
                    }

                    $expirePlan = '\t- ';
                    $isFirst = true;

                    foreach ($sectionData as $key => $section) {
                        if (!$isFirst) {
                            $expirePlan .= ', ';
                        } else {
                            $isFirst = false;
                        }

                        $subscriptionSectionToUpdate->orWhere('ss.section = :sectionNumber_'.$key);
                        $subscriptionSectionToUpdate->setParameter('sectionNumber_'.$key, $section['number']);

                        $expirePlan .= '"' . $section['name'] . '"';
                    }

                    $tpl->assign('expire_plan', $expirePlan);

                    $tpl->assign('subs_expire_date', $formatedEndDate);
                    $tpl->assign('subs_remained_days', $remainedDays);
                    $tpl->assign('subs_start_date', $formatedStartDate);

                    $counter++;
                }
            }

            $tpl->assign('site', $subscription->getPublication()->getDefaultAlias()->getName());
            if (!$notify) {
                continue;
            }

            $message['recipients'] = array($subscription->getUser()->getEmail());
            $message['text'] = $tpl->fetch($notifierTemplate);
            $message['subject'] = 'Subscription to ' . $subscription->getPublication()->getName();

            if ($this->sendEmail($message) == false) {
                continue;
            }

            $subscriptionSectionToUpdate = $subscriptionSectionToUpdate->getQuery()->getResult();

            foreach ($subscriptionSectionToUpdate as $updateMe) {
                $updateMe->setNoticeSent('Y');
            }
            $this->em->flush();

            $notifiedIndex++;
        }

        if ($notifiedIndex > 0) {
            if ($input->getOption('verbose')) {
                $output->writeln('<info>'.$notifiedIndex . ' user(s) notified.<info>');
            }
        }
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
     * Get ending subscriptions
     * @return Newscoop\Subscription\Subscription
     */
    private function getEndingSubscriptions()
    {
        $subscriptions = $this->em->getRepository('Newscoop\Subscription\Subscription')
            ->createQueryBuilder('s')
            ->select('s, p, u, a')
            ->leftJoin('s.publication', 'p')
            ->leftJoin('s.user', 'u')
            ->leftJoin('p.defaultAlias', 'a')
            ->where('s.active = :active')
            ->andWhere('s.toPay = :toPay')
            ->getQuery()
            ->setParameters(array(
                'active' => 'Y',
                'toPay' => '0.00'
            ))
            ->getResult();

        return $subscriptions;
    }

    private function getIssueMaxNumber($publicationId, $languageId)
    {
        $issue = $this->em->getRepository('Newscoop\Entity\Issue')
            ->createQueryBuilder('i')
            ->select('MAX(i.number) AS MaxNumber')
            ->where('i.publication = :publication')
            ->andWhere('i.language = :language')
            ->andWhere('i.workflowStatus = :workflowStatus')
            ->getQuery()
            ->setParameters(array(
                'publication' => $publicationId,
                'language' => $languageId,
                'workflowStatus' => 'Y'
            ))
            ->getScalarResult();

        if (count($issue) > 0) {
            return $issue[0]['MaxNumber'];
        }

        return false;
    }

    private function getSectionCounts($subscription, $issueMaxNumber)
    {
        $sectionsCount = $this->em->getRepository('Newscoop\Entity\Section')
            ->createQueryBuilder('s')
            ->select('COUNT(s) AS numberOfSections')
            ->where('s.publication = :publicationId')
            ->andWhere('s.issue = :issueId')
            ->andWhere('s.language = :language')
            ->setParameters(array(
                'publicationId' => $subscription->getPublication()->getId(),
                'issueId' => $issueMaxNumber,
                'language' => $subscription->getPublication()->getDefaultLanguage()->getId()
            ))
            ->getQuery()
            ->getScalarResult();

        return $sectionsCount;
    }

    private function getSubscritpionsSections($subscriptionId)
    {
        $sectionsCount = $this->em->getRepository('Newscoop\Subscription\Section')
            ->createQueryBuilder('s');

        // select sections older than 48 hours
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping();
        $rsm->addEntityResult('\Newscoop\Subscription\Section', 'ss');
        $rsm->addFieldResult('ss', 'StartDate', 'startDate');
        $rsm->addFieldResult('ss', 'PaidDays', 'paidDays');
        $rsm->addFieldResult('ss', 'IdSubscription', 'subscription');
        $rsm->addFieldResult('ss', 'NoticeSent', 'noticeSent');
        $rsm->addScalarResult('formated_start_date', 'formated_start_date');
        $rsm->addScalarResult('to_days_start_date', 'to_days_start_date');
        $rsm->addScalarResult('to_days_now', 'to_days_now');
        $rsm->addScalarResult('formated_end_date', 'formated_end_date');

        $sql = "SELECT 
                    ss.StartDate, 
                    DATE_FORMAT(ss.StartDate, '%M %D, %Y') AS formated_start_date, 
                    ss.PaidDays, 
                    TO_DAYS(ss.StartDate) AS to_days_start_date, 
                    TO_DAYS(now()) AS to_days_now, 
                    DATE_FORMAT(ADDDATE(ss.StartDate, INTERVAL ss.PaidDays DAY), '%M %D, %Y') AS formated_end_date
                FROM 
                    SubsSections ss
                WHERE 
                    ss.IdSubscription =  $subscriptionId 
                AND 
                    ss.NoticeSent = 'N' 
                GROUP BY 
                    ss.StartDate, ss.PaidDays
        ";

        $query = $this->em->createNativeQuery($sql, $rsm);

        return $query->getResult();
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

        return $tpl;
    }

    /**
     * Send notify email
     *
     * @param array $message
     *
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
