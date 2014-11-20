<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Output;

use Doctrine\ORM\Mapping as ORM;
use Newscoop\Entity\Resource;
use Newscoop\Entity\Publication;
use Newscoop\Entity\OutputSettings;
use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;

/**
 * Provides the settings for an output for a theme.
 *
 * @ORM\Entity
 * @ORM\Table(name="output_theme", uniqueConstraints={@ORM\UniqueConstraint(name="publication_themes_idx", columns={"fk_output_id", "fk_publication_id", "fk_theme_path_id"})})
 */
class OutputSettingsTheme extends OutputSettings
{

	/**
	 * Provides the class name as a constant.
	 */
	const NAME = __CLASS__;

	/* --------------------------------------------------------------- */

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication")
	 * @ORM\JoinColumn(name="fk_publication_id", referencedColumnName="Id")
	 *  @var Newscoop\Entity\Publication
	 */
	protected $publication;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @ORM\JoinColumn(name="fk_theme_path_id", referencedColumnName="id")
	 * @var Newscoop\Entity\Resource
	 */
	protected $themePath;


	/* --------------------------------------------------------------- */

	/**
	 * Provides the publication that owns the theme.
	 *
	 * @return Newscoop\Entity\Publication
	 *		The publication that owns the theme.
	 */
	public function getPublication()
	{
		return $this->publication;
	}

	/**
	 * Set the publication that owns the theme.
	 *
	 * @param Publication $publication
	 *		The publication that owns the theme.
	 *
	 * @return OutputSettingsTheme
	 *		This object for chaining purposes.
	 */
	public function setPublication(Publication $publication)
	{
		Validation::notEmpty($publication, 'publication');
		$this->publication = $publication;
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
	 * @param Resource $themePath
	 *		The path of the theme, must not be null or empty.
	 *
	 * @return OutputSettingsTheme
	 *		This object for chaining purposes.
	 */
	public function setThemePath(Resource $themePath)
	{
		Validation::notEmpty($themePath, 'themePath');
		$this->themePath = $themePath;
		return $this;
	}

}