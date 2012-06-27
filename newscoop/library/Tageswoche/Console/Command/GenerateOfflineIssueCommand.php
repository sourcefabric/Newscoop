<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Console\Command;

use Symfony\Component\Console\Input\InputArgument,
    Symfony\Component\Console\Input\InputOption,
    Symfony\Component\Console;

/**
 */
class GenerateOfflineIssueCommand extends Console\Command\Command
{
    /**
     */
    protected function configure()
    {
        $this->setName('oi:generate')
            ->setDescription('Generate offline issue');
    }

    /**
     */
    protected function execute($input, $output)
    {
        $toc = $this->fetchJson('http://localhost/newscoop/newscoop/api/issues/101/toc');
        $dir = $this->getIssueDir($toc);
        foreach ($toc->articles as $article) {
            $this->generateArticle($article, $dir);
        }

        file_put_contents("$dir/toc.json", json_encode($toc));
    }

    /**
     * Generate article in given folder
     *
     * @param object $article
     * @param string $issueDir
     * @return void
     */
    private function generateArticle($article, $issueDir)
    {
        $dir = $this->getArticleDir($article, $issueDir);
        $this->generateArticleContent($article, $dir);
    }

    /**
     * Generate article content in given dir
     *
     * @param object $article
     * @param string $dir
     * @return void
     */
    private function generateArticleContent($article, $dir)
    {
        $dest = "$dir/content.html";
        file_put_contents("$dir/content.html", $this->fetch($article->url));
        $article->url = basename(dirname($dest)) . '/' . basename($dest);
        touch("$dir/style.css");
    }

    /**
     * Fetch content from given url
     *
     * @param string $url
     * @return string
     */
    private function fetch($url)
    {
        $client = $this->getClient();
        $response = $client->get($url)->send();
        return $response->isSuccessful() ? $response->getBody(TRUE) : null;
    }

    /**
     * Fetch json from given url
     *
     * @param string $url
     * @return object
     */
    private function fetchJson($url)
    {
        $data = $this->fetch($url);
        return !empty($data) ? json_decode($data) : null;
    }

    /**
     * Get http client
     *
     * @return object
     */
    private function getClient()
    {
        return $this->getHelper('container')->getService('http.client_factory')->getClient();
    }

    /**
     * Generate dir if not exists
     *
     * @param string $path
     * @return string
     */
    private function createDir($path)
    {
        if (!is_dir($path) && is_file($path)) {
            throw new \InvalidArgumentException("'$path' is a file.");
        }

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        return $path;
    }

    /**
     * Get issue dir
     *
     * @param object $toc
     * @return string
     */
    private function getIssueDir($toc)
    {
        return $this->createDir(sprintf('/tmp/issues/issue_%d', $toc->issue_id));
    }

    /**
     * Get article dir
     *
     * @param object $article
     * @param object $issueDir
     * @return string
     */
    private function getArticleDir($article, $issueDir)
    {
        return $this->createDir(sprintf('%s/article_%d', $issueDir, $article->article_id));
    }
}
