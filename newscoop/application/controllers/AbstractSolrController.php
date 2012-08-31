<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
abstract class AbstractSolrController extends Zend_Controller_Action
{
    const LIMIT = 12;

    /**
     * @var array
     */
    protected $dates = array(
        '24h' => '[NOW-1DAY/HOUR TO *]',
        '1d' => '[NOW/DAY TO *]',
        '2d' => '[NOW-1DAY/DAY TO NOW/DAY]',
        '7d' => '[NOW-7DAY/DAY TO *]',
        '1y' => '[NOW-1YEAR/DAY TO *]',
    );

    /**
     * @var array
     */
    protected $types = array(
        'article' => array('news', 'newswire'),
        'dossier' => 'dossier',
        'blog' => 'blog',
        'comment' => 'comment',
        'link' => 'link',
        'event' => 'event',
        'user' => 'user',
    );

    public function init()
    {
        $this->_helper->contextSwitch
            ->addActionContext('index', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        try {
            $client = $this->_helper->service('solr.client.select');
            $client->setParameterGet($this->buildSolrParams());
            $response = $client->request();
        } catch (\Exception $e) {
            $this->_forward('error', 'search', 'default');
            return;
        }

        if (!$response->isSuccessful()) {
            $this->_forward('error', 'search', 'default');
            return;
        }

        if ($this->_helper->contextSwitch->getCurrentContext() === 'json') {
            $this->_helper->json($this->decodeSolrResponse($response));
            return;
        }

        $this->view->result = $this->decodeSolrResponse($response);

        if ($this->_helper->contextSwitch->getCurrentContext() === 'xml') {
            $this->getResponse()->setHeader('Content-Type', 'application/rss-xml', true);
            $this->render('xml');
        }
    }

    /**
     * Build solr params array
     *
     * @return array
     */
    protected function buildSolrParams()
    {
        return array(
            'wt' => 'json',
            'rows' => self::LIMIT,
            'start' => max(0, (int) $this->_getParam('start')),
        );
    }

    /**
     * Build solr date param
     *
     * @return string
     */
    protected function buildSolrDateParam()
    {
        $date = $this->_getParam('date', false);
        if (!$date) {
            return;
        }

        if (array_key_exists($date, $this->dates)) {
            return sprintf('published:%s', $this->dates[$date]);
        }

        try {
            list($from, $to) = explode(',', $date, 2);
            $fromDate = empty($from) ? null : new \DateTime($from);
            $toDate = empty($to) ? null : new \DateTime($to);
        } catch (\Exception $e) {
            return;
        }

        return sprintf('published:[%s TO %s]',
            $fromDate === null ? '*' : $fromDate->format('Y-m-d\TH:i:s\Z') . '/DAY',
            $toDate === null ? '*' : $toDate->format('Y-m-d\TH:i:s\Z') . '/DAY');
    }

    /**
     * Decode solr response
     *
     * @return array
     */
    protected function decodeSolrResponse(\Zend_Http_Response $response)
    {
        $decoded = json_decode($response->getBody(), true);
        $decoded['responseHeader']['params']['q'] = $this->_getParam('q'); // this might be modified, keep users query
        $decoded['responseHeader']['params']['date'] = $this->_getParam('date');
        $decoded['responseHeader']['params']['type'] = $this->_getParam('type');
        $decoded['responseHeader']['params']['source'] = $this->_getParam('source');
        $decoded['responseHeader']['params']['section'] = $this->_getParam('section');
        $decoded['responseHeader']['params']['sort'] = $this->_getParam('sort');
        $decoded['responseHeader']['params']['topic'] = array_filter(explode(',', $this->_getParam('topic', '')));

        $decoded['responseHeader']['params']['q_topic'] = null;
        if ($this->_getParam('q') !== '') {
            $topic = new Topic($this->_getParam('q') . ':de');
            if ($topic->exists()) {
                $decoded['responseHeader']['params']['q_topic'] = $topic->getName(5);
            }
        }

        return $decoded;
    }
}
