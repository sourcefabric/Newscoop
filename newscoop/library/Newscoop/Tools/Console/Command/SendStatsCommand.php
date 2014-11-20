<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console;

/**
 * Send stats command
 */
class SendStatsCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('newscoop:stats:send')
        ->setDescription('Sends stats')
        ->setHelp(<<<EOT
Sends stats
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {   
        $preferencesService = \Zend_Registry::get('container')->getService('system_preferences_service');
        $supportSend = $preferencesService->support_send;
        if ($supportSend) {
            $stats = $this->getApplication()->getKernel()->getContainer()->getService('stat')->getAll();
            
            $statsUrl = 'http://stat.sourcefabric.org';
            $parameters = array('p' => 'newscoop');
            $parameters['installation_id'] = $stats['installationId'];
            $parameters['server'] = $preferencesService->support_stats_server;
            $parameters['ip_address'] = $preferencesService->support_stats_ip_address;
            $parameters['ram_used'] = $stats['ramUsed'];
            $parameters['ram_total'] = $preferencesService->support_stats_ram_total;
            $parameters['version'] = $stats['version'];
            $parameters['install_method'] = $stats['installMethod'];
            $parameters['publications'] = $stats['publications'];
            $parameters['issues'] = $stats['issues'];
            $parameters['sections'] = $stats['sections'];
            $parameters['articles'] = $stats['articles'];
            $parameters['articles_published'] = $stats['articlesPublished'];
            $parameters['languages'] = $stats['languages'];
            $parameters['authors'] = $stats['authors'];
            $parameters['subscribers'] = $stats['subscribers'];
            $parameters['backend_users'] = $stats['backendUsers'];
            $parameters['images'] = $stats['images'];
            $parameters['attachments'] = $stats['attachments'];
            $parameters['topics'] = $stats['topics'];
            $parameters['comments'] = $stats['comments'];
            $parameters['hits'] = $stats['hits'];
            
            $client = new \Zend_Http_Client();
            $client->setUri($statsUrl);
            $client->setParameterPost($parameters);
            $client->request('POST');
        }
    }
}