<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;
use Newscoop\Entity\Theme;

/**
 * Provides the settings for an output in relation with the theme resources.
 *
 * @MappedSuperclass
 */
class OutputSettings extends Entity
{

    	/**
	 * Provides the class name as a constant.
	 */
	const NAME = __CLASS__;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Output")
	 * @JoinColumn(name="fk_output_id", referencedColumnName="id", nullable=FALSE)
	 * @var Newscoop\Entity\Output
	 */
	private $output;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @JoinColumn(name="fk_front_page_id", referencedColumnName="id")
	 * @var Newscoop\Entity\Resource
	 */
	private $frontPage;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @JoinColumn(name="fk_section_page_id", referencedColumnName="id", nullable=TRUE)
	 * @var Newscoop\Entity\Resource
	 */
	private $sectionPage;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @JoinColumn(name="fk_article_page_id", referencedColumnName="id", nullable=TRUE)
	 * @var Newscoop\Entity\Resource
	 */
	private $articlePage;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @JoinColumn(name="fk_error_page_id", referencedColumnName="id", nullable=TRUE)
	 * @var Newscoop\Entity\Resource
	 */
	private $errorPage;

	/* --------------------------------------------------------------- */

	/**
	 * Provides the output that is the owner of this settings.
	 *
	 * @return Newscoop\Entity\Output
	 *		The output that is the owner of this settings.
	 */
	function getOutput()
	{
		return $this->output;
	}

	/**
	 * Set the output that is the owner of this settings.
	 *
	 * @param Newscoop\Entity\Output $output
	 *		The output that is the owner of this settings, must not be null or empty.
	 *
	 * @return Newscoop\Entity\OutputSetting
	 *		This object for chaining purposes.
	 */
	function setOutput(Output $output)
	{
		Validation::notEmpty($output, 'output');
		$this->output = $output;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the front page template resource.
	 *
	 * @return Newscoop\Entity\Resource
	 *		The front page template resource.
	 */
	function getFrontPage()
	{
		return $this->frontPage;
	}

	/**
	 * Set the front page template resource.
	 *
	 * @param Newscoop\Entity\Resource $frontPage
	 *		The front page template resource, must not be null or empty.
	 *
	 * @return Newscoop\Entity\OutputSetting
	 *		This object for chaining purposes.
	 */
	function setFrontPage(Resource $frontPage = null)
	{
		$this->frontPage = $frontPage;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the section page template resource.
	 *
	 * @return Newscoop\Entity\Resource
	 *		The section page template resource.
	 */
	function getSectionPage()
	{
		return $this->sectionPage;
	}

	/**
	 * Set the section page template resource.
	 *
	 * @param Newscoop\Entity\Resource $sectionPage
	 *		The section page template resource, must not be null or empty.
	 *
	 * @return Newscoop\Entity\OutputSetting
	 *		This object for chaining purposes.
	 */
	function setSectionPage(Resource $sectionPage = null)
	{
		$this->sectionPage = $sectionPage;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the article page template resource.
	 *
	 * @return Newscoop\Entity\Resource
	 *		The article page template resource.
	 */
	function getArticlePage()
	{
		return $this->articlePage;
	}

	/**
	 * Set the article page template resource.
	 *
	 * @param Newscoop\Entity\Resource $articlePage
	 *		The article page template resource, must not be null or empty.
	 *
	 * @return Newscoop\Entity\OutputSetting
	 *		This object for chaining purposes.
	 */
	function setArticlePage(Resource $articlePage = null)
	{
		$this->articlePage = $articlePage;
		return $this;
	}

	/* --------------------------------------------------------------- */

	/**
	 * Provides the article page template resource.
	 *
	 * @return Newscoop\Entity\Resource
	 *		The article page template resource.
	 */
	function getErrorPage()
	{
		return $this->errorPage;
	}

	/**
	 * Set the error page template resource.
	 *
	 * @param Newscoop\Entity\Resource $errorPage
	 *		The error page template resource, must not be null or empty.
	 *
	 * @return Newscoop\Entity\OutputSetting
	 *		This object for chaining purposes.
	 */
	function setErrorPage(Resource $errorPage = null)
	{
		$this->errorPage = $errorPage;
		return $this;
	}

	/* --------------------------------------------------------------- */
	
	/**
	 * Copies the cvcontent from this object to the provided object.
	 */
	function copyTo($outputSetting)
	{
		$outputSetting->setOutput($this->getOutput());
		$outputSetting->setFrontPage($this->getFrontPage());
		$outputSetting->setSectionPage($this->getSectionPage());
		$outputSetting->setArticlePage($this->getArticlePage());
		$outputSetting->setErrorPage($this->getErrorPage());
	}

}