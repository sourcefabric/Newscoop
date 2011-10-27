<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\ArticlePopularity,
    Newscoop\Entity\Article,
    Newscoop\Entity\Language,
    Zend_Gdata,
    Zend_Gdata_Query,
    Zend_Gdata_ClientLogin,
    DOMDocument;

/**
 * Article pouplarity service
 */
class ArticlePopularityService
{
    const SITE_URL = 'http://dev.tageswoche.ch';

    const TWITTER_QUERY_URL = 'http://urls.api.twitter.com/1/urls/count.json?url=';

    const FACEBOOK_QUERY_URL = 'https://graph.facebook.com/?ids=';

    /** @var array */
    private $criteria = array(
        'unique_views' => 'getUniqueViews',
        'avg_time_on_page' => 'getAvgTimeOnPage',
        'tweets' => 'getTweets',
        'likes' => 'getLikes',
        'comments' => 'getComments',
    );

    /** @var string */
    private $uri;

    /** @var Newscoop\Entity\Article */
    private $article;

    /** @var Doctrine\ORM\EntityManager */
    private $em;


    /**
     * @param array $config
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Register a page in the popularity table
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function register(\Zend_Controller_Request_Abstract $request)
    {
        // get requested uri
        $this->uri = $request->getPathInfo();

        // get language
        $language = $this->em->getRepository('Newscoop\Entity\Language')
            ->findOneBy(array('code' => $request->getParam('language')));

        $article = $this->findArticle($request->getParam('articleNo'), $language);
        $data = array(
            'article_id' => $article->getId(),
            'language_id' => $language->getId(),
            'url' => $this->uri,
            'date' => new \DateTime,
            'unique_views' => 0,
            'avg_time_on_page' => 0,
            'tweets' => 0,
            'likes' => 0,
            'comments' => 0,
            'popularity' => 0,
        );

        $entity = $this->getRepository()->findOneBy(array(
                'article_id' => $article->getId(),
                'language_id' => $language->getId(),
        ));

        try {
            $this->save($data, $entity);
            $this->em->flush();
        } catch (\InvalidArgumentException $e) {
        }

        return $this;
    }

    /**
     * Save article popularity record
     *
     * @param array $data
     * @param Newscoop\Entity\ArticlePopularity $popularity
     * @return Newscoop\Entity\ArticlePopularity
     */
    public function save(array $data, ArticlePopularity $entity = null)
    {
        if ($entity === null) {
            $entity = new ArticlePopularity;
        }

        if ($this->getRepository()->exists($entity) && $entity->getURL() == $data['url']) {
            return;
        }

        $this->getRepository()->save($entity, $data);
        return $entity;
    }

    /**
     * Update an entry
     *
     * @param Newscoop\Entity\ArticlePopularity $entry
     * @return void
     */
    public function update(ArticlePopularity $entry)
    {
        $url = $entry->getURL();
        $ga = $this->fetchGAData($url);
        $data = array(
            'unique_views' => $ga['ga:uniquePageviews'],
            'avg_time_on_page' => $ga['ga:avgTimeOnPage'],
            'tweets' => $this->fetchTweets($url),
            'likes' => $this->fetchLikes($url),
            'comments' => $this->fetchComments($entry),
        );

        try {
            $this->getRepository()->save($entry, $data);
        } catch (\InvalidArgumentException $e) {
        }
    }

    /**
     * Update all entries
     *
     * @return void
     */
    public function updateMetrics()
    {
        $entries = $this->getRepository()->findAll();
        if (!is_array($entries)) {
            $entries = array();
        }

        foreach($entries as $entry) {
            $response = $this->ping($entry->getURL());
            if (!$response->isSuccessful()) {
		continue;
            }

            $this->update($entry);
        }
        $this->em->flush();
    }

    /**
     * @param Newscoop\Entity\ArticlePopularity $entity
     * @param array $maxs
     * @return float
     */
    public function computeRanking(ArticlePopularity $entity, array $maxs)
    {
        $values = array();
        foreach($this->criteria as $criterion => $getter) {
            if (isset($maxs[$criterion]) && $maxs[$criterion] <= 0) {
                $values[$criterion] = 0;
                continue;
            }

            $values[$criterion] = ($entity->$getter() / $maxs[$criterion]) * 100;
        }

        $popularity = array_sum($values);

        return $popularity;
    }

    /**
     * Computes the article priority for all registered articles.
     *
     * @return void
     */
    public function updateRanking()
    {
        $maxs = $this->getRepository()->findMax($this->criteria);

        $entries = $this->getRepository()->findAll();
        if (!is_array($entries)) {
            $entries = array();
        }

        $data = array();
        foreach($entries as $entry) {
            $data['popularity'] = $this->computeRanking($entry, $maxs);

            try {
                $this->getRepository()->save($entry, $data);
            } catch (\InvalidArgumentException $e) {
            }
        }

        $this->em->flush();
    }

    /**
     * Find an article
     *
     * @param int $number
     * @param Newscoop\Entity\Language $language
     * @return Newscoop\Entity\Article
     */
    public function findArticle($number, Language $language)
    {
        // get article
        $article = $this->em->getRepository('Newscoop\Entity\Article')
            ->findOneBy(array('language' => $language->getId(), 'number' => $number));

        if (empty($article)) {
            return null;
        }

        return $article;
    }

    /**
     * Read google analytics metrics 
     *
     * @param string $uri 
     * @return array 
     */
    public function fetchGAData($uri)
    {
        $email = 'analytics@tageswoche.ch';
        $pass = 'sourcefabric';
        $client = Zend_Gdata_ClientLogin::getHttpClient($email, $pass, 'analytics');
        $gdClient = new Zend_Gdata($client);
        $gdClient->useObjectMapping(false);

        try {
            $dimensions = array('ga:pagePath');
            $metrics = array(
                'ga:uniquePageviews',
                'ga:bounces',
                'ga:exits',
                'ga:timeOnPage',
                'ga:avgTimeOnPage'
            );
            $reportURL = 'https://www.google.com/analytics/feeds/data?ids=ga:48980166&dimensions=' . @implode(',', $dimensions) . '&metrics=' . @implode(',', $metrics) . '&start-date=2011-07-01&end-date=2011-09-20&filters=ga:pagePath%3D%3D' . urlencode($uri);
            $xml = $gdClient->getFeed($reportURL);

            $dom = new DOMDocument();
            $dom->loadXML($xml); 
            $entries = $dom->getElementsByTagName('entry');
            $data = array();
            foreach($entries as $entry) {
                foreach($entry->getElementsByTagName('metric') as $metric) {
                    $data[$metric->getAttribute('name')] = $metric->getAttribute('value');
                }
            }
        } catch (\Zend_Exception $e) {
            echo 'Caught exception: ' . get_class($e) . "\n"; echo 'Message: ' . $e->getMessage() . "\n";
        }

        return $data;
    }

    /**
     * Read re-tweets
     *
     * @param string $uri
     * @return int
     */
    public function fetchTweets($uri)
    {
        $tdata = file_get_contents(self::TWITTER_QUERY_URL . self::SITE_URL . $uri);
        $tdata = json_decode($tdata);
        return (int) $tdata->count;
    }

    /**
     * Read facebook likes
     *
     * @param string $uri
     * @return int
     */
    public function fetchLikes($uri)
    {
        $page = self::SITE_URL . $uri;
        $fdata = file_get_contents(self::FACEBOOK_QUERY_URL . $page);
        $fdata = json_decode($fdata);
        return isset($fdata->$page->shares) ? (int) $fdata->$page->shares : 0;
    }

    /**
     * Read number of comments
     *
     * @return int
     */
    public function fetchComments(ArticlePopularity $entry)
    {
        $comments = 0;
        $article = $this->getRepository()->getArticle($entry);
        if ($article->commentsEnabled()) {
            $commentsRepository = $this->em->getRepository('Newscoop\Entity\Comment');
            $filter = array( 'thread' => $entry->getArticleId(), 'language' => $entry->getLanguageId());
            $params = array( 'sFilter' => $filter);
            $comments = $commentsRepository->getCount($params);
        }

        return (int) $comments;
    }

    /**
     * Ping the given URL
     *
     * @param string $uri
     * @return Zend_Http_Response
     */
    public function ping($uri)
    {
        $uri = self::SITE_URL . $uri;
	$client = new \Zend_Http_Client($uri);
        return $client->request();
    }

    /**
     * Find by given criteria
     *
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = array(), $limit = 25, $offset = 0)
    {
        return $this->getRepository()
            ->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * Get repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\ArticlePopularity');
    }
}
