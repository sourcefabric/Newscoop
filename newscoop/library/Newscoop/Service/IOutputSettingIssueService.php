<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Output;
use Newscoop\Entity\Issue;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Newscoop\Service\IEntityService;


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
	 * Provides the Output that has the provided name.
	 *
	 * @param Issue $issue
	 *		The issue to be searched, not null, not empty.
	 *
	 * @return Newscoop\Entity\Output
	 *		The Output, null if no Output could be found for the provided name.
	 */
	function findByIssue($issue);

        /**
         * Update
         */
        //function update(OutputSettingsIssue $outputSettingsIssue);

        function insert(OutputSettingsIssue $outputSettingsIssue);

        //function delete(OutputSettingsIssue $outputSettingsIssue);
}