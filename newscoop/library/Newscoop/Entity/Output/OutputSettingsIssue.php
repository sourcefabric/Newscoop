<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Output;

use Newscoop\Entity\Issue;
use Newscoop\Entity\OutputSettings;
use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;
use Newscoop\Entity\Theme;
use Newscoop\Entity\Resource;
/**
 * Provides the settings for an output for an issue.
 *
 * @Entity
 * @Table(name="output_issue", uniqueConstraints={@UniqueConstraint(name="publication_themes_idx", columns={"fk_output_id", "fk_issue_id"})})
 */
class OutputSettingsIssue extends OutputSettings
{
        /**
	 * Provides the class name as a constant.
	 */
	const NAME_1 = __CLASS__;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Issue")
	 * @JoinColumn(name="fk_issue_id", referencedColumnName="id")
	 * @var Newscoop\Entity\Issue
	 */
	private $issue;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @JoinColumn(name="fk_theme_path_id", referencedColumnName="id")
	 * @var Newscoop\Entity\Resource
	 */
	private $themePath;

	/* --------------------------------------------------------------- */

	/**
	 * Provides the issue that is the owner of this settings.
	 *
	 * @return Newscoop\Entity\Issue
	 *		The the issue that is the owner of this settings.
	 */
	public function getIssue()
	{
		return $this->issue;
	}

	/**
	 * Set the issue that is the owner of this settings.
	 *
	 * @param Newscoop\Entity\Issue $issue
	 *		The issue that is the owner of this settings, must not be null or empty.
	 *
	 * @return Newscoop\Entity\OutputSettingsIssue
	 *		This object for chaining purposes.
	 */
	public function setIssue(Issue $issue)
	{
		Validation::notEmpty($issue, 'issue');
		$this->issue = $issue;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the path of the theme associated.
	 *
	 * @return Newscoop\Entity\Resource
	 *		The path of the theme.
	 */
	public function getThemePath()
	{
		return $this->themePath;
	}

	/**
	 * Set the path of the theme associated.
	 *
	 * @param Newscoop\Entity\Resource $themePath
	 *		The path of the theme, must not be null or empty.
	 *
	 * @return Newscoop\Entity\PublicationTheme
	 *		This object for chaining purposes.
	 */
	public function setThemePath(Resource $themePath)
	{
		Validation::notEmpty($themePath, 'themePath');
		$this->themePath = $themePath;
		return $this;
	}
	
	/* --------------------------------------------------------------- */
	
	/**
	 * Copies the cvcontent from this object to the provided object.
	 */
	function copyTo($outputSetting)
	{
		parent::copyTo($outputSetting);
		$outputSetting->setIssue($this->getIssue());
		$outputSetting->setThemePath($this->getThemePath());
	}
}