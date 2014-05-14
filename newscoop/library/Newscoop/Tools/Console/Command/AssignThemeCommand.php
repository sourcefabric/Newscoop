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
use Newscoop\Service\IThemeManagementService;

/**
 * Assign themes for publication Command
 */
class AssignThemeCommand extends ContainerAwareCommand
{
    /**
     */
    protected function configure()
    {
        $this
        ->setName('themes:assign')
        ->addArgument('theme', InputArgument::OPTIONAL, 'Theme name to assign, e.g. set_quetzal', 1)
        ->setDescription('Assign theme for publications');
    }

    /**
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $resourceId = new \Newscoop\Service\Resource\ResourceId(__CLASS__);
        $themeService = $resourceId->getService(IThemeManagementService::NAME_1);
        $publicationService = $this->getContainer()->getService('content.publication');
        $publications =  $publicationService->findAll();
        $themeToSet = (string) $input->getArgument('theme');

        if ($themeToSet != null) {
            foreach ($themeService->getUnassignedThemes() as $theme) {
                if (strpos($theme->getPath(), $themeToSet) !== false) {
                    foreach ($publications as $publication) {
                        $themeService->assignTheme($theme, $publication);
                    }
                }
            }
        } else {
            foreach ($themeService->getUnassignedThemes() as $theme) {
                foreach ($publications as $publication) {
                    $themeService->assignTheme($theme, $publication);
                }
            }
        }
    }
}
