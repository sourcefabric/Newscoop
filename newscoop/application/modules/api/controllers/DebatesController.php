<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Article;

require_once __DIR__ . '/AbstractController.php';

/**
 * Api Debates service controller.
 */
class Api_DebatesController extends AbstractController
{
    const IMAGE_RENDITION = 'dossierteaser';

    /**
     * @var array
     */
    private $answers = array(
        'yes' => 1,
        'no' => 2,
    );

    /**
     * @var int
     */
    private $rank = 1;

    /**
     * @var array
     */
    private $stages = array(
        'date_openinig',
        'date_rebuttal',
        'date_final',
        'date_closing',
    );

    /**
     * Returns list of debates.
     *
     * @return json
     */
    public function listAction()
    {
        $response = array();
        foreach ($this->_helper->service('debate')->findAllPublished() as $debate) {
            list($pro, $con) = $this->_helper->service('debate')->getVotes($debate);
            $response[] = array(
                'debate_id' => $debate->getNumber(),
                'url' => $this->view->serverUrl($this->view->url(array(
                    'module' => 'api',
                    'controller' => 'debates',
                    'action' => 'index',
                )) . $this->getApiQueryString(array(
                    'debate_id' => $debate->getNumber(),
                ))),
                'title' => $debate->getTitle(),
                'image_url' => $this->getRenditionUrl($debate, 'rubrikenseite', array(105, 70), array(210, 140)),
                'total_pro_votes' => $pro,
                'total_con_votes' => $con,
                'comment_count' => $this->getCommentsCount($debate),
                'published' => $this->formatDate($this->getPublishDate($debate)),
                'year' => (int) $this->getPublishDate($debate)->format('Y'),
                'month' => (int) $this->getPublishDate($debate)->format('n'),
                'week' => (int) $this->getPublishDate($debate)->format('W'),
                'rank' => $this->rank++,
            );
        }

        $this->_helper->json($response);
    }

    public function indexAction()
    {
        if ($this->_getParam('debate_id') === null) {
            $this->sendError('Missing debate_id param.', 400);
        }

        $debate = $this->_helper->service('debate')->findPublished($this->_getParam('debate_id'));
        if (empty($debate)) {
            $this->sendError('Debate not found.', 404);
        }

        list($pro, $con) = $this->_helper->service('debate')->getVotes($debate);
        $this->_helper->json(array(
            'debate_id' => $debate->getNumber(),
            'title' => $debate->getTitle(),
            'image_url' => $this->getRenditionUrl($debate, 'topfront', array(320, 140), array(640, 280)),
            'my_vote' => null,
            'website_url' => $this->getWebsiteUrl($debate),
            'start_date' => $this->getArticleField($debate, 'date_opening'),
            'intro_url' => $this->getWebsiteUrl($debate) . '?stage=0',
            'conclusion_url' => $this->getWebsiteUrl($debate) . '?stage=3',
            'conclusion_date' => $this->getArticleField($debate, 'date_closing'),
            'comment_count' => $this->getCommentsCount($debate),
            'total_pro_votes_percent' => $pro,
            'total_con_votes_percent' => $con,
            'current_stage' => $this->getCurrentStage($debate),
            'pro' => $this->getPro($debate),
            'con' => $this->getCon($debate),
            'stages' => $this->getStages($debate),
        ));
    }

    public function currentAction()
    {
        $this->getRequest()->setParam('debate_id', (string) $this->_helper->service('debate')->findCurrentDebateId());
        $this->_forward('index');
    }

    /**
     * Gets the debate publish date as DateTime object.
     *
     * @param Newscoop\Entity\Article $article
     * @return DateTime
     */
    private function getPublishDate(Article $article)
    {
        $date = $article->getData('date_opening');
        return new DateTime($date);
    }

    /**
     * Get current stage of a debate
     *
     * @param Newscoop\Entity\Article $debate
     * @return int
     */
    protected function getCurrentStage(Article $debate)
    {
        $nowDateTime = new DateTime('now');
        $now = $nowDateTime->format('Y-m-d');

        for ($i = 1; $i < count($this->stages); $i++) {
            if ($now >= $this->getArticleField($debate, $this->stages[$i - 1])
                && $now < $this->getArticleField($debate, $this->stages[$i])) {
                return $i;
            }
        }
    }

    /**
     * Get pro object
     *
     * @param Newscoop\Entity\Article $debate
     * @return array
     */
    protected function getPro(Article $debate)
    {
        $statement = $this->_helper->service('debate')->findStatement($debate, true);
        return $this->getAuthorInfo($statement);
    }

    /**
     * Get con object
     *
     * @param Newscoop\Entity\Article $debate
     * @return array
     */
    protected function getCon(Article $debate)
    {
        $statement = $this->_helper->service('debate')->findStatement($debate, false);
        return $this->getAuthorInfo($statement);
    }

    /**
     * Get author info
     *
     * @param Newscoop\Entity\Article $article
     * @return array
     */
    protected function getAuthorInfo($article)
    {
        foreach ($article->getAuthors() as $author) {
            return array(
                'author_name' => $author->getFullName(),
                'author_profile_url' => null,
                'short_bio' => $author->getBiography(),
                'author_image_url' => $author->getImage()->getPath(),
            );
        }
    }

    /**
     * Get debate stages
     *
     * @param Newscoop\Entity\Article $debate
     * @return array
     */
    protected function getStages(Article $debate)
    {
        $stages = array();
        for ($i = 1; $i <= 3; $i++) {
            list($pro, $con) = $this->_helper->service('debate')->getVotes($debate, $i);
            $stages[] = array(
                'date' => $this->getArticleField($debate, $this->stages[$i]),
                'pro_percent' => $pro,
                'con_percent' => $con,
                'article_url' => $this->getWebsiteUrl($debate) . '?stage=' . $i,
            );
        }

        return $stages;
    }

    /**
     * Get debate website url
     *
     * @param Newscoop\Entity\Article $debate
     * @return string
     */
    protected function getWebsiteUrl(Article $debate)
    {
        $url = parent::getWebsiteUrl($debate);
        $pieces = explode($debate->getNumber(), $url);
        return $pieces[0];
    }
}
