<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Tools\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 * XML export to SMD command
 */
class XMLExportSMDCommand extends Console\Command\Command
{
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('XMLExportSMD')
        ->setDescription('Export XML files to SMD.')
        ->setHelp(<<<EOT
Export XML files to SMD.
EOT
        );
    }

    /**
     * @see Console\Command\Command
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $configuration = parse_ini_file(APPLICATION_PATH . '/configs/XMLExportSMD.ini');
        
        $contents = array();
        $attachments = array();
        
        $xmlExportService = $this->getHelper('container')->getService('XMLExport');
        
        $articles = $xmlExportService->getArticles($configuration['articleType'], $configuration['issue']);
        
        $contents = $xmlExportService->getXML($configuration['articleType'], $configuration['attachmentPrefix'], $articles);
        
        $attachments = $xmlExportService->getAttachments($configuration['attachmentPrefix'], $articles);
        
        $xmlExportService->createArchive($configuration['directoryName'], $configuration['fileName'], $contents, $attachments);
        $xmlExportService->upload($configuration['directoryName'], $configuration['fileName'], $configuration['ftpHost'], $configuration['ftpUsername'], $configuration['ftpPassword']);
        $xmlExportService->clean($configuration['directoryName']);
    }
}
