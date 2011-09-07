<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Ingest\Feed;

/**
 * @Acl(ignore=1)
 */
class Admin_IngestController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\IngestService */
    private $service;

    public function init()
    {
        $this->service = $this->_helper->service('ingest');
    }

    public function indexAction()
    {
        $this->view->feeds = $this->service->getFeeds();
        $this->view->entries = $this->service->findBy(array(), array(), 25, 0);
    }
}
