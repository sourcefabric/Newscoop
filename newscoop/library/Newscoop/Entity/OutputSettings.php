<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;

/**
 * Provides the settings for an output in relation with the theme resources.
 *
 * @ORM\MappedSuperclass
 */
class OutputSettings extends AbstractEntity
{

    /**
	 * Provides the class name as a constant.
	 */
	const NAME = __CLASS__;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Output")
	 * @ORM\JoinColumn(name="fk_output_id", referencedColumnName="id", nullable=FALSE)
	 * @var Newscoop\Entity\Output
	 */
	protected $output;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @ORM\JoinColumn(name="fk_front_page_id", referencedColumnName="id")
	 * @var Newscoop\Entity\Resource
	 */
	protected $frontPage;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @ORM\JoinColumn(name="fk_section_page_id", referencedColumnName="id", nullable=TRUE)
	 * @var Newscoop\Entity\Resource
	 */
	protected $sectionPage;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @ORM\JoinColumn(name="fk_article_page_id", referencedColumnName="id", nullable=TRUE)
	 * @var Newscoop\Entity\Resource
	 */
	protected $articlePage;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @ORM\JoinColumn(name="fk_error_page_id", referencedColumnName="id", nullable=TRUE)
	 * @var Newscoop\Entity\Resource
	 */
	protected $errorPage;

	/* --------------------------------------------------------------- */

	/**
	 * Provides the output that is the owner of this settings.
	 *
	 * @return Output
	 *		The output that is the owner of this settings.
	 */
	function getOutput()
	{
		return $this->output;
	}

	/**
	 * Set the output that is the owner of this settings.
	 *
	 * @param Output $output
	 *		The output that is the owner of this settings, must not be null or empty.
	 *
	 * @return OutputSettings
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
	 * @return Resource
	 *		The front page template resource.
	 */
	function getFrontPage()
	{
		return $this->frontPage;
	}

	/**
	 * Set the front page template resource.
	 *
	 * @param Resource $frontPage
	 *		The front page template resource, must not be null or empty.
	 *
	 * @return OutputSettings
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
	 * @return Resource
	 *		The section page template resource.
	 */
	function getSectionPage()
	{
		return $this->sectionPage;
	}

	/**
	 * Set the section page template resource.
	 *
	 * @param Resource $sectionPage
	 *		The section page template resource, must not be null or empty.
	 *
	 * @return OutputSettings
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
	 * @return Resource
	 *		The article page template resource.
	 */
	function getArticlePage()
	{
		return $this->articlePage;
	}

	/**
	 * Set the article page template resource.
	 *
	 * @param Resource $articlePage
	 *		The article page template resource, must not be null or empty.
	 *
	 * @return OutputSettings
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
	 * @return Resource
	 *		The article page template resource.
	 */
	function getErrorPage()
	{
		return $this->errorPage;
	}

	/**
	 * Set the error page template resource.
	 *
	 * @param Resource $errorPage
	 *		The error page template resource, must not be null or empty.
	 *
	 * @return OutputSettings
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
