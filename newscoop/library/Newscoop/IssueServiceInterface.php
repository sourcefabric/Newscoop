<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Symfony\Component\HttpFoundation\Request;
use Newscoop\Entity\Issue;

/**
 * Issue service interface
 */
interface IssueServiceInterface
{
    /**
     * Issue resolver
     *
     * @param Request $request Request
     *
     * @return Issue|void Returns current issue or nothing
     */
    public function issueResolver(Request $request);

    /**
     * Get issue meta data
     *
     * @return array Issue meta data
     */
    public function getIssueMetadata();

    /**
     * Get Issue object
     *
     * @return Issue Issue entity object
     */
    public function getIssue();

    /**
     * Set Issue object
     *
     * @param Issue $issue Issue entity object
     * @return Services\IssueService
     */
    public function setIssue(Issue $issue);

    /**
     * Get latest published issue from current publication
     *
     * @return Issue|null Returns Issue object or null
     */
    public function getLatestPublishedIssue();
}
