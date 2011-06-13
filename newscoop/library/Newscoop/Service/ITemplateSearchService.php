<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Service;

use Newscoop\Entity\Issue;
use Newscoop\Entity\Section;
use Newscoop\Entity\Output;

/**
 * Provides the services for Searching a template.
 */
interface ITemplateSearchService
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /* --------------------------------------------------------------- */

    /**
     * Get the page for front page
     *      to be used as a template.
     *
     * @param Issue|Int $issue
     *      The issue object or the id of the issue for whom the template is needed.
     * @param Output|int|string $output
     *      The object Output, the id or the Name of the Output for whom the template is needed.
     *
     * @return string
     *      The full path of the template.
     */
    function getFrontPage($issue, $output);

    /**
     * Get the page for error
     *      to be used as a template.
     *
     * @param Issue|Int $issue
     *      The issue object or the id of the issue for whom the template is needed.
     * @param Output|int|string $output
     *      The object Output, the id or the Name of the Output for whom the template is needed.
     *
     * @return string
     *      The full path of the template.
     */
    function getErrorPage($issue, $output);

    /**
     * Get the page for section
     *      to be used as a template.
     *
     * @param Section|Int $section
     *      The section object or the id of the issue for whom the template is needed.
     * @param Output|int|string $output
     *      The object Output, the id or the Name of the Output for whom the template is needed.
     *
     *
     * @return string
     *      The full path of the template.
     */
    function getSectionPage($section, $output);

    /**
     * Get the page for article
     *      to be used as a template.
     *
     * @param Section|Int $section
     *      The section object or the id of the issue for whom the template is needed.
     * @param Output|int|string $output
     *      The object Output, the id or the Name of the Output for whom the template is needed.
     *
     * @return string
     *      The full path of the template.
     */
    function getArticlePage($section, $output);
}