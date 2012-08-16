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
    const NOT_FOUND = 'Not found';
    const NOT_FOUND_CODE = 404;

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
        $this->assertIsSubscriber();

        if (!$this->_getParam('id') || !is_numeric($this->_getParam('id'))) {
            $this->sendError(self::NOT_FOUND, self::NOT_FOUND_CODE);
        }

        $this->sendZip($this->service->getArticleZipPath($this->_getParam('id'), $this->isRetinaClient()));
    }

    public function issuesAction()
    {
        $issue = $this->_helper->service('mobile.issue')->find($this->_getParam('id'));
        if (!$issue) {
            $this->sendError(self::NOT_FOUND, self::NOT_FOUND_CODE);
        }

        if ($issue->getNumber() == $this->_helper->service('mobile.issue')->getCurrentIssueId()) {
            $this->assertIsSubscriber();
        }

        $this->sendZip($this->service->getIssueZipPath($issue, $this->isRetinaClient()));
    }

    /**
     * Send zip file to browser
     *
     * @param string $zip
     * @return void
     */
    private function sendZip($zip)
    {
        if (!file_exists($zip)) {
            $this->sendError(self::NOT_FOUND, self::NOT_FOUND_CODE);
        }

        $this->getResponse()->setHeader('Content-Type', 'application/zip', true);
        $this->getResponse()->setHeader('Content-Disposition', sprintf('attachment; filename=%s', basename($zip)));
        $this->getResponse()->setHeader('Content-Length', filesize($zip));
        $this->getResponse()->sendHeaders();

        readfile($zip);
        exit;
    }
}
