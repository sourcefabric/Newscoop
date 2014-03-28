<?php
/**
 * @package   Newscoop
 * @author    Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Removes obsolete pending users data
 */
class UserGarbageCollectionCommand extends ContainerAwareCommand
{
    /**
     */
    protected function configure()
    {
        $this
        ->setName('user:garbage')
        ->setDescription('Users Garbage Collection')
        ->setHelp("Removes obsolete pending users data");
    }

    /**
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $systemPreferences = $this->getContainer()->getService('system_preferences_service');

        if ($systemPreferences->get('userGarbageActive') === 'Y' && !is_null($systemPreferences->get('userGarbageActive'))) {
            $this->getContainer()->getService('user.garbage')->run($systemPreferences->get('userGarbageDays'));

            if ($input->getOption('verbose')) {
                $output->writeln('<info>Obsolete pending users successfuly removed.</info>');
            }
        }
    }
}
