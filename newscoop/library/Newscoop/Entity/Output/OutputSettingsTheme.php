<?php

/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Output;

use Newscoop\Entity\Resource;
use Newscoop\Entity\Publication;
use Newscoop\Entity\PublicationTheme;
use Newscoop\Entity\OutputSettings;
use Newscoop\Utils\Validation;
use Newscoop\Entity\Entity;
use Newscoop\Entity\Theme;

/**
 * Provides the settings for an output for a theme.
 *
 * @Entity
 * @Table(name="output_theme", uniqueConstraints={@UniqueConstraint(name="publication_themes_idx", columns={"fk_output_id", "fk_publication_id", "fk_theme_path_id"})})
 */
class OutputSettingsTheme extends OutputSettings
{

	/**
	 * Provides the class name as a constant.
	 */
	const NAME = __CLASS__;

	/* --------------------------------------------------------------- */

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Publication")
	 * @JoinColumn(name="fk_publication_id", referencedColumnName="Id")
	 *  @var Newscoop\Entity\Publication
	 */
	private $publication;

	/**
	 * @ManyToOne(targetEntity="Newscoop\Entity\Resource")
	 * @JoinColumn(name="fk_theme_path_id", referencedColumnName="id")
	 * @var Newscoop\Entity\Resource
	 */
	private $themePath;


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
	 * @param Newscoop\Entity\Publication $publication
	 *		The publication that owns the theme.
	 *
	 * @return Newscoop\Entity\PublicationTheme
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

}