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
 * Fetch available publications Command
 */
class FetchPublicationsCommand extends ContainerAwareCommand
{
    /**
     */
    protected function configure()
    {
        $this
        ->setName('publications:fetch')
        ->setDescription('Fetch available publications');
    }

    /**
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $resourceId = new \Newscoop\Service\Resource\ResourceId(__CLASS__);
        $themeService = $resourceId->getService(IThemeManagementService::NAME_1);
        $publicationService = $this->getContainer()->getService('content.publication');
        $publications =  $publicationService->findAll();

        foreach ($themeService->getUnassignedThemes() as $theme) {
            foreach ($publications as $publication) {
                $themeService->assignTheme($theme, $publication);
            }
        }
    }
}
