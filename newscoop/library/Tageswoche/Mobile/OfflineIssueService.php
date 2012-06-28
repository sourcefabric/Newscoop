<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Mobile;

use Newscoop\Entity\Article,
    Newscoop\Http\ClientFactory,
    Zend_View;

/**
 */
class OfflineIssueService
{
    const SERVER_URL = 'http://localhost';

    /**
     * @var Newscoop\Http\ClientFactory
     */
    private $clientFactory;

    /**
     * @var Zend_View
     */
    private $view;

    /**
     * @param Newscoop\Http\ClientFactory $clientFactory
     */
    public function __construct(ClientFactory $clientFactory, Zend_View $view)
    {
        $this->clientFactory = $clientFactory;
        $this->view = $view;
    }

    /**
     * Test if issue package is generated
     *
     * @param Newscoop\Entity\Article $issue
     * @return bool
     */
    public function isIssueGenerated(Article $issue)
    {
        return file_exists($this->getIssuePath($issue));
    }

    /**
     * Get issue path
     *
     * @param Newscoop\Entity\Article $issue
     * @return string
     */
    public function getIssuePath(Article $issue)
    {
        return sprintf('/tmp/issues/issue-%d.zip', $issue->getNumber());
    }

    /**
     * Generate offline issue
     *
     * @param Newscoop\Entity\Article $issue
     * @return void
     */
    public function generateIssue(Article $issue)
    {
        $toc = $this->fetchJson($this->getApiUrl(array(
            'controller' => 'online',
            'action' => 'toc',
            'id' => $issue->getNumber(),
        )));

        $dir = $this->getIssueDir($toc);
        foreach ($toc->articles as $article) {
            $this->generateArticle($article, $dir);
        }

        file_put_contents("$dir/toc.json", json_encode($toc));

        chdir($dir);
        system(sprintf('zip -r %s *', $this->getIssuePath($issue)));
    }

    /**
     * Generate article in given folder
     *
     * @param object $article
     * @param string $issueDir
     * @return void
     */
    public function generateArticle($article, $issueDir)
    {
        $dir = $this->getArticleDir($article, $issueDir);
        $this->generateArticleContent($article, $dir);
        if ($article->image_url) {
            $ext = pathinfo($article->image_url, PATHINFO_EXTENSION);
            file_put_contents("{$dir}/image.{$ext}", file_get_contents($article->image_url));
            $article->image_url = basename("image.{$ext}");
        }

        file_put_contents("$dir/comments.json", $this->fetch($article->comment_url));
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
        return $this->clientFactory->getClient();
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
        return $this->createDir(sprintf('/tmp/issues/issue-%d', $toc->issue_id));
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

    /**
     * Get api url
     *
     * @param array $params
     * @return string
     */
    private function getApiUrl(array $params)
    {
        return sprintf('%s/%s', 'http://localhost/newscoop/newscoop', $this->view->url($params, 'api'));
    }
}
