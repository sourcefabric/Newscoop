<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Section;
use Newscoop\Entity\Output\OutputSettingsSection;
use Newscoop\Service\IEntityBaseService;

/**
 * Provides the services for the Output Setting.
 */
interface IOutputSettingSectionService extends IEntityBaseService
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /* --------------------------------------------------------------- */

    /**
     * Provides the Output Settings that has the provided Section and Output.
     *
     * @param Section|int $section
     * 		The section to be searched, not null, not empty.
     * @param Output|string $output
     * 		The output to be searched, not null, not empty.
     *
     * @return Newscoop\Entity\OutputSettingsSection
     * 		The Output Setting, empty array if no Output Setting could be found for the provided section.
     */
    function findBySectionAndOutput($section, $output);

    /**
     * Provides the Output Settings that has the provided Section.
     *
     * @param Section|int $section
     * 		The section to be searched, not null, not empty.
     *
     * @return Newscoop\Entity\OutputSettingsSection
     * 		The Output Setting, empty array if no Output Setting could be found for the provided section.
     */
    function findBySection($section);

    /**
     * Update an ouput setting section
     *
     * @param OutputSettingsSection $outputSettingsSection
     */
    function update(OutputSettingsSection $outputSettingsSection);

    /**
     * Inserts an ouput setting section
     *
     * @param OutputSettingsSection $outputSettingsSection
     */
    function insert(OutputSettingsSection $outputSettingsSection);

    /**
     * Delete an ouput setting section
     *
     * @param OutputSettingsSection $outputSettingsSection
     */
    function delete(OutputSettingsSection $outputSettingsSection);
}