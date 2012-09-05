<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Output;

use Newscoop\Entity\Section;
use Newscoop\Entity\OutputSettings;
use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;
use Newscoop\Entity\Theme;

/**
 * Provides the settings for an output for a section.
 *
 * @Entity
 * @Table(name="output_section")
 */
class OutputSettingsSection extends OutputSettings
{
    /**
     * Provides the class name as a constant.
     */
    const NAME_1 = __CLASS__;

    /**
     * @ManyToOne(targetEntity="Newscoop\Entity\Section")
     * @JoinColumn(name="fk_section_id", referencedColumnName="id")
     * @var Newscoop\Entity\Section
     */
    private $section;

    /* --------------------------------------------------------------- */

    /**
     * Provides the section that is the owner of this settings.
     *
     * @return Newscoop\Entity\Section
     * 		The the section that is the owner of this settings.
     */
    public function getSection()
    {
        return $this->section;
    }

    /**
     * Set the section that is the owner of this settings.
     *
     * @param Newscoop\Entity\Section $section
     * 		The section that is the owner of this settings, must not be null or empty.
     *
     * @return Newscoop\Entity\OutputSettingsSection
     * 		This object for chaining purposes.
     */
    public function setSection(Section $section)
    {
        Validation::notEmpty($section, 'section');
        $this->section = $section;
        return $this;
    }

	/* --------------------------------------------------------------- */
	
	/**
	 * Copies the cvcontent from this object to the provided object.
	 */
	function copyTo($outputSetting)
	{
		parent::copyTo($outputSetting);
		$outputSetting->setSection($this->getSection());
	}
}