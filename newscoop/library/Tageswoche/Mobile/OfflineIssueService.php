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
    const RETINA = 'retina';

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
        'path' => '/tmp/issues/',
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
     * @param bool $isRetina
     * @return string
     */
    public function getIssueZipPath(Article $issue, $isRetina = false)
    {
        return $this->getPath(sprintf('issue_%d.zip', $issue->getNumber()), $isRetina);
    }

    /**
     * Get article zip path
     *
     * @param Newscoop\Entity\Article $article
     * @param bool $isRetina
     * @return string
     */
    public function getArticleZipPath($article, $isRetina = false)
    {
        return $this->getPath(sprintf('article_%d.zip', is_numeric($article) ? $article : $article->article_id), $isRetina);
    }

    /**
     * Get full path for given filename
     *
     * @param string $filename
     * @param bool $isRetina
     * @return string
     */
    private function getPath($filename, $isRetina)
    {
        return realpath($this->config['path']) . ($isRetina ? '/' . self::RETINA . '/' : '/') . $filename;
    }

    /**
     * Generate offline issue
     *
     * @param Newscoop\Entity\Article $issue
     * @return void
     */
    public function generateIssue(Article $issue)
    {
        foreach (array('', self::RETINA) as $client) {
            $toc = $this->fetchJson($this->getApiUrl(array(
                'controller' => 'online',
                'action' => 'toc',
                'id' => $issue->getNumber(),
            ), array(
                'client' => $client,
            )));

            $dir = $this->getIssueDir($toc, $client);
            foreach ($toc->articles as $article) {
                $articleDir = $this->getArticleDir($article, $dir, $client);
                $this->generateArticle($article, $articleDir, $client);
                $this->zip($articleDir, $this->getArticleZipPath($article, $client === self::RETINA));
            }

            file_put_contents("$dir/toc.json", json_encode($toc));
            $this->zip($dir, $this->getIssueZipPath($issue, $client === self::RETINA));
        }
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
        $article->comment_url = basename($dir) . '/comments.json';
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
        chdir($dir);
        $url = $article->url . sprintf('%s_secret=%s', strpos($article->url, '?') !== false ? '&' : '?', $this->config['secret']);
        system(sprintf("wget --quiet -E -H -k -p '%s'", $url));
        $parsedUrl = parse_url($url);
        $article->url = basename($dir) . '/' . $parsedUrl['host'] . $parsedUrl['path'] . '?' . $parsedUrl['query'] . '.html';
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
     * @param string $client
     * @return string
     */
    private function getIssueDir($toc, $client)
    {
        return $this->createDir($this->getPath(sprintf(
            'issue_%d',
            $toc->issue_id
        ), $client === self::RETINA));
    }

    /**
     * Get article dir
     *
     * @param object $article
     * @param object $issueDir
     * @param string $client
     * @return string
     */
    private function getArticleDir($article, $issueDir, $client)
    {
        return $this->createDir($this->getPath(sprintf(
            '%s/article_%d',
            basename($issueDir),
            $article->article_id
        ), $client === self::RETINA));
    }

    /**
     * Get api url
     *
     * @param array $params
     * @param array $queryData
     * @return string
     */
    private function getApiUrl(array $params, array $queryData = array())
    {
        return sprintf('%s/%s?%s',
            rtrim($this->config['site_url'], '/'),
            ltrim($this->view->url($params, 'api'), '/'),
            http_build_query($queryData)
        );
    }
}
