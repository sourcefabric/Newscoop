<?php
/**
 * @package Newscoop
 * @author Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2015 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Output;

use Doctrine\ORM\Mapping AS ORM;
use Newscoop\Entity\AbstractEntity;

/**
 * Provides the settings for the output for a publication.
 *
 * @ORM\Entity
 * @ORM\Table(name="output_publication", uniqueConstraints={@ORM\UniqueConstraint(name="publication_language", columns={"fk_publication_id", "fk_langyage_id"})})
 */
class OutputSettingsPublication extends AbstractEntity
{
    /**
	 * Provides the class name as a constant.
	 */
	const NAME_1 = __CLASS__;

    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Output")
     * @ORM\JoinColumn(name="fk_output_id", referencedColumnName="id", nullable=FALSE)
     * @var Newscoop\Entity\Output
     */
    protected $output;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Publication", inversedBy="outputSettingsPublication")
	 * @ORM\JoinColumn(name="fk_publication_id", referencedColumnName="Id")
	 * @var Newscoop\Entity\Publication
	 */
	protected $publication;

	/**
	 * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Language")
	 * @ORM\JoinColumn(name="fk_language_id", referencedColumnName="Id")
	 * @var Newscoop\Entity\Language
	 */
	protected $language;


    /**
     * @ORM\ManyToOne(targetEntity="Newscoop\Entity\Resource")
     * @ORM\JoinColumn(name="fk_theme_path_id", referencedColumnName="id")
     * @var Newscoop\Entity\Resource
     */
    protected $themePath;

    /**
     * Getter for output
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Setter for output
     *
     * @param mixed $output Value to set
     *
     * @return self
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Getter for publication
     *
     * @return \Newscoop\Entity\Publication
     */
    public function getPublication()
    {
        return $this->publication;
    }

    /**
     * Setter for publication
     *
     * @param \Newscoop\Entity\Publication $publication Value to set
     *
     * @return self
     */
    public function setPublication($publication)
    {
        $this->publication = $publication;

        return $this;
    }

    /**
     * Getter for language
     *
     * @return \Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Setter for language
     *
     * @param \Newscoop\Entity\Language $language Value to set
     *
     * @return self
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Provides the path of the theme associated.
     *
     * @return Newscoop\Entity\Resource|null
     *      The path of the theme.
     */
    public function getThemePath()
    {
        return $this->themePath;
    }

    /**
     * Set the path of the theme associated.
     *
     * @param Newscoop\Entity\Resource|null $themePath
     *      The path of the theme, can be empty.
     *
     * @return Newscoop\Entity\PublicationTheme
     *      This object for chaining purposes.
     */
    public function setThemePath($themePath)
    {
        $this->themePath = $themePath;

        return $this;
    }
}
