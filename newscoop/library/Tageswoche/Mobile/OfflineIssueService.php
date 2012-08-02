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
    /**
     * @var Newscoop\Http\ClientFactory
     */
    private $clientFactory;

    /**
     * @var Zend_View
     */
    private $view;

    /**
     * @var array
     */
    private $config = array(
        'site_url' => 'http://localhost/newscoop/newscoop/',
        'secret' => '',
    );

    /**
     * @param Newscoop\Http\ClientFactory $clientFactory
     * @param Zend_View $view
     * @param array $config
     */
    public function __construct(ClientFactory $clientFactory, Zend_View $view, array $config = array())
    {
        $this->clientFactory = $clientFactory;
        $this->view = $view;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Get issue path
     *
     * @param Newscoop\Entity\Article $issue
     * @return string
     */
    public function getIssueZipPath(Article $issue)
    {
        return sprintf('/tmp/issues/issue_%d.zip', $issue->getNumber());
    }

    /**
     * Get article zip path
     *
     * @param Newscoop\Entity\Article $article
     * @return string
     */
    public function getArticleZipPath($article)
    {
        return sprintf('/tmp/issues/article_%d.zip', is_numeric($article) ? $article : $article->article_id);
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
            $articleDir = $this->getArticleDir($article, $dir);
            $this->generateArticle($article, $articleDir);
            $this->zip($articleDir, $this->getArticleZipPath($article));
        }

        file_put_contents("$dir/toc.json", json_encode($toc));
        $this->zip($dir, $this->getIssueZipPath($issue));
    }

    /**
     * Zip given directory
     *
     * @param string $dir
     * @param string $desc
     * @return void
     */
    private function zip($dir, $dest)
    {
        chdir(dirname($dir));
        $command = sprintf('zip -r %s %s', $dest, basename($dir));
        `$command`; // runs without outputing
    }

    /**
     * Generate article in given folder
     *
     * @param object $article
     * @param string $dir
     * @return void
     */
    public function generateArticle($article, $dir)
    {
        $this->generateArticleContent($article, $dir);
        if ($article->image_url) {
            $ext = pathinfo($article->image_url, PATHINFO_EXTENSION);
            file_put_contents("{$dir}/image.{$ext}", $this->fetch($article->image_url));
            $article->image_url = basename($dir) . '/' . basename("image.{$ext}");
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
        $url .= sprintf('%s_secret=%s', strpos($url, '?') !== false ? '&' : '?', $this->config['secret']);
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
        return isset($this->config['auth_username'])
            ? $this->clientFactory->getAuthClient($this->config['auth_username'], $this->config['auth_password'])
            : $this->clientFactory->getClient();
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

    /**
     * Get api url
     *
     * @param array $params
     * @return string
     */
    private function getApiUrl(array $params)
    {
        return sprintf('%s/%s', rtrim($this->config['site_url'], '/'), ltrim($this->view->url($params, 'api'), '/'));
    }
}
