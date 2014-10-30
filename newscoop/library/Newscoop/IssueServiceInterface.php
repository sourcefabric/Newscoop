<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric z.ú.
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Symfony\Component\HttpFoundation\Request;

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
}
