<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Article;

require_once __DIR__ . '/AbstractController.php';

/**
 * Offline Service
 */
class Api_OfflineController extends AbstractController
{
    /**
     * @var Tageswoche\Mobile\OfflineIssueService
     */
    private $service;

    public function init()
    {
        $this->service = $this->_helper->service('mobile.issue.offline');
    }

    public function articlesAction()
    {
        var_dump($this->getRequest()->getParams());
        exit;
    }

    public function issuesAction()
    {
        $issue = $this->_helper->service('mobile.issue')->find($this->_getParam('id'));
        if (!$issue) {
            $this->sendError("Issue not found.", 404);
        }

        if (!$this->service->isIssueGenerated($issue)) {
            $this->sendError("Issue not generated.", 503);
        }

        $this->getResponse()->setHeader('Content-Type', 'application/zip', true);
        $this->getResponse()->setHeader('Content-Disposition', sprintf('attachment; filename=issue-%d.zip', $issue->getNumber()));
        $this->getResponse()->setHeader('Content-Length', filesize($this->service->getIssuePath($issue)));
        $this->getResponse()->sendHeaders();

        readfile($this->service->getIssuePath($issue));
        exit;
        //$this->getResponse()->setBody(file_get_contents($this->service->getIssuePath($issue)));
    }
}
