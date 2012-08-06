<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

require_once __DIR__ . '/AbstractController.php';

/**
 * Api Debates service controller.
 */
class Api_DebatesController extends AbstractController
{
    const PUBLICATION = 1;
    const LANGUAGE = 5;
    const SECTION = 81;
    const IMAGE_RENDITION = 'dossierteaser';

    /** @var Zend_Controller_Request_Http */
    private $request;

    private $answers = array(
        'yes' => 1,
        'no' => 2,
    );

    private $rank = 1;

    /**
     * Init controller.
     */
    public function init()
    {
        global $Campsite;

        $this->_helper->layout->disableLayout();
        $this->request = $this->getRequest();
        $this->url = $Campsite['WEBSITE_URL'];
    }

    /**
     * Default action controller.
     */
    public function indexAction()
    {
        $this->_forward('list');
    }

    /**
     * Returns list of debates.
     *
     * @return json
     */
    public function listAction()
    {
        $this->getHelper('contextSwitch')->addActionContext('list', 'json')->initContext();
        $response = array();

        $articles = $this->_helper->service('article')->
            findBy(array('type' => 'deb_moderator', 'publication' => self::PUBLICATION, 'section' => self::SECTION));

        foreach ($articles as $article) {
            $votes = $this->getVotes($article);
            $response[] = array(
                'debate_id' => $article->getNumber(),
                'url' => $this->view->serverUrl($this->view->url(array(
                    'module' => 'api',
                    'controller' => 'debates',
                    'action' => 'item',
                ), 'default') . $this->getApiQueryString(array(
                    'debate_id' => $article->getNumber(),
                ))),
                'title' => $article->getName(),
                'image_url' => $this->getImageUrl($article, self::IMAGE_RENDITION),
                'total_pro_votes' => $votes['yes'],
                'total_con_votes' => $votes['no'],
                'comment_count' => $this->getCommentsCount($article),
                'published' => $this->formatDate($this->getPublishDate($article)),
                'year' => (int) $this->getPublishDate($article)->format('Y'),
                'month' => (int) $this->getPublishDate($article)->format('n'),
                'week' => (int) $this->getPublishDate($article)->format('W'),
                'rank' => $this->rank++,
            );
        }

        $this->_helper->json($response);
    }

    /**
     * Returns a single debate.
     *
     * @return json
     */
    public function itemAction()
    {
        /** @todo */
    }

    /**
     * Gets number of votes for the given debate.
     *
     * @param Newscoop\Entity\Article $article
     * @return array
     */
    private function getVotes($article)
    {
        $debate = \DebateArticle::getAssignments(null, self::LANGUAGE, $article->getNumber(), 0, 1);
        if (empty($debate)) {
            break;
        }

        $votes = array('yes' => 0, 'no' => 0);
        foreach($this->answers as $name => $id) {
            $answer = new \DebateAnswer(self::LANGUAGE, $debate[0]->getDebateNumber(), $id);
            $votes[$name] = (int) $answer->getProperty('nr_of_votes');
        }

        return $votes;
    }

    /**
     * Gets the debate publish date as DateTime object.
     *
     * @param Newscoop\Entity\Article $article
     * @return DateTime
     */
    private function getPublishDate($article)
    {
        $date = $article->getData('date_opening');
        return new DateTime($date);
    }
}
