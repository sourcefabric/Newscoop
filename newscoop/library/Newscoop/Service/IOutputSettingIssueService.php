<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Issue;
use Newscoop\Entity\Output;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Newscoop\Service\IEntityBaseService;

/**
 * Provides the services for the Outputs.
 */
interface IOutputSettingIssueService extends IEntityBaseService
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /* --------------------------------------------------------------- */

    /**
     * Provides the Output Settings Issue for the provided issue
     *
     * @param Issue|int $issue
     * 		The issue to be searched, not null, not empty.
     *
     * @return array Newscoop\Entity\Output\OutputSettingsIssue
     * 		The Output Setting, empty array if no Output Setting could be found for the provided issue.
     */
    function findByIssue($issue);

    /**
     * Provides the Output Settings Issue for the provided issue and output
     *
     * @param Issue|int $issue
     * 		The issue to be searched, not null, not empty.

     * @param Output|string $output
     * 		The output to be searched, not null, not empty.
     *
     * @return array Newscoop\Entity\Output\OutputSettingsIssue
     * 		The Output Setting, empty array if no Output Setting could be found for the provided issue.
     */
    function findByIssueAndOutput($issue, $output);
    
    /**
     * Checks if the provided theme is used by any issue at this point.
     * @param Theme|str $theme
     * 		The theme or path to be checked if used.
     */
    function isThemeUsed($theme);

    /**
     * Update an ouput setting issue
     *
     * @param OutputSettingsIssue $outputSettingsIssue
     */
    function update(OutputSettingsIssue $outputSettingsIssue);

    /**
     * Inserts an ouput setting issue
     *
     * @param OutputSettingsIssue $outputSettingsIssue
     */
    function insert(OutputSettingsIssue $outputSettingsIssue);

    /**
     * Delete an ouput setting issue
     *
     * @param OutputSettingsIssue $outputSettingsIssue
     */
    function delete(OutputSettingsIssue $outputSettingsIssue);
}