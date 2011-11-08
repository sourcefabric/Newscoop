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
    private $articleType = 'news';
    private $directoryName = 'temp';
    private $fileName = '';
    private $ftp = array('host' => 'anlieferung.smd.ch', 'username' => 'ftp.tagesWoche', 'password' => '32466tagesWoche');
    private $time = 999999999;
    
    /**
     * @see Console\Command\Command
     */
    protected function configure()
    {
        $this
        ->setName('XML:exportSMD')
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
        $this->fileName = 'tageswoche_'.date('Ymd');
        $this->time = 7*24*60*60;
        
        $contents = array();
        $attachments = array();
        
        $xmlExportService = $this->getHelper('container')->getService('XMLExport');
        
        $articles = $xmlExportService->getArticles($this->articleType, $this->time);
        
        $contents = $xmlExportService->getXML($this->articleType, $articles);
        $attachments = $xmlExportService->getAttachments($articles);
        
        $xmlExportService->createArchive($this->directoryName, $this->fileName, $contents, $attachments);
        
        $xmlExportService->upload($this->directoryName, $this->fileName, $this->ftp['host'], $this->ftp['username'], $this->ftp['password']);
        $xmlExportService->clean($this->directoryName);
    }
}
