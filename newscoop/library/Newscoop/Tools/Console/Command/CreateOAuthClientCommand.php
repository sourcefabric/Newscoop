<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Create oauth client
 */
class CreateOAuthClientCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
            ->setName('oauth:create-client')
            ->setDescription('Create oauth2 client.')
            ->addArgument('name', InputArgument::REQUIRED, 'Client name')
            ->addArgument('publication', InputArgument::REQUIRED, 'Publication alias')
            ->addArgument('redirectUris', InputArgument::REQUIRED, 'Redirect uris')
            ->addOption('test', null, InputOption::VALUE_NONE, 'If set it will create test client with predefnied data (for automatic tests)')
            ->addOption('default', null, InputOption::VALUE_NONE, 'If set it will create default client with predefnied name');
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getApplication()->getKernel()->getContainer();
        $em = $container->getService('em');
        $clientManager = $container->get('fos_oauth_server.client_manager.default');
        $clientManager = $container->get('fos_oauth_server.client_manager.default');

        $name = $input->getArgument('name');
        $publication = $em->getRepository('\Newscoop\Entity\Aliases')
            ->findOneByName($input->getArgument('publication'))
            ->getPublication();
        $redirectUris = $input->getArgument('redirectUris');

        $client = $clientManager->createClient();
        $client->setAllowedGrantTypes(array('token', 'authorization_code', 'client_credentials', 'password'));
        $client->setRedirectUris(array($redirectUris));
        $client->setName($name);
        $client->setPublication($publication);

        if ($input->getOption('test')) {
            $client->setRandomId('svdg45ew371vtsdgd29fgvwe5v');
            $client->setSecret('h48fgsmv0due4nexjsy40jdf3sswwr');
            $client->setTrusted(true);
        }

        if ($input->getOption('default')) {
            $preferencesService = $container->get('preferences');
            $clientName = 'newscoop_'.$preferencesService->SiteSecretKey;
            $client->setName($clientName);
            $client->setTrusted(true);
        } 

        $clientManager->updateClient($client);
    }
}
