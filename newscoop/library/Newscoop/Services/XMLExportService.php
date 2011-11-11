<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleAttachment.php');

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\Article;

/**
 * User service
 */
class XMLExportService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     *
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getArticles($type, $issue)
    {
        $articles = $this->em->getRepository('Newscoop\Entity\Article')
            ->findBy(array('type' => $type, 'issueId' => $issue, 'workflowStatus' => \Newscoop\Entity\Article::STATUS_PUBLISHED));

        return $articles;
    }
    
    public function getXML($type, $prefix, $articles)
    {
        $xml = new \SimpleXMLElement('<DDD></DDD>');
        
        foreach ($articles as $article) {
            $data = $this->getData($type, $article->getNumber(), $article->getLanguage()->getId());
            
            $item = $xml->addChild('DD');
            $item->addChild('DA', $article->getPublishDate());
            $item->addChild('HT', $article->getName());
            
            $attachments = \ArticleAttachment::GetAttachmentsByArticleNumber($article->getNumber());
            foreach ($attachments as $attachment) {
                $temp = explode('.', $attachment->getFileName());
                if (substr($attachment->getFileName(), 0, strlen($prefix)) == $prefix && $temp[count($temp) - 1] == 'pdf') {
                    $item->addChild('ME', 'pdf/'.$attachment->getFileName());
                }
            }
            
            try {
                $sectionName = $article->getSection()->getName();
            }
            catch(\Exception $e) {
                $sectionName = '';
            }
            
            $item->addChild('RE', $sectionName);
            $item->addChild('LD', $data['Flede']);
            $item->addChild('TX', $data['Fbody']);
            
            try {
                $creator = $article->getCreator();
            } catch (\Exception $e) {
                $creator = null;
            }
            
            if ($creator == null) {
                $item->addChild('NT', 'Online');
            }
            else {
                if ($creator->getUsername() == 'printdesk') {
                    $item->addChild('NT', 'Printed');
                }
                else {
                    $item->addChild('NT', 'Online');
                }
            }
        }
        return($xml->asXML());
    }
    
    public function getData($type, $number, $language)
    {
        $query = "select * from X".$type." where NrArticle = '".$number."' and IdLanguage = '".$language."'";
        $sql1 = mysql_query($query);
        $sql2 = mysql_fetch_assoc($sql1);
        return($sql2);
    }
    
    public function getAttachments($prefix, $articles)
    {
        $attachments = array();
        foreach ($articles as $article) {
            $temp_attachments = \ArticleAttachment::GetAttachmentsByArticleNumber($article->getNumber());
            foreach ($temp_attachments as $attachment) {
                $temp = explode('.', $attachment->getFileName());
                if (substr($attachment->getFileName(), 0, strlen($prefix)) == $prefix && $temp[count($temp) - 1] == 'pdf') {
                    $attachments[] = array(
                        'filename' => $attachment->getFileName(),
                        'location' => $attachment->getStorageLocation(),
                    );
                }
            }
        }

        return($attachments);
    }
    
    public function createArchive($directoryName, $fileName, $contents, $attachments)
    {
        if (!is_dir($directoryName)) {
            mkdir($directoryName);
        }
        
        $file = fopen($directoryName.'/'.$fileName.date('Ymd').'.xml', 'w');
        fwrite($file, $contents);
        fclose($file);
        
        $zip = new \ZipArchive();
        $zip->open($directoryName.'/'.$fileName.date('Ymd').'.zip', \ZIPARCHIVE::OVERWRITE);
        if (file_exists($directoryName.'/'.$fileName.date('Ymd').'.xml')) {
            $zip->addFile($directoryName.'/'.$fileName.date('Ymd').'.xml', $fileName.date('Ymd').'.xml');
        }

        foreach ($attachments as $attachment) {
            if (file_exists($attachment['location'])) {
                $zip->addFile($attachment['location'], 'pdf/'.$attachment['filename']);
            }
        }

        $zip->close();
    }
    
    public function upload($directoryName, $fileName, $host, $user, $password)
    {
        $connection = ftp_connect($host);
        $login = ftp_login($connection, $user, $password);
        
        ftp_pasv($connection, true);
        
        if ($connection && $login) {
            $upload = ftp_put($connection, $fileName.date('Ymd').'.zip', $directoryName.'/'.$fileName.date('Ymd').'.zip', FTP_BINARY);
        }
        
        ftp_close($connection);
    }
    
    public function clean($directoryName)
    {
        $directory = opendir($directoryName);
        while (($file = readdir($directory)) !== false) {
            if ($file != '.' && $file != '..') {
                unlink($directoryName.'/'.$file);
            }
        }
        closedir($directory);
        rmdir($directoryName);
    }
}
