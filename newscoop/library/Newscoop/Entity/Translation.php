<?php
/**
 * @package Newscoop
 * @copyright 2014 Sourcefabric o.p.s.
 * @author Paweł Mikołąjczuk <pawel.mikolajczuk@sourcefabric.org>
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Snippet entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="Translations", indexes={
 *   @ORM\Index(name="phrase_language_index", columns={"fk_language_id"}),
 * })
 */
class Translation
{
    /**
     * @ORM\Id
     * @ORM\Column(name="phrase_id", type="integer")
     * @var int
     */
    protected $phrase_id;

    /**
     * @ORM\ManyToOne(targetEntity="\Newscoop\Entity\Language")
     * @ORM\JoinColumn(name="fk_language_id", referencedColumnName="Id", nullable=false)
     * @var \Newscoop\Entity\Language
     */
    protected $language;

    /**
     * @ORM\Column(name="translation_text", type="text", nullable=true)
     * @var integer
     */
    protected $translationText;

    /**
     * @param int $phraseId Current phrase id for translations from autoid table.
     */
    public function __construct($phraseId)
    {
        $this->setPhraseId($phraseId);
    }

    /**
     * Gets the value of language.
     *
     * @return \Newscoop\Entity\Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Sets the value of language.
     *
     * @param \Newscoop\Entity\Language $language the language
     *
     * @return self
     */
    public function setLanguage(\Newscoop\Entity\Language $language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Gets the value of translationText.
     *
     * @return integer
     */
    public function getTranslationText()
    {
        return $this->translationText;
    }

    /**
     * Sets the value of translationText.
     *
     * @param integer $translationText the translation text
     *
     * @return self
     */
    public function setTranslationText($translationText)
    {
        $this->translationText = $translationText;

        return $this;
    }

    /**
     * Return translation text when echo this object
     *
     * @return integer
     */
    public function __toString()
    {
        return $this->translationText;
    }

    /**
     * Gets the value of phrase_id.
     *
     * @return int
     */
    public function getPhraseId()
    {
        return $this->phrase_id;
    }

    /**
     * Sets the value of phrase_id.
     *
     * @param int $phrase_id the phrase_id
     *
     * @return self
     */
    public function setPhraseId($phrase_id)
    {
        $this->phrase_id = $phrase_id;

        return $this;
    }
}
